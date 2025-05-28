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

    public function view(): View
    {
        $tahun = $this->tahun;
        $wilayah_id = $this->wilayah_id;
        $kategori = $this->kategori_wilayah;

        $daftarTahun = DB::table('resiko_wilayah')
            ->selectRaw('DISTINCT LEFT(periode, 4) as tahun')
            ->pluck('tahun');

        $daftarWilayah = DB::table('data_wilayah')->get();

        $wilayahFilteredIds = DB::table('resiko_wilayah')
            ->when($kategori, fn($q) => $q->where('resiko_wilayah', $kategori))
            ->pluck('id_wilayah')
            ->unique();

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

        $pernikahan = HasilKlasifikasi::with(['pernikahan.wilayah.resiko_wilayah'])
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->get()
            ->filter(function ($item) use ($wilayah_id, $kategori) {
                $wilayah = $item->pernikahan?->wilayah;
                $resiko = $wilayah?->resiko_wilayah->first()?->resiko_wilayah;
                return (!$wilayah_id || $wilayah?->id == $wilayah_id)
                    && (!$kategori || $resiko === $kategori);
            });

        $statistikKategori = $pernikahan
            ->groupBy('kategori_pernikahan')
            ->map(function ($group, $kategori_pernikahan) {
                return (object)[
                    'kategori_pernikahan' => $kategori_pernikahan,
                    'total' => $group->count()
                ];
            })
            ->values();

        $statistikUsia = [
            'avg_suami' => round($pernikahan->avg(fn($p) => $p->pernikahan?->usia_suami ?? 0), 2),
            'avg_istri' => round($pernikahan->avg(fn($p) => $p->pernikahan?->usia_istri ?? 0), 2),
            'min_suami' => $pernikahan->min(fn($p) => $p->pernikahan?->usia_suami ?? 0),
            'min_istri' => $pernikahan->min(fn($p) => $p->pernikahan?->usia_istri ?? 0),
            'max_suami' => $pernikahan->max(fn($p) => $p->pernikahan?->usia_suami ?? 0),
            'max_istri' => $pernikahan->max(fn($p) => $p->pernikahan?->usia_istri ?? 0),
        ];

        $statistikGender = [
            'suami_dini' => $pernikahan->filter(fn($p) => $p->pernikahan?->usia_suami < 19)->count(),
            'istri_dini' => $pernikahan->filter(fn($p) => $p->pernikahan?->usia_istri < 19)->count(),
        ];

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

        return view('dashboard.laporan.export-excel', compact(
            'statistikWilayah',
            'statistikKategori',
            'statistikUsia',
            'statistikGender',
            'statistikPendidikan'
        ));
    }
}