<?php

namespace App\Http\Controllers;

use Log;
use App\Models\DataWilayah;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DataPernikahan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HasilKlasifikasi;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataKlasifikasiExport;

class DataKlasifikasiController extends Controller
{
    public function index(Request $request)
    {
        $query = HasilKlasifikasi::with([
            'pernikahan:id,nama_suami,nama_istri,usia_suami,usia_istri,wilayah_id',
            'pernikahan.wilayah:id,desa'
        ])
        ->whereNotNull('id_pernikahan');

        // Filter nama suami/istri
        if ($request->filled('search')) {
            $query->whereHas('pernikahan', function ($q) use ($request) {
                $q->where('nama_suami', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_istri', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan kelurahan
        if ($request->filled('filter_kelurahan')) {
            $query->whereHas('pernikahan.wilayah', function ($q) use ($request) {
                $q->where('desa', $request->filter_kelurahan);
            });
        }

        // Filter berdasarkan tahun akad
        if ($request->filled('filter_tahun')) {
            $query->whereHas('pernikahan', function ($q) use ($request) {
                $q->whereYear('tanggal_akad', $request->filter_tahun);
            });
        }

        // Filter hasil klasifikasi
        if ($request->filled('hasil_klasifikasi')) {
            $query->where('kategori_pernikahan', $request->hasil_klasifikasi);
        }

        $data = $query->get();

        // Data untuk form dropdown
        $kelurahans = DataWilayah::select('desa')->distinct()->get();
        $tahun = DataPernikahan::selectRaw('YEAR(tanggal_akad) as tahun')->distinct()->get();
        $kategori = HasilKlasifikasi::select('kategori_pernikahan')->distinct()->get();

        return view('dashboard.data_klasifikasi.index', compact('data', 'kelurahans', 'tahun', 'kategori'));
    }

    public function detail($id)
{
    $klasifikasi = HasilKlasifikasi::with([
        'pernikahan:id,nama_suami,nama_istri,usia_suami,usia_istri,wilayah_id',
        'pernikahan.wilayah:id,desa'
    ])->find($id);

    return view('dashboard.data_klasifikasi.detail_klasifikasi', compact('klasifikasi'));
}

public function exportExcel(Request $request)
{
    // Prepare filters
    $filters = [
        'tahun' => $request->filter_tahun,
        'kelurahan' => $request->filter_kelurahan,
        'search' => $request->search,
        'kategori' => $request->filter_hasil_klasifikasi
    ];

    // Build descriptive filename
    $filenameParts = ['data_klasifikasi'];

    if ($filters['tahun']) {
        $filenameParts[] = 'tahun_' . $filters['tahun'];
    }

    if ($filters['kelurahan']) {
        $filenameParts[] = 'kelurahan_' . Str::slug($filters['kelurahan']);
    }

    if ($filters['kategori']) {
        $filenameParts[] = Str::slug($filters['kategori']);
    }

    if ($filters['search']) {
        $filenameParts[] = 'pencarian_' . Str::slug(substr($filters['search'], 0, 20));
    }

    $filename = implode('_', $filenameParts) . '_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(
        new DataKlasifikasiExport($filters),
        $filename,
        \Maatwebsite\Excel\Excel::XLSX,
        ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
    );
}

public function exportCsv(Request $request)
{
    // Prepare filters
    $filters = [
        'tahun' => $request->filter_tahun,
        'kelurahan' => $request->filter_kelurahan,
        'search' => $request->search,
        'kategori' => $request->filter_hasil_klasifikasi
    ];

    // Build descriptive filename
    $filenameParts = ['data_klasifikasi'];

    if ($filters['tahun']) {
        $filenameParts[] = 'tahun_' . $filters['tahun'];
    }

    if ($filters['kelurahan']) {
        $filenameParts[] = 'kelurahan_' . Str::slug($filters['kelurahan']);
    }

    if ($filters['kategori']) {
        $filenameParts[] = Str::slug($filters['kategori']);
    }

    if ($filters['search']) {
        $filenameParts[] = 'pencarian_' . Str::slug(substr($filters['search'], 0, 20));
    }

    $filename = implode('_', $filenameParts) . '_' . now()->format('Ymd_His') . '.csv';

    return Excel::download(
        new DataKlasifikasiExport($filters),
        $filename,
        \Maatwebsite\Excel\Excel::CSV,
        ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
    );
}

public function exportPdf(Request $request)
{
    $query = HasilKlasifikasi::with([
        'pernikahan:id,nama_suami,nama_istri,usia_suami,usia_istri,tanggal_akad,wilayah_id',
        'pernikahan.wilayah:id,desa'
    ])->whereNotNull('id_pernikahan');

    // Filter nama suami/istri
    if ($request->filled('search')) {
        $query->whereHas('pernikahan', function ($q) use ($request) {
            $q->where('nama_suami', 'like', '%' . $request->search . '%')
              ->orWhere('nama_istri', 'like', '%' . $request->search . '%');
        });
    }

    // Filter kelurahan
    if ($request->filled('filter_kelurahan')) {
        $query->whereHas('pernikahan.wilayah', function ($q) use ($request) {
            $q->where('desa', $request->filter_kelurahan);
        });
    }

    // Filter tahun akad
    if ($request->filled('filter_tahun')) {
        $query->whereHas('pernikahan', function ($q) use ($request) {
            $q->whereYear('tanggal_akad', $request->filter_tahun);
        });
    }

    // Filter hasil klasifikasi
    if ($request->filled('hasil_klasifikasi')) {
        $query->where('kategori_pernikahan', $request->hasil_klasifikasi);
    }

    // Ambil data
    $data = $query->get()->map(function ($item) {
        return [
            'nama_suami'       => $item->pernikahan->nama_suami ?? '-',
            'nama_istri'       => $item->pernikahan->nama_istri ?? '-',
            'usia_suami'       => $item->pernikahan->usia_suami ?? '-',
            'usia_istri'       => $item->pernikahan->usia_istri ?? '-',
            'tanggal_akad'     => optional($item->pernikahan)->tanggal_akad 
                                    ? \Carbon\Carbon::parse($item->pernikahan->tanggal_akad)->translatedFormat('d F Y')
                                    : '-',
            'kelurahan'        => $item->pernikahan->wilayah->desa ?? '-',
            'kategori'         => ucfirst($item->kategori_pernikahan),
        ];
    });

    // Judul dan deskripsi
    $judul = 'LAPORAN HASIL KLASIFIKASI PERNIKAHAN';
    $keterangan = 'Data hasil klasifikasi berdasarkan informasi pasangan dan wilayah pernikahan.';

    // Kolom yang ingin ditampilkan
    $kolom = [
        'nama_suami'   => 'Nama Suami',
        'nama_istri'   => 'Nama Istri',
        'usia_suami'   => 'Usia Suami',
        'usia_istri'   => 'Usia Istri',
        'tanggal_akad' => 'Tanggal Akad',
        'kelurahan'    => 'Kelurahan/Desa',
        'kategori'     => 'Kategori Pernikahan',
    ];

    // Nama file
    $fileName = 'hasil_klasifikasi_pernikahan_' . now()->format('Ymd_His') . '.pdf';

    // Buat PDF
    $pdf = Pdf::loadView('components.laporan_pdf', compact('data', 'judul', 'keterangan', 'kolom'))
        ->setPaper('A4', 'landscape');

    return $pdf->download($fileName);
}

}
