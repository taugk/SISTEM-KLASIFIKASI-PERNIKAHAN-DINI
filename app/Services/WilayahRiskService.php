<?php

namespace App\Services;

use App\Models\DataWilayah;
use Illuminate\Support\Facades\Cache;

class WilayahRiskService
{
    public function getKategoriResiko(int $wilayahId, string $tahun): array
    {
        $cacheKey = "wilayah_risk_{$wilayahId}_{$tahun}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($wilayahId, $tahun) {
            $faktor = $this->hitungFaktorWilayah($wilayahId, $tahun);
            $skor = $this->hitungSkorRisiko($faktor);

            return [
                'kategori' => $this->tentukanKategori($skor),
                'skor' => round($skor, 2),
                'faktor' => $faktor,
                'rekomendasi' => $this->generateRekomendasi($faktor),
            ];
        });
    }

    private function hitungFaktorWilayah(int $wilayahId, string $tahun): array
    {
        $wilayah = DataWilayah::with(['pernikahan' => function ($q) use ($tahun) {
            $q->whereYear('tanggal_akad', $tahun)->with('hasilKlasifikasi');
        }])->findOrFail($wilayahId);

        $data = $wilayah->pernikahan;

        $total = $data->count();
        $totalDini = $data->filter(fn($p) => $p->hasilKlasifikasi?->kategori_pernikahan === 'Pernikahan Dini')->count();

        $pendidikanRendah = [
            'TIDAK SEKOLAH', 'TIDAK TAMAT SD/SEDERAJAT', 'TAMAT SD/SEDERAJAT', 'SLTP/SEDERAJAT'
        ];
        $pekerjaanTidakStabil = [
            'TIDAK BEKERJA', 'PELAJAR/MAHASISWA', 'PEKERJA LEPAS', 'PETANI', 'NELAYAN'
        ];

        $usiaMuda = 0;
        $pendidikanRendahCount = 0;
        $pekerjaanTidakStabilCount = 0;

        foreach ($data as $p) {
            $suami = strtoupper(trim($p->pendidikan_suami));
            $istri = strtoupper(trim($p->pendidikan_istri));
            $pekerjaanSuami = strtoupper(trim($p->pekerjaan_suami));
            $pekerjaanIstri = strtoupper(trim($p->pekerjaan_istri));

            if ((int)$p->usia_suami <= 19 || (int)$p->usia_istri <= 19) {
                $usiaMuda++;
            }

            if (in_array($suami, $pendidikanRendah) || in_array($istri, $pendidikanRendah)) {
                $pendidikanRendahCount++;
            }

            if (in_array($pekerjaanSuami, $pekerjaanTidakStabil) || in_array($pekerjaanIstri, $pekerjaanTidakStabil)) {
                $pekerjaanTidakStabilCount++;
            }
        }

        return [
            'nama_wilayah' => $wilayah->desa,
            'total_pasangan' => $total,
            'total_dini' => $totalDini,
            'persentase_dini' => $total > 0 ? ($totalDini / $total) * 100 : 0,
            'persentase_pendidikan_rendah' => $total > 0 ? ($pendidikanRendahCount / $total) * 100 : 0,
            'persentase_pekerjaan_tidak_stabil' => $total > 0 ? ($pekerjaanTidakStabilCount / $total) * 100 : 0,
            'persentase_usia_muda' => $total > 0 ? ($usiaMuda / $total) * 100 : 0,
        ];
    }

    private function hitungSkorRisiko(array $faktor): float
    {
        $bobot = config('wilayah_risk.bobot', [
            'persentase_dini' => 0.4,
            'pendidikan_rendah' => 0.2,
            'pekerjaan_tidak_stabil' => 0.2,
            'usia_muda' => 0.2,
        ]);

        return (
            $faktor['persentase_dini'] * $bobot['persentase_dini'] +
            $faktor['persentase_pendidikan_rendah'] * $bobot['pendidikan_rendah'] +
            $faktor['persentase_pekerjaan_tidak_stabil'] * $bobot['pekerjaan_tidak_stabil'] +
            $faktor['persentase_usia_muda'] * $bobot['usia_muda']
        ) / 100;
    }

    private function tentukanKategori(float $skor): string
    {
        $threshold = config('wilayah_risk.thresholds', [
            'tinggi' => 0.6,
            'sedang' => 0.3,
        ]);

        return match (true) {
            $skor >= $threshold['tinggi'] => 'tinggi',
            $skor >= $threshold['sedang'] => 'sedang',
            default => 'rendah',
        };
    }

    private function generateRekomendasi(array $faktor): string
    {
        if ($faktor['persentase_dini'] > 40) {
            return 'Lakukan penyuluhan intensif dan advokasi keluarga.';
        } elseif ($faktor['persentase_pendidikan_rendah'] > 50) {
            return 'Dorong program pendidikan dan beasiswa remaja.';
        } elseif ($faktor['persentase_pekerjaan_tidak_stabil'] > 50) {
            return 'Perkuat akses lapangan kerja dan pelatihan keterampilan.';
        } elseif ($faktor['persentase_usia_muda'] > 50) {
            return 'Sosialisasikan pentingnya usia matang dalam pernikahan.';
        }

        return 'Lakukan monitoring berkala dan pembinaan ringan.';
    }
}
