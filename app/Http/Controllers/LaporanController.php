<?php
namespace App\Http\Controllers;

use App\Exports\Laporan;
use App\Models\DataWilayah;
use Illuminate\Http\Request;
use App\Models\DataPernikahan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HasilKlasifikasi;
use App\Models\Resiko_Wilayah;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function laporanAkhir(Request $request)
{
    $tahun = $request->input('tahun');
    $kategori = $request->input('kategori_wilayah');

    // Ambil tahun unik dari kolom 'periode'
    $daftarTahun = Resiko_Wilayah::selectRaw('DISTINCT YEAR(periode) as tahun')->orderBy('tahun', 'desc')->pluck('tahun');

    // Query data resiko wilayah beserta relasi wilayah dan pernikahan, filter pernikahan berdasarkan tahun
    $query = Resiko_Wilayah::with(['wilayah.pernikahan' => function ($q) use ($tahun) {
        if ($tahun) {
            $q->whereYear('tanggal_akad', $tahun);
        }
    }]);

    // Filter berdasarkan tahun (periode)
    if ($tahun) {
        $query->whereYear('periode', $tahun);
    }

    // Filter berdasarkan kategori wilayah (resiko)
    if ($kategori) {
        $query->where('resiko_wilayah', $kategori);
    }

    $data = $query->get();

    // Rekapitulasi data per wilayah
    $rekap = $data->map(function ($item) {
        $wilayah = $item->wilayah;

        // Ambil pernikahan di wilayah tersebut (sudah terfilter berdasarkan tahun di eager load)
        $pelakuDini = $wilayah->pernikahan->filter(function ($p) {
            return ($p->usia_suami < 20 || $p->usia_istri < 20);
        });

        $jumlahDini = $pelakuDini->count();
        $rata_usia_suami = $pelakuDini->avg('usia_suami') ?: 0;
        $rata_usia_istri = $pelakuDini->avg('usia_istri') ?: 0;

        // Ambil pendidikan terbanyak (modus)
        $rata_pendidikan_suami = $this->getMostFrequent($pelakuDini->pluck('pendidikan_suami')->toArray());
        $rata_pendidikan_istri = $this->getMostFrequent($pelakuDini->pluck('pendidikan_istri')->toArray());

        // Normalisasi untuk pengecekan pendidikan rendah
        $pendidikan_suami = strtolower(trim($rata_pendidikan_suami));
        $pendidikan_istri = strtolower(trim($rata_pendidikan_istri));

        $pendidikan_rendah = [
            'TIDAK/BELUM SEKOLAH',
            'TIDAK TAMAT/BELUM SEKOLAH',
            'TIDAK TAMAT SD/SEDERAJAT',
            'SD/SEDERAJAT',
            'SLTP/SEDERAJAT',
        ];


        // Logika rekomendasi
        $rekomendasi = '-';

        if (
            ($rata_usia_suami <= 19 || $rata_usia_istri < 19) ||
            in_array($pendidikan_suami, $pendidikan_rendah) ||
            in_array($pendidikan_istri, $pendidikan_rendah)
        ) {
            $rekomendasi = 'Penyuluhan intensif dan kunjungan langsung';
        } elseif ($jumlahDini > 10) {
            $rekomendasi = 'Penyuluhan intensif dan kunjungan langsung';
        } elseif ($jumlahDini > 2) {
            $rekomendasi = 'Penyuluhan berkala melalui posyandu & sekolah';
        } else {
            $rekomendasi = 'Monitoring rutin dan penyuluhan ringan';
        }

        return [
            'wilayah' => ucwords(strtolower($wilayah->desa)),
            'resiko' => ucfirst($item->resiko_wilayah),
            'jumlah_pernikahan_dini' => $jumlahDini,
            'rata_usia_suami' => $rata_usia_suami ? number_format($rata_usia_suami, 1) : '-',
            'rata_usia_istri' => $rata_usia_istri ? number_format($rata_usia_istri, 1) : '-',
            'rata_pendidikan_suami' => ucwords(strtolower($rata_pendidikan_suami)?: '-'),
            'rata_pendidikan_istri' => ucwords(strtolower($rata_pendidikan_istri) ?: '-'),
        'rata_pekerjaan_suami' => ucwords(strtolower($pelakuDini->pluck('pekerjaan_suami')->unique()->implode(', ') ?: '-')),
            'rata_pekerjaan_istri' => ucwords(strtolower($pelakuDini->pluck('pekerjaan_istri')->unique()->implode(', ') ?: '-')),
            'rekomendasi' => $rekomendasi,
        ];
    });

    return view('dashboard.laporan.laporan_akhir', compact(
        'rekap', 'daftarTahun', 'tahun', 'kategori'
    ));
}


    public function laporanAkhirPdf(Request $request)
    {
        $tahun = $request->input('tahun');
        $kategori = $request->input('kategori_wilayah');

        // Ambil data laporan akhir
        $rekap = Resiko_Wilayah::with(['wilayah.pernikahan'])
            ->when($tahun, fn($q) => $q->where('periode', $tahun))
            ->when($kategori, fn($q) => $q->where('resiko_wilayah', $kategori))
            ->get()
            ->map(function ($item) {
                $wilayah = $item->wilayah;

                // Filter pelaku pernikahan dini (usia suami atau istri < 20)
                $pelakuDini = $wilayah->pernikahan->filter(function ($p) {
                    return ($p->usia_suami < 20 || $p->usia_istri < 20);
                });

                $jumlahDini = $pelakuDini->count();
                $rata_usia_suami = $pelakuDini->avg('usia_suami') ?: 0;
                $rata_usia_istri = $pelakuDini->avg('usia_istri') ?: 0;

                // Fungsi bantu cari pendidikan terbanyak (modus)
                $rata_pendidikan_suami = $this->getMostFrequent($pelakuDini->pluck('pendidikan_suami')->toArray());
                $rata_pendidikan_istri = $this->getMostFrequent($pelakuDini->pluck('pendidikan_istri')->toArray());

                // Normalisasi string pendidikan (huruf kecil tanpa spasi)
                $pendidikan_suami = strtolower(trim($rata_pendidikan_suami));
                $pendidikan_istri = strtolower(trim($rata_pendidikan_istri));

                // Logika rekomendasi dinamis dengan prioritas tinggi jika usia <18 atau pendidikan rendah
                $pendidikan_rendah = [
                    'TIDAK/BELUM SEKOLAH',
                    'TIDAK TAMAT/BELUM SEKOLAH',
                    'TIDAK TAMAT SD/SEDERAJAT',
                    'SD/SEDERAJAT',
                    'SLTP/SEDERAJAT',
                ];


                if (
                    ($rata_usia_suami <= 19 || $rata_usia_istri < 19) ||
                    in_array($pendidikan_suami, $pendidikan_rendah) ||
                    in_array($pendidikan_istri, $pendidikan_rendah)
                ) {
                    $rekomendasi = 'Penyuluhan intensif dan kunjungan langsung';
                } elseif ($jumlahDini > 10) {
                    $rekomendasi = 'Penyuluhan intensif dan kunjungan langsung';
                } elseif ($jumlahDini > 2) {
                    $rekomendasi = 'Penyuluhan berkala melalui posyandu & sekolah';
                } else {
                    $rekomendasi = 'Monitoring rutin dan penyuluhan ringan';
                }
                return [
                    'wilayah' => $wilayah->desa,
                    'resiko' => ucfirst($item->resiko_wilayah),
                    'jumlah_pernikahan_dini' => $jumlahDini,
                    'rata_usia_suami' => $rata_usia_suami ? number_format($rata_usia_suami, 1) : '-',
                    'rata_usia_istri' => $rata_usia_istri ? number_format($rata_usia_istri, 1) : '-',
                    'rata_pendidikan_suami' => $rata_pendidikan_suami ?: '-',
                    'rata_pendidikan_istri' => $rata_pendidikan_istri ?: '-',
                    'rekomendasi' => $rekomendasi,
                ];
            });
        $pdf = Pdf::loadView('components.laporan_akhir_pdf', [
            'rekap' => $rekap,
            'tahun' => $tahun,
            'kategori' => $kategori,
        ]);
        $pdf->setPaper('A4', 'landscape');
        $filename = 'laporan_akhir_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->stream($filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    // Fungsi bantu cari modus (nilai paling sering muncul)
    private function getMostFrequent(array $array)
    {
        if (empty($array)) {
            return null;
        }
        $values = array_count_values($array);
        arsort($values);
        return key($values);
    }

    public function statistik(Request $request)
{
    $tahun = $request->input('tahun');
    $wilayah_id = $request->input('wilayah_id');
    $kategori = $request->input('kategori_wilayah');

    // Dropdown
    $daftarTahun = DB::table('resiko_wilayah')
        ->selectRaw('DISTINCT LEFT(periode, 4) as tahun')
        ->pluck('tahun');

    $daftarWilayah = DB::table('data_wilayah')->get();

    // Data wilayah yang sesuai filter kategori_wilayah
    $wilayahFilteredIds = DB::table('resiko_wilayah')
        ->when($kategori, fn($q) => $q->where('resiko_wilayah', $kategori))
        ->pluck('id_wilayah')
        ->unique();

    // Statistik kategori wilayah (tetap ambil semua untuk chart)
    $kategoriWilayah = DB::table('resiko_wilayah')
        ->selectRaw('resiko_wilayah, COUNT(*) as jumlah_pernikahan_dini')
        ->groupBy('resiko_wilayah')
        ->get();

    $statistikWilayah = DataWilayah::query()
    ->when($wilayah_id, fn($q) => $q->where('id', $wilayah_id))
    ->when($kategori && $wilayahFilteredIds->isNotEmpty(), fn($q) =>
        $q->whereIn('id', $wilayahFilteredIds)
    )
    ->when($kategori || $tahun, function ($query) use ($kategori, $tahun) {
        $query->whereHas('resiko_wilayah', function ($q) use ($kategori, $tahun) {
            if ($kategori) {
                $q->where('resiko_wilayah', $kategori);
            }
            if ($tahun) {
                $q->where('periode', 'like', "$tahun%");
            }
        });
    })
    ->withCount([
        'wilayah as jumlah_pernikahan' => fn($q) =>
            $q->select(DB::raw('count(*)'))
    ])
    ->with([
        'resiko_wilayah' => function ($query) use ($tahun, $kategori) {
            if ($tahun) {
                $query->where('periode', 'like', "$tahun%");
            }
            if ($kategori) {
                $query->where('resiko_wilayah', $kategori);
            }
            $query->select('id_wilayah', 'resiko_wilayah', 'jumlah_pernikahan_dini', 'periode');
        }
    ])
    ->get();


    // Ambil data hasil klasifikasi + filter wilayah dan kategori
    $pernikahan = HasilKlasifikasi::with(['pernikahan.wilayah.resiko_wilayah'])
        ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
        ->get()
        ->filter(function ($item) use ($wilayah_id, $kategori) {
            $wilayah = $item->pernikahan?->wilayah;
            $resiko = $wilayah?->resiko_wilayah->first()?->resiko_wilayah;
            return (!$wilayah_id || $wilayah?->id == $wilayah_id)
                && (!$kategori || $resiko === $kategori);
        });

    // Statistik kategori hasil klasifikasi
    $statistikKategori = $pernikahan
        ->groupBy('kategori_pernikahan')
        ->map(function ($group, $kategori_pernikahan) {
            return (object)[
                'kategori_pernikahan' => $kategori_pernikahan,
                'total' => $group->count()
            ];
        })
        ->values();

    // Statistik usia
    $statistikUsia = [
        'avg_suami' => round($pernikahan->avg(fn($p) => $p->pernikahan?->usia_suami ?? 0), 2),
        'avg_istri' => round($pernikahan->avg(fn($p) => $p->pernikahan?->usia_istri ?? 0), 2),
        'min_suami' => $pernikahan->min(fn($p) => $p->pernikahan?->usia_suami ?? 0),
        'min_istri' => $pernikahan->min(fn($p) => $p->pernikahan?->usia_istri ?? 0),
        'max_suami' => $pernikahan->max(fn($p) => $p->pernikahan?->usia_suami ?? 0),
        'max_istri' => $pernikahan->max(fn($p) => $p->pernikahan?->usia_istri ?? 0),
    ];

    // Statistik gender
    $statistikGender = [
        'suami_dini' => $pernikahan->filter(fn($p) => $p->pernikahan?->usia_suami < 19)->count(),
        'istri_dini' => $pernikahan->filter(fn($p) => $p->pernikahan?->usia_istri < 19)->count(),
    ];

    // Statistik pendidikan
    $data = DataPernikahan::with('wilayah.resiko_wilayah')
        ->when($tahun, fn($q) => $q->whereYear('tanggal_akad', $tahun))
        ->when($wilayah_id, fn($q) => $q->where('wilayah_id', $wilayah_id))
        ->get()
        ->filter(function ($item) use ($kategori) {
            $resiko = $item->wilayah?->resiko_wilayah->first()?->resiko_wilayah;
            return !$kategori || $resiko === $kategori;
        });

    $statistikPendidikan = collect($data)
        ->groupBy('pendidikan_suami')
        ->map(function ($group, $key) use ($data) {
            return (object)[
                'pendidikan' => $key ?? 'Tidak diketahui',
                'jumlah_suami' => $group->count(),
                'jumlah_istri' => $data->where('pendidikan_istri', $key)->count()
            ];
        })
        ->values();

    return view('dashboard.laporan.index', compact(
        'statistikWilayah',
        'statistikKategori',
        'daftarTahun',
        'daftarWilayah',
        'tahun',
        'wilayah_id',
        'kategori',
        'kategoriWilayah',
        'statistikPendidikan',
        'statistikUsia',
        'statistikGender'
    ));
}

   public function exportExcel(Request $request)
{
    $tahun = $request->tahun;
    $wilayah_id = $request->wilayah_id;

    $filename = 'laporan_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(new Laporan($tahun, $wilayah_id), $filename, \Maatwebsite\Excel\Excel::XLSX, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'inline; filename="' . $filename . '"',
    ]);
}

public function exportCsv(Request $request)
{
    $tahun = $request->tahun;
    $wilayah_id = $request->wilayah_id;

    $filename = 'laporan_' . now()->format('Ymd_His') . '.csv';

    return Excel::download(new Laporan($tahun, $wilayah_id), $filename, \Maatwebsite\Excel\Excel::XLSX, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'inline; filename="' . $filename . '"',
    ]);
}

public function exportPdf(Request $request)
{
   $tahun = $request->input('tahun');
    $wilayah_id = $request->input('wilayah_id');
    $kategori = $request->input('kategori_wilayah');

    // Dropdown
    $daftarTahun = DB::table('resiko_wilayah')
        ->selectRaw('DISTINCT LEFT(periode, 4) as tahun')
        ->pluck('tahun');

    $daftarWilayah = DB::table('data_wilayah')->get();

    // Data wilayah yang sesuai filter kategori_wilayah
    $wilayahFilteredIds = DB::table('resiko_wilayah')
        ->when($kategori, fn($q) => $q->where('resiko_wilayah', $kategori))
        ->pluck('id_wilayah')
        ->unique();

    // Statistik kategori wilayah (tetap ambil semua untuk chart)
    $kategoriWilayah = DB::table('resiko_wilayah')
        ->selectRaw('resiko_wilayah, COUNT(*) as jumlah_pernikahan_dini')
        ->groupBy('resiko_wilayah')
        ->get();

    $statistikWilayah = DataWilayah::query()
    ->when($wilayah_id, fn($q) => $q->where('id', $wilayah_id))
    ->when($kategori && $wilayahFilteredIds->isNotEmpty(), fn($q) =>
        $q->whereIn('id', $wilayahFilteredIds)
    )
    ->when($kategori || $tahun, function ($query) use ($kategori, $tahun) {
        $query->whereHas('resiko_wilayah', function ($q) use ($kategori, $tahun) {
            if ($kategori) {
                $q->where('resiko_wilayah', $kategori);
            }
            if ($tahun) {
                $q->where('periode', 'like', "$tahun%");
            }
        });
    })
    ->withCount([
        'wilayah as jumlah_pernikahan' => fn($q) =>
            $q->select(DB::raw('count(*)'))
    ])
    ->with('resiko_wilayah')
    ->get();

    // Get nama wilayah for display
    $nama_wilayah = null;
    if ($wilayah_id) {
        $wilayah = DataWilayah::find($wilayah_id);
        $nama_wilayah = $wilayah ? $wilayah->desa . ', ' . $wilayah->kecamatan : null;
    }

    // Ambil data hasil klasifikasi + filter wilayah dan kategori
    $pernikahan = HasilKlasifikasi::with(['pernikahan.wilayah.resiko_wilayah'])
        ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
        ->get()
        ->filter(function ($item) use ($wilayah_id, $kategori) {
            $wilayah = $item->pernikahan?->wilayah;
            $resiko = $wilayah?->resiko_wilayah->first()?->resiko_wilayah;
            return (!$wilayah_id || $wilayah?->id == $wilayah_id)
                && (!$kategori || $resiko === $kategori);
        });

    // Statistik kategori hasil klasifikasi
    $statistikKategori = $pernikahan
        ->groupBy('kategori_pernikahan')
        ->map(function ($group, $kategori_pernikahan) {
            return (object)[
                'kategori_pernikahan' => $kategori_pernikahan,
                'total' => $group->count()
            ];
        })
        ->values();

    // Statistik usia
    $statistikUsia = [
        'avg_suami' => round($pernikahan->avg(fn($p) => $p->pernikahan?->usia_suami ?? 0), 2),
        'avg_istri' => round($pernikahan->avg(fn($p) => $p->pernikahan?->usia_istri ?? 0), 2),
        'min_suami' => $pernikahan->min(fn($p) => $p->pernikahan?->usia_suami ?? 0),
        'min_istri' => $pernikahan->min(fn($p) => $p->pernikahan?->usia_istri ?? 0),
        'max_suami' => $pernikahan->max(fn($p) => $p->pernikahan?->usia_suami ?? 0),
        'max_istri' => $pernikahan->max(fn($p) => $p->pernikahan?->usia_istri ?? 0),
    ];

    // Statistik gender
    $statistikGender = [
        'suami_dini' => $pernikahan->filter(fn($p) => $p->pernikahan?->usia_suami < 19)->count(),
        'istri_dini' => $pernikahan->filter(fn($p) => $p->pernikahan?->usia_istri < 19)->count(),
    ];

    // Statistik pendidikan
    $data = DataPernikahan::with('wilayah.resiko_wilayah')
        ->when($tahun, fn($q) => $q->whereYear('tanggal_akad', $tahun))
        ->when($wilayah_id, fn($q) => $q->where('wilayah_id', $wilayah_id))
        ->get()
        ->filter(function ($item) use ($kategori) {
            $resiko = $item->wilayah?->resiko_wilayah->first()?->resiko_wilayah;
            return !$kategori || $resiko === $kategori;
        });

    $statistikPendidikan = collect($data)
        ->groupBy('pendidikan_suami')
        ->map(function ($group, $key) use ($data) {
            return (object)[
                'pendidikan' => $key ?? 'Tidak diketahui',
                'jumlah_suami' => $group->count(),
                'jumlah_istri' => $data->where('pendidikan_istri', $key)->count()
            ];
        })
        ->values();

    $pdf = PDF::loadView('components.laporan_statistik_pdf', compact(
        'statistikWilayah',
        'statistikKategori',
        'tahun',
        'wilayah_id',
        'kategori',
        'kategoriWilayah',
        'statistikPendidikan',
        'statistikUsia',
        'statistikGender',
        'nama_wilayah'
    ));

    $pdf->setPaper('A4', 'landscape');
    $filename = 'laporan_statistik_' . now()->format('Ymd_His') . '.pdf';

    // Stream the PDF for preview instead of downloading
    return $pdf->stream($filename);
}






}
