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

class HasilKlasifikasiController extends Controller
{

    public function map(Request $request)
    {
        // Ambil data risiko wilayah beserta relasi ke tabel wilayah
        $query = Resiko_Wilayah::with('wilayah')->get();

        // Ubah menjadi array dengan key slug nama desa
        $query = $query->mapWithKeys(function ($item) {
            $slugNama = Str::slug(strtolower($item->wilayah->desa));
            return [$slugNama => $item];
        });

        // Baca file GeoJSON
        $geojsonPath = public_path('geojson/map.geojson');
        $geojson = json_decode(file_get_contents($geojsonPath), true);

        // Loop setiap fitur dan isi data risiko
        foreach ($geojson['features'] as $key => $feature) {
            // Ambil nama kelurahan dari properti NAMOBJ dan slugify
            $namaAsli = $feature['properties']['NAMOBJ'] ?? '';
            $namaKelurahan = Str::slug(strtolower($namaAsli));

            // Cari data berdasarkan slug
            $data = $query[$namaKelurahan] ?? null;

            // Update geojson
            $geojson['features'][$key]['properties']['resiko'] = $data->resiko_wilayah ?? 0;
            $geojson['features'][$key]['properties']['jumlah_pernikahan_dini'] = $data->jumlah_pernikahan_dini ?? 0;
        }

        // Kirim ke view
        return view('dashboard.hasil_klasifikasi.peta.index', [
            'geojson' => $geojson,
            'query' => $query,
        ]);
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
    // Load data dengan eager loading relasi bertingkat
    $data = HasilKlasifikasi::with('pernikahan.wilayah.resiko_wilayah')->get();

    // Group berdasarkan nama desa wilayah, dengan fallback label jika data tidak lengkap
    $wilayahChart = $data->groupBy(function ($item) {
        return optional($item->pernikahan)
            ->wilayah
            ? $item->pernikahan->wilayah->desa
            : 'Tidak Diketahui';
    })->map->count();

    // Group berdasarkan nama resiko wilayah, dengan fallback label jika data tidak lengkap
    $resikoChart = $data->groupBy(function ($item) {
        return optional(optional(optional($item->pernikahan)->wilayah)->resikoWilayah)
            ->resiko_wilayah
            ?: 'Tidak Diketahui';
    })->map->count();

    // Group berdasarkan tahun dari created_at, pastikan created_at ada
    $tahunChart = $data->groupBy(function ($item) {
        return $item->created_at
            ? \Carbon\Carbon::parse($item->created_at)->format('Y')
            : 'Tidak Diketahui';
    })->map->count();

    return view('dashboard.hasil_klasifikasi.chart.index', [
        'wilayahLabels' => $wilayahChart->keys(),
        'wilayahData' => $wilayahChart->values(),

        'resikoLabels' => $resikoChart->keys(),
        'resikoData' => $resikoChart->values(),

        'tahunLabels' => $tahunChart->keys(),
        'tahunData' => $tahunChart->values(),
    ]);
}




public function graphView(Request $request)
{
    $tahunTerpilih = $request->input('tahun', date('Y'));

    // Ambil daftar tahun unik dari data pernikahan
    $daftarTahun = DataPernikahan::selectRaw('YEAR(tanggal_akad) as tahun')
        ->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

    // 1. Donut Chart: Jumlah pernikahan dini per desa (filter tahun)
    $pernikahanPerWilayah = DataWilayah::withCount([
        'wilayah as jumlah_pernikahan_dini' => function ($query) use ($tahunTerpilih) {
            $query->whereYear('tanggal_akad', $tahunTerpilih)
                ->whereHas('hasilKlasifikasi', function ($sub) {
                    $sub->where('kategori_pernikahan', 'Pernikahan Dini');
                });
        }
    ])->get();

    $wilayahLabels = $pernikahanPerWilayah->pluck('desa');
    $wilayahData = $pernikahanPerWilayah->pluck('jumlah_pernikahan_dini');

    // 2. Bubble Chart: Risiko (tidak tergantung tahun)
    $resikoCounts = DB::table('resiko_wilayah')
        ->select('resiko_wilayah', DB::raw('COUNT(*) as jumlah'))
        ->groupBy('resiko_wilayah')
        ->get();

    $resikoLabels = $resikoCounts->pluck('resiko_wilayah');
    $resikoData = $resikoCounts->map(function ($item, $index) {
        return [
            'x' => $index + 1,
            'y' => $item->jumlah,
            'r' => $item->jumlah * 2,
            'kategori' => $item->resiko_wilayah
        ];
    });

    // 3. Scatter Chart: Pernikahan dini per bulan di tahun terpilih
    $bulanData = DataPernikahan::selectRaw('MONTH(tanggal_akad) as bulan, COUNT(*) as jumlah')
        ->whereYear('tanggal_akad', $tahunTerpilih)
        ->whereHas('hasilKlasifikasi', function ($q) {
            $q->where('kategori_pernikahan', 'Pernikahan Dini');
        })
        ->groupByRaw('MONTH(tanggal_akad)')
        ->orderBy('bulan')
        ->get();

    $bulanLabels = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];

    $bulanValues = [];
    for ($i = 1; $i <= 12; $i++) {
        $bulanValues[] = $bulanData->firstWhere('bulan', $i)->jumlah ?? 0;
    }



    return view('dashboard.hasil_klasifikasi.grafik.index', [
        'wilayahLabels' => $wilayahLabels,
        'wilayahData' => $wilayahData,
        'resikoLabels' => $resikoLabels,
        'resikoData' => $resikoData,
        'bulanLabels' => array_values($bulanLabels),
        'bulanData' => $bulanValues,
        'daftarTahun' => $daftarTahun,
        'tahunTerpilih' => $tahunTerpilih
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







}
