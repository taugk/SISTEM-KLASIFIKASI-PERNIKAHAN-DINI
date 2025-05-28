<?php
namespace App\Http\Controllers;

use App\Exports\Laporan;
use App\Models\DataWilayah;
use Illuminate\Http\Request;
use App\Models\DataPernikahan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HasilKlasifikasi;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{

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

        $nama_wilayah = 'Semua Wilayah';
        if ($wilayah_id) {
            $wilayah = DataWilayah::find($wilayah_id);
            $nama_wilayah = $wilayah ? $wilayah->desa : 'Wilayah tidak ditemukan';
        }

        


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

    return $pdf->download($filename);
}






}
