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


    public function index(Request $request)
    {
        // Query untuk mengambil data resiko_wilayah dengan data wilayah yang terhubung
        $query = Resiko_Wilayah::with('wilayah') // Memastikan relasi dengan model 'wilayah' dimuat
            ->leftJoin('data_wilayah', 'resiko_wilayah.id_wilayah', '=', 'data_wilayah.id') // Join dengan tabel data_wilayah
            ->orderBy('resiko_wilayah.created_at', 'desc');

        // Filter search nama suami/istri
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

        // Ambil data yang sudah difilter
        $data = $query->get();

        // Data referensi untuk filter dropdown
        $kelurahans = DataWilayah::select('desa')->distinct()->get(); // Fetch all desa/kesulahan
        $tahun = Resiko_Wilayah::select('periode as tahun')->distinct()->orderBy('tahun', 'desc')->get();
        $kategori = Resiko_Wilayah::select('resiko_wilayah as kategori_pernikahan')->distinct()->get();

        $wilayah = DataWilayah::all(); // Get all wilayah data

        return view('dashboard.hasil_klasifikasi.index', compact(
            'data', 'kelurahans', 'tahun', 'kategori', 'wilayah'
        ));
    }


    public function detail($id){
        $data = HasilKlasifikasi::find($id);
        return view('dashboard.hasil_klasifikasi.detail', compact('data'));
    }

    public function chart(Request $request)
{
    $data = HasilKlasifikasi::all();

    // Grafik 1: Jumlah per wilayah
    $wilayahChart = $data->groupBy('wilayah')->map(function ($item) {
        return $item->count();
    });

    // Grafik 2: Distribusi resiko
    $resikoChart = $data->groupBy('resiko_wilayah')->map(function ($item) {
        return $item->count();
    });

    // Grafik 3: Jumlah per tahun (asumsi ada kolom 'tahun' atau ambil dari created_at)
    $tahunChart = $data->groupBy(function ($item) {
        return \Carbon\Carbon::parse($item->created_at)->format('Y');
    })->map(function ($item) {
        return $item->count();
    });

    return view('dashboard.hasil_klasifikasi.chart.index', [
        'wilayahLabels' => $wilayahChart->keys(),
        'wilayahData' => $wilayahChart->values(),

        'resikoLabels' => $resikoChart->keys(),
        'resikoData' => $resikoChart->values(),

        'tahunLabels' => $tahunChart->keys(),
        'tahunData' => $tahunChart->values()
    ]);
}


public function graphView(Request $request)
{
    $data = Resiko_Wilayah::with('wilayah')->get();

    // Grafik 1: Jumlah pernikahan dini per desa
    $wilayahChart = $data->groupBy('wilayah.desa')->map(function ($item) {
        return $item->sum('jumlah_pernikahan_dini');
    });

    // Grafik 2: Total pernikahan dini per tingkat risiko
    $resikoChart = $data->groupBy('resiko_wilayah')->map(function ($item) {
        return $item->sum('jumlah_pernikahan_dini');
    });

    // Grafik 3: Jumlah pernikahan dini per tahun/periode
    $periodeChart = $data->groupBy('periode')->map(function ($item) {
        return $item->sum('jumlah_pernikahan_dini');
    });

    return view('dashboard.hasil_klasifikasi.grafik.index', [
        'wilayahLabels' => $wilayahChart->keys(),
        'wilayahData' => $wilayahChart->values(),
        'resikoLabels' => $resikoChart->keys(),
        'resikoData' => $resikoChart->values(),
        'tahunLabels' => $periodeChart->keys(),
        'tahunData' => $periodeChart->values()
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
