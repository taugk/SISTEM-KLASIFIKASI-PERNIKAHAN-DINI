<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use App\Models\DataWilayah;
use App\Models\DataPernikahan;
use App\Models\HasilKlasifikasi;

class Laporan implements FromView
{
    protected $tahun;
    protected $wilayah_id;

    protected $kategori_wilayah;

    public function __construct($tahun = null, $wilayah_id = null, $kategori_wilayah = null)
    {
        $this->tahun = $tahun;
        $this->wilayah_id = $wilayah_id;
        $this->kategori_wilayah = $kategori_wilayah;
    }
    /**
     * Export data laporan dalam format excel

     * @return \Illuminate\Contracts\View\View
     */
/*******  331b0064-b8ba-412e-bc24-6dcf9167ed12  *******/
    public function view(): View
    {
        $tahun = $this->tahun;
        $wilayah_id = $this->wilayah_id;
        $kategori = $this->kategori_wilayah;


        $statistikWilayah = DataWilayah::when($wilayah_id, fn($q) => $q->where('id', $wilayah_id))
            ->when($kategori, function ($query) use ($kategori) {
                return $query->whereHas('resikoWilayah', function ($q) use ($kategori) {
                    $q->where('resiko_wilayah', $kategori);
                });
            })
            ->withCount(['wilayah as jumlah_pernikahan' => fn($q) => $q->select(DB::raw('count(*)'))])
            ->with(['resikoWilayah' => function ($q) use ($tahun) {
                if ($tahun) $q->where('periode', 'like', "$tahun%");
                $q->select('id_wilayah', 'resiko_wilayah', 'jumlah_pernikahan_dini', 'periode')
                  ->groupBy('id_wilayah', 'resiko_wilayah', 'jumlah_pernikahan_dini', 'periode');
            }])
            ->get();


        $statistikKategori = HasilKlasifikasi::select('kategori_pernikahan')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('kategori_pernikahan')
            ->get();

        $pernikahan = HasilKlasifikasi::when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->when($wilayah_id, fn($q) => $q->where('id_wilayah', $wilayah_id))
            ->get();

        $statistikUsia = [
            'avg_suami' => round($pernikahan->avg('usia_suami'), 2),
            'avg_istri' => round($pernikahan->avg('usia_istri'), 2),
            'min_suami' => $pernikahan->min('usia_suami'),
            'min_istri' => $pernikahan->min('usia_istri'),
            'max_suami' => $pernikahan->max('usia_suami'),
            'max_istri' => $pernikahan->max('usia_istri'),
        ];

        $statistikGender = [
            'suami_dini' => $pernikahan->where('usia_suami', '<', 19)->count(),
            'istri_dini' => $pernikahan->where('usia_istri', '<', 19)->count(),
        ];

        $data = DataPernikahan::when($tahun, fn($q) => $q->whereYear('tanggal_akad', $tahun))
            ->when($wilayah_id, fn($q) => $q->where('wilayah_id', $wilayah_id))
            ->get();

        $statistikPendidikan = $data
            ->groupBy('pendidikan_suami')
            ->map(function ($group, $key) use ($data) {
                return (object)[
                    'pendidikan' => $key ?? 'Tidak diketahui',
                    'jumlah_suami' => $group->count(),
                    'jumlah_istri' => $data->where('pendidikan_istri', $key)->count()
                ];
            })->values();

        return view('dashboard.laporan.export-excel', compact(
            'statistikWilayah',
            'statistikKategori',
            'statistikUsia',
            'statistikGender',
            'statistikPendidikan'
        ));
    }
}
