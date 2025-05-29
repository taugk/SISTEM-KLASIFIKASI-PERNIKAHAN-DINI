<?php

namespace App\Http\Controllers;

use App\Models\DataEdukasi;
use Carbon\Carbon;
use App\Models\DataWilayah;
use Illuminate\Http\Request;
use App\Models\DataPernikahan;
use App\Models\Resiko_Wilayah;
use App\Models\HasilKlasifikasi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total data pernikahan
        $totalPernikahan = DataPernikahan::count();

        // Data pernikahan bulan ini
        $pernikahanBulanIni = DataPernikahan::whereMonth('tanggal_akad', Carbon::now()->month)
                                            ->whereYear('tanggal_akad', Carbon::now()->year)
                                            ->count();

        // Ambil data tren pernikahan per bulan dan tahun
        $rawData = HasilKlasifikasi::join('pernikahan', 'hasil_klasifikasi.id_pernikahan', '=', 'pernikahan.id')
            ->where('hasil_klasifikasi.kategori_pernikahan', 'Pernikahan Dini')
            ->selectRaw('YEAR(pernikahan.tanggal_akad) as tahun, MONTH(pernikahan.tanggal_akad) as bulan, COUNT(*) as total')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'ASC')
            ->orderBy('bulan', 'ASC')
            ->get();

        // Nama bulan Indonesia
        $nama_bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Ubah bulan angka jadi teks dan susun data tren
        $datatren = $rawData->map(function ($item) use ($nama_bulan) {
            return [
                'tahun' => $item->tahun,
                'bulan' => $nama_bulan[(int) $item->bulan] . ' ' . $item->tahun,
                'total' => $item->total
            ];
        });

        // Ambil bulan dan total untuk chart
        $bulan = $datatren->pluck('bulan');
        $total = $datatren->pluck('total');

        // Jumlah pernikahan dini
        $pernikahanDini = HasilKlasifikasi::where('kategori_pernikahan', 'Pernikahan Dini')->count();

        // Jumlah pernikahan dini per bulan
        $pernikahanDiniBulanIni = HasilKlasifikasi::where('kategori_pernikahan', 'Pernikahan Dini')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Jumlah wilayah
        $jumlahWilayah = DataWilayah::count();

        // Jumlah materi edukasi (asumsi menggunakan DB langsung)
        $jumlahMateri = DataEdukasi::count();

        // Distribusi risiko wilayah
        $resikoData = Resiko_Wilayah::select('resiko_wilayah', DB::raw('count(*) as jumlah'))
            ->groupBy('resiko_wilayah')
            ->orderByRaw("FIELD(resiko_wilayah, 'tinggi', 'sedang', 'rendah')")  // Sort in specific order
            ->get()
            ->map(function ($item) {
                // Capitalize first letter and format the label
                return [
                    'resiko_wilayah' => ucfirst(strtolower($item->resiko_wilayah)),
                    'jumlah' => $item->jumlah
                ];
            });

        $labelRisiko = $resikoData->pluck('resiko_wilayah');
        $jumlahRisiko = $resikoData->pluck('jumlah');

        // Wilayah risiko tinggi
        $wilayahRisikoTinggi = Resiko_Wilayah::with('wilayah')
            ->where('resiko_wilayah', 'tinggi')
            ->orderByDesc('jumlah_pernikahan_dini')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'desa' => $item->wilayah->desa ?? '-',
                    'kecamatan' => $item->wilayah->kecamatan ?? '-',
                    'jumlah_pernikahan_dini' => $item->jumlah_pernikahan_dini,
                    'periode' => $item->periode,
                ];
            });

        // Kirim data ke view
        return view('dashboard.index', compact(
            'totalPernikahan',
            'pernikahanBulanIni',
            'datatren',
            'bulan',
            'total',
            'pernikahanDini',
            'pernikahanDiniBulanIni',
            'jumlahWilayah',
            'jumlahMateri',
            'labelRisiko',
            'jumlahRisiko',
            'wilayahRisikoTinggi'
        ));
    }
}
