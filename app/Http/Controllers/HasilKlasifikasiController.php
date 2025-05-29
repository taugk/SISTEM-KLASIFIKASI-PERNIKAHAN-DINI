<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\DataWilayah;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DataPernikahan;
use App\Models\Resiko_Wilayah;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HasilKlasifikasi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HasilKlasifikasiExport;
use App\Helpers\NotificationHelper;

class HasilKlasifikasiController extends Controller
{

    public function map(Request $request)
    {
        try {
            // Base query
            $query = Resiko_Wilayah::with(['wilayah' => function($q) {
                $q->select('id', 'desa', 'kecamatan', 'kabupaten', 'provinsi');
            }])
            ->select('id', 'id_wilayah', 'resiko_wilayah', 'jumlah_pernikahan_dini', 'created_at');

            // Apply filters
            if ($request->filled('filter_provinsi')) {
                $query->whereHas('wilayah', function($q) use ($request) {
                    $q->where('provinsi', strtoupper($request->filter_provinsi));
                });
            }

            if ($request->filled('filter_kabupaten')) {
                $query->whereHas('wilayah', function($q) use ($request) {
                    $q->where('kabupaten', strtoupper($request->filter_kabupaten));
                });
            }

            if ($request->filled('filter_kecamatan')) {
                $query->whereHas('wilayah', function($q) use ($request) {
                    $q->where('kecamatan', strtoupper($request->filter_kecamatan));
                });
            }

            if ($request->filled('filter_resiko')) {
                $query->where('resiko_wilayah', $request->filter_resiko);
            }

            // Get the latest risk assessment for each wilayah
            $resikoData = $query->orderBy('created_at', 'desc')
                          ->get()
                          ->unique('id_wilayah')
                          ->keyBy(function ($item) {
                              return strtolower($item->wilayah->desa ?? '');
                          });

            // Read GeoJSON file
            $geojsonPath = public_path('geojson/map.geojson');
            if (!file_exists($geojsonPath)) {
                \Log::error('GeoJSON file not found at: ' . $geojsonPath);
                return response()->view('errors.custom', [
                    'message' => 'File GeoJSON tidak ditemukan'
                ], 404);
            }

            $geojsonContent = file_get_contents($geojsonPath);
            if (empty($geojsonContent)) {
                \Log::error('GeoJSON file is empty');
                return response()->view('errors.custom', [
                    'message' => 'File GeoJSON kosong'
                ], 404);
            }

            // Decode GeoJSON
            $geojson = json_decode($geojsonContent, true);

            // Add risk data to GeoJSON properties
            foreach ($geojson['features'] as &$feature) {
                $desaName = strtolower($feature['properties']['NAMOBJ'] ?? '');
                if (isset($resikoData[$desaName])) {
                    $resikoWilayah = $resikoData[$desaName];
                    $feature['properties']['resiko_wilayah'] = $resikoWilayah->resiko_wilayah;
                    $feature['properties']['jumlah_pernikahan_dini'] = $resikoWilayah->jumlah_pernikahan_dini;
                } else {
                    $feature['properties']['resiko_wilayah'] = 'tidak tersedia';
                    $feature['properties']['jumlah_pernikahan_dini'] = 0;
                }
            }

            // Get filter options
            $provinsis = DataWilayah::select('provinsi')->distinct()->pluck('provinsi');
            $kabupatens = DataWilayah::select('kabupaten')->distinct()->pluck('kabupaten');
            $kecamatans = DataWilayah::select('kecamatan')->distinct()->pluck('kecamatan');
            $resikoOptions = Resiko_Wilayah::select('resiko_wilayah')->distinct()->pluck('resiko_wilayah');

            return view('dashboard.hasil_klasifikasi.peta.index', [
                'geojson' => json_encode($geojson),
                'provinsis' => $provinsis,
                'kabupatens' => $kabupatens,
                'kecamatans' => $kecamatans,
                'resikoOptions' => $resikoOptions,
                'selectedProvinsi' => $request->filter_provinsi,
                'selectedKabupaten' => $request->filter_kabupaten,
                'selectedKecamatan' => $request->filter_kecamatan,
                'selectedResiko' => $request->filter_resiko
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in map method: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->view('errors.custom', [
                'message' => 'Terjadi kesalahan saat memuat peta: ' . $e->getMessage()
            ], 500);
        }
    }


    public function index(Request $request){
    // Query dasar dengan relasi wilayah
    $query = Resiko_Wilayah::with('wilayah')
        ->orderBy('created_at', 'desc');

    // Filter: nama suami/istri (kalau field ini memang ada di Resiko_Wilayah)
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('nama_suami', 'like', '%' . $request->search . '%')
              ->orWhere('nama_istri', 'like', '%' . $request->search . '%');
        });
    }

    // Filter: kelurahan
    if ($request->filled('filter_kelurahan')) {
        $query->whereHas('wilayah', function ($q) use ($request) {
            $q->where('desa', $request->filter_kelurahan);
        });
    }

    // Filter: tahun
    if ($request->filled('filter_tahun')) {
        $query->where('periode', $request->filter_tahun);
    }

    // Filter: kategori risiko
    if ($request->filled('filter_resiko')) {
        $query->where('resiko_wilayah', $request->filter_resiko);
    }

    // Ambil hasil
    $data = $query->get();

    // Data untuk dropdown filter
    $kelurahans = DataWilayah::select('desa')->distinct()->get();
    $tahun = Resiko_Wilayah::select('periode as tahun')->distinct()->orderByDesc('tahun')->get();
    $kategori = Resiko_Wilayah::select('resiko_wilayah as kategori_pernikahan')->distinct()->get();
    $wilayah = DataWilayah::all();

    return view('dashboard.hasil_klasifikasi.index', compact(
        'data', 'kelurahans', 'tahun', 'kategori', 'wilayah'
    ));
}


public function detail($id)
{
    $data = DataWilayah::with(['wilayah','resiko_wilayah'])->findOrFail($id);

    // Pastikan pernikahan collection tidak null
    $pernikahan = $data->wilayah ?? collect();

    $totalPernikahan = $pernikahan->count();
    $totalPernikahanDini = $pernikahan->where('kategori_pernikahan', 'Pernikahan Dini')->count();

    $rataUsiaSuami = $pernikahan->avg('usia_suami');
    $rataUsiaIstri = $pernikahan->avg('usia_istri');

    return view('dashboard.hasil_klasifikasi.detail_hasil', compact(
        'data', 'totalPernikahan', 'totalPernikahanDini', 'rataUsiaSuami', 'rataUsiaIstri'
    ));
}



public function chart(Request $request)
{
    try {
        // Get filter values
        $tahunTerpilih = $request->input('tahun', date('Y'));
        $wilayahTerpilih = $request->input('wilayah');
        $pendidikanTerpilih = $request->input('pendidikan');
        $statusTerpilih = $request->input('status');

        // Get filter options
        $daftarTahun = DB::table('pernikahan')
            ->selectRaw('DISTINCT YEAR(tanggal_akad) as tahun')
            ->whereNotNull('tanggal_akad')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Add current year and 2025 if not present
        if (!$daftarTahun->contains(date('Y'))) {
            $daftarTahun->prepend(date('Y'));
        }
        if (!$daftarTahun->contains(2025)) {
            $daftarTahun->prepend(2025);
        }
        $daftarTahun = $daftarTahun->sort()->reverse();

        // Get list of wilayah
        $daftarWilayah = DB::table('data_wilayah')
            ->orderBy('desa')
            ->pluck('desa');

        // Get list of pendidikan
        $daftarPendidikan = DB::table('pernikahan')
            ->select('pendidikan_istri')
            ->distinct()
            ->whereNotNull('pendidikan_istri')
            ->orderBy('pendidikan_istri')
            ->pluck('pendidikan_istri');

        // Get list of status
        $daftarStatus = DB::table('pernikahan')
            ->select('status_istri')
            ->distinct()
            ->whereNotNull('status_istri')
            ->orderBy('status_istri')
            ->pluck('status_istri');

        // 1. Pie Chart: Perbandingan Pernikahan Dini vs Normal
        $queryPerbandingan = DB::table('hasil_klasifikasi')
            ->select('kategori_pernikahan', DB::raw('COUNT(*) as total'));

        if ($wilayahTerpilih || $tahunTerpilih) {
            $queryPerbandingan->join('pernikahan', 'hasil_klasifikasi.id_pernikahan', '=', 'pernikahan.id');
            
            if ($wilayahTerpilih) {
                $queryPerbandingan->join('data_wilayah', 'pernikahan.wilayah_id', '=', 'data_wilayah.id')
                                ->where('data_wilayah.desa', $wilayahTerpilih);
            }
            if ($tahunTerpilih) {
                $queryPerbandingan->whereYear('pernikahan.tanggal_akad', $tahunTerpilih);
            }
        }

        $perbandinganPernikahan = $queryPerbandingan->groupBy('kategori_pernikahan')->get();

        // 2. Donut Chart: Distribusi Usia Pernikahan Dini
        $queryUsia = DB::table('pernikahan')
            ->join('hasil_klasifikasi', 'pernikahan.id', '=', 'hasil_klasifikasi.id_pernikahan')
            ->where('hasil_klasifikasi.kategori_pernikahan', 'Pernikahan Dini');

        if ($wilayahTerpilih) {
            $queryUsia->join('data_wilayah', 'pernikahan.wilayah_id', '=', 'data_wilayah.id')
                    ->where('data_wilayah.desa', $wilayahTerpilih);
        }
        if ($tahunTerpilih) {
            $queryUsia->whereYear('pernikahan.tanggal_akad', $tahunTerpilih);
        }

        $distribusiUsia = $queryUsia
            ->select(
                DB::raw('
                    CASE 
                        WHEN usia_istri < 16 THEN "< 16 tahun"
                        WHEN usia_istri BETWEEN 16 AND 18 THEN "16-18 tahun"
                        ELSE "> 18 tahun"
                    END as kelompok_usia
                '),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('kelompok_usia')
            ->orderBy(DB::raw('MIN(usia_istri)'))
            ->get();

        // 3. Polar Area Chart: Distribusi Pendidikan
        $queryPendidikan = DB::table('pernikahan')
            ->join('hasil_klasifikasi', 'pernikahan.id', '=', 'hasil_klasifikasi.id_pernikahan')
            ->where('hasil_klasifikasi.kategori_pernikahan', 'Pernikahan Dini');

        if ($wilayahTerpilih) {
            $queryPendidikan->join('data_wilayah', 'pernikahan.wilayah_id', '=', 'data_wilayah.id')
                        ->where('data_wilayah.desa', $wilayahTerpilih);
        }
        if ($tahunTerpilih) {
            $queryPendidikan->whereYear('pernikahan.tanggal_akad', $tahunTerpilih);
        }
        if ($pendidikanTerpilih) {
            $queryPendidikan->where('pendidikan_istri', $pendidikanTerpilih);
        }

        $distribusiPendidikan = $queryPendidikan
            ->select('pendidikan_istri', DB::raw('COUNT(*) as total'))
            ->groupBy('pendidikan_istri')
            ->orderBy('total', 'desc')
            ->get();

        // 4. Radar Chart: Distribusi Status
        $queryStatus = DB::table('pernikahan')
            ->join('hasil_klasifikasi', 'pernikahan.id', '=', 'hasil_klasifikasi.id_pernikahan')
            ->where('hasil_klasifikasi.kategori_pernikahan', 'Pernikahan Dini');

        if ($wilayahTerpilih) {
            $queryStatus->join('data_wilayah', 'pernikahan.wilayah_id', '=', 'data_wilayah.id')
                    ->where('data_wilayah.desa', $wilayahTerpilih);
        }
        if ($tahunTerpilih) {
            $queryStatus->whereYear('pernikahan.tanggal_akad', $tahunTerpilih);
        }
        if ($statusTerpilih) {
            $queryStatus->where('status_istri', $statusTerpilih);
        }

        $distribusiStatus = $queryStatus
            ->select('status_istri', DB::raw('COUNT(*) as total'))
            ->groupBy('status_istri')
            ->orderBy('total', 'desc')
            ->get();

    return view('dashboard.hasil_klasifikasi.chart.index', [
            'daftarTahun' => $daftarTahun,
            'daftarWilayah' => $daftarWilayah,
            'daftarPendidikan' => $daftarPendidikan,
            'daftarStatus' => $daftarStatus,
            'tahunTerpilih' => $tahunTerpilih,
            'wilayahTerpilih' => $wilayahTerpilih,
            'pendidikanTerpilih' => $pendidikanTerpilih,
            'statusTerpilih' => $statusTerpilih,
            'kategoriLabels' => $perbandinganPernikahan->pluck('kategori_pernikahan'),
            'kategoriData' => $perbandinganPernikahan->pluck('total'),
            'usiaLabels' => $distribusiUsia->pluck('kelompok_usia'),
            'usiaData' => $distribusiUsia->pluck('total'),
            'pendidikanLabels' => $distribusiPendidikan->pluck('pendidikan_istri'),
            'pendidikanData' => $distribusiPendidikan->pluck('total'),
            'statusLabels' => $distribusiStatus->pluck('status_istri'),
            'statusData' => $distribusiStatus->pluck('total')
        ]);
    } catch (\Exception $e) {
        NotificationHelper::error('Gagal memuat data grafik: ' . $e->getMessage());
        return redirect()->back();
    }
}




public function graphView(Request $request)
{
    // Get current year for default selection
    $currentYear = date('Y');
    $tahunTerpilih = $request->input('tahun', $currentYear);
    $wilayahTerpilih = $request->input('wilayah');
    $risikoTerpilih = $request->input('risiko');
    $bulanTerpilih = $request->input('bulan');

    // Get all years from pernikahan table
    $daftarTahun = DB::table('pernikahan')
        ->selectRaw('DISTINCT YEAR(tanggal_akad) as tahun')
        ->whereNotNull('tanggal_akad')
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    // If there's no data yet for current year, add it manually
    if (!$daftarTahun->contains($currentYear)) {
        $daftarTahun->prepend($currentYear);
    }

    // Add 2025 if not present
    if (!$daftarTahun->contains(2025)) {
        $daftarTahun->prepend(2025);
    }

    // Sort years in descending order
    $daftarTahun = $daftarTahun->sort()->reverse();

    // Get list of wilayah for filter
    $daftarWilayah = DB::table('data_wilayah')
        ->orderBy('desa')
        ->pluck('desa');

    // Get list of risiko for filter
    $daftarRisiko = DB::table('resiko_wilayah')
        ->select('resiko_wilayah')
        ->distinct()
        ->orderByRaw("FIELD(resiko_wilayah, 'tinggi', 'sedang', 'rendah')")
        ->pluck('resiko_wilayah');

    // 1. Donut Chart - Pernikahan Dini per Desa
    $queryWilayah = DB::table('pernikahan')
        ->join('hasil_klasifikasi', 'pernikahan.id', '=', 'hasil_klasifikasi.id_pernikahan')
        ->join('data_wilayah', 'pernikahan.wilayah_id', '=', 'data_wilayah.id')
        ->where('hasil_klasifikasi.kategori_pernikahan', 'Pernikahan Dini');

    if ($tahunTerpilih) {
        $queryWilayah->whereYear('pernikahan.tanggal_akad', $tahunTerpilih);
    }
    if ($bulanTerpilih) {
        $queryWilayah->whereMonth('pernikahan.tanggal_akad', $bulanTerpilih);
    }
    if ($wilayahTerpilih) {
        $queryWilayah->where('data_wilayah.desa', $wilayahTerpilih);
    }

    $pernikahanPerWilayah = $queryWilayah
        ->select('data_wilayah.desa', DB::raw('COUNT(*) as total'))
        ->groupBy('data_wilayah.desa')
        ->orderBy('total', 'desc')
        ->get();

    // 2. Bar Chart - Distribusi Risiko
    $queryRisiko = DB::table('resiko_wilayah')
        ->select('resiko_wilayah', DB::raw('COUNT(*) as y'));

    if ($wilayahTerpilih) {
        $queryRisiko->join('data_wilayah', 'resiko_wilayah.id_wilayah', '=', 'data_wilayah.id')
                    ->where('data_wilayah.desa', $wilayahTerpilih);
    }
    if ($risikoTerpilih) {
        $queryRisiko->where('resiko_wilayah', $risikoTerpilih);
    }

    $resikoData = $queryRisiko
        ->groupBy('resiko_wilayah')
        ->orderByRaw("FIELD(resiko_wilayah, 'tinggi', 'sedang', 'rendah')")
        ->get();

    // 3. Line Chart - Tren per Bulan
    $queryBulan = DB::table('pernikahan')
        ->join('hasil_klasifikasi', 'pernikahan.id', '=', 'hasil_klasifikasi.id_pernikahan')
        ->where('hasil_klasifikasi.kategori_pernikahan', 'Pernikahan Dini');

    if ($tahunTerpilih) {
        $queryBulan->whereYear('pernikahan.tanggal_akad', $tahunTerpilih);
    }
    if ($wilayahTerpilih) {
        $queryBulan->join('data_wilayah', 'pernikahan.wilayah_id', '=', 'data_wilayah.id')
                  ->where('data_wilayah.desa', $wilayahTerpilih);
    }

    $bulanData = $queryBulan
        ->select(DB::raw('MONTH(tanggal_akad) as bulan'), DB::raw('COUNT(*) as jumlah'))
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get();

    // Initialize array with zeros for all months
    $bulanCounts = array_fill(0, 12, 0);
    foreach ($bulanData as $data) {
        $bulanCounts[$data->bulan - 1] = $data->jumlah;
    }

    $namaBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    return view('dashboard.hasil_klasifikasi.grafik.index', [
        'daftarTahun' => $daftarTahun,
        'daftarWilayah' => $daftarWilayah,
        'daftarRisiko' => $daftarRisiko,
        'namaBulan' => $namaBulan,
        'tahunTerpilih' => $tahunTerpilih,
        'wilayahTerpilih' => $wilayahTerpilih,
        'risikoTerpilih' => $risikoTerpilih,
        'bulanTerpilih' => $bulanTerpilih,
        'wilayahLabels' => $pernikahanPerWilayah->pluck('desa'),
        'wilayahData' => $pernikahanPerWilayah->pluck('total'),
        'resikoLabels' => $resikoData->pluck('resiko_wilayah'),
        'resikoData' => $resikoData,
        'bulanLabels' => $namaBulan,
        'bulanData' => $bulanCounts
    ]);
}



public function exportExcel(Request $request){
     // Ambil data filter dari request
        $filters = [
            'search'           => $request->input('search'),
            'filter_kelurahan' => $request->input('filter_kelurahan'),
            'filter_tahun'     => $request->input('filter_tahun'),
            'filter_resiko'    => $request->input('filter_resiko'),
        ];

        // Buat nama file dengan timestamp
        $fileName = 'hasil_klasifikasi_' . now()->format('Ymd_His') . '.xlsx';

        // Download file menggunakan class export dan passing filter
        return Excel::download(new HasilKlasifikasiExport($filters), $fileName);
}

public function exportPdf(Request $request)
{
    // Query untuk ambil data dengan relasi ke data_wilayah
    $query = Resiko_Wilayah::with('wilayah')
        ->leftJoin('data_wilayah', 'resiko_wilayah.id_wilayah', '=', 'data_wilayah.id')
        ->orderBy('resiko_wilayah.created_at', 'desc');

    // Filter pencarian nama suami/istri
    if ($request->has('search') && $request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('nama_suami', 'like', '%' . $request->search . '%')
              ->orWhere('nama_istri', 'like', '%' . $request->search . '%');
        });
    }

    // Filter kelurahan
    if ($request->has('filter_kelurahan') && $request->filter_kelurahan) {
        $query->whereHas('wilayah', function ($q) use ($request) {
            $q->where('desa', $request->filter_kelurahan);
        });
    }

    // Filter tahun
    if ($request->has('filter_tahun') && $request->filter_tahun) {
        $query->where('periode', $request->filter_tahun);
    }

    // Filter kategori resiko
    if ($request->has('filter_resiko') && $request->filter_resiko) {
        $query->where('resiko_wilayah', $request->filter_resiko);
    }

    // Ambil data yang difilter
    $data = $query->get()->map(function ($item) {
        return [
            'kelurahan' => $item->wilayah->desa ?? '-',
            'jumlah_pernikahan' => $item->jumlah_pernikahan ?? 0,
            'jumlah_pernikahan_dini' => $item->jumlah_pernikahan_dini ?? 0,
            'resiko_wilayah' => ucfirst($item->resiko_wilayah),
        ];
    });

    // Judul dan deskripsi untuk laporan
    $judul = 'LAPORAN DATA PERNIKAHAN BERDASARKAN RESIKO';
    $keterangan = 'Data yang ditampilkan berdasarkan filter hasil klasifikasi resiko wilayah.';

    // Kolom yang akan ditampilkan
    $kolom = [
        'kelurahan' => 'Kelurahan/Desa',
        'jumlah_pernikahan' => 'Jumlah Pernikahan',
        'jumlah_pernikahan_dini' => 'Jumlah Pernikahan Dini',
        'resiko_wilayah' => 'Resiko Wilayah',
    ];

    // Nama file PDF
    $fileName = 'laporan_klasifikasi_resiko_' . now()->format('Ymd_His') . '.pdf';

    // Render PDF pakai template blade
    $pdf = Pdf::loadView('components.laporan_pdf', compact('data', 'judul', 'keterangan', 'kolom'))
        ->setPaper('A4', 'landscape');

        // stream
         return $pdf->stream($fileName);

    // return $pdf->download($fileName);
}

public function exportCsv(Request $request){
     // Ambil data filter dari request
        $filters = [
            'search'           => $request->input('search'),
            'filter_kelurahan' => $request->input('filter_kelurahan'),
            'filter_tahun'     => $request->input('filter_tahun'),
            'filter_resiko'    => $request->input('filter_resiko'),
        ];

        // Buat nama file dengan timestamp
        $fileName = 'hasil_klasifikasi_' . now()->format('Ymd_His') . '.csv';

        // Download file menggunakan class export dan passing filter
        return Excel::download(new HasilKlasifikasiExport($filters), $fileName);
}

public function store(Request $request)
{
    try {
        DB::beginTransaction();

        // Your existing store logic here
        
        DB::commit();
        NotificationHelper::success('Data berhasil disimpan');
        return redirect()->route('hasil_klasifikasi.index');
    } catch (\Exception $e) {
        DB::rollback();
        NotificationHelper::error('Gagal menyimpan data: ' . $e->getMessage());
        return redirect()->back();
    }
}

public function update(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $hasil = HasilKlasifikasi::findOrFail($id);
        // Your existing update logic here
        
        DB::commit();
        NotificationHelper::success('Data berhasil diperbarui');
        return redirect()->route('hasil_klasifikasi.index');
    } catch (\Exception $e) {
        DB::rollback();
        NotificationHelper::error('Gagal memperbarui data: ' . $e->getMessage());
        return redirect()->back();
    }
}

public function destroy($id)
{
    try {
        DB::beginTransaction();

        $hasil = HasilKlasifikasi::findOrFail($id);
        $hasil->delete();
        
        DB::commit();
        NotificationHelper::success('Data berhasil dihapus');
        return redirect()->route('hasil_klasifikasi.index');
    } catch (\Exception $e) {
        DB::rollback();
        NotificationHelper::error('Gagal menghapus data: ' . $e->getMessage());
        return redirect()->back();
    }
}

}
