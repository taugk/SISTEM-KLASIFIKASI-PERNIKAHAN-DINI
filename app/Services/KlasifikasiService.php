<?php

namespace App\Services;

use App\Models\DataWilayah;
use App\Models\DataPernikahan;
use App\Models\Resiko_Wilayah;
use App\Models\HasilKlasifikasi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KlasifikasiService
{
    public function prosesDanSimpan(array $dataInput, int $idPernikahan): void
    {
        // Validasi input sederhana
        if (empty($dataInput['wilayah_id'])) {
            throw new \InvalidArgumentException('Wilayah ID tidak boleh kosong.');
        }

        DB::beginTransaction();
        try {
            $wilayah = DataWilayah::find($dataInput['wilayah_id']);
            if (!$wilayah) {
                throw new \Exception('Wilayah tidak ditemukan.');
            }

            $payload = [
                'umur_suami' => $dataInput['usia_suami'],
                'umur_istri' => $dataInput['usia_istri'],
                'pendidikan_suami' => $dataInput['pendidikan_suami'],
                'pendidikan_istri' => $dataInput['pendidikan_istri'],
                'pekerjaan_suami' => $dataInput['pekerjaan_suami'],
                'pekerjaan_istri' => $dataInput['pekerjaan_istri'],
                'status_suami' => $dataInput['status_suami'],
                'status_istri' => $dataInput['status_istri'],
                'nama_kelurahan' => $wilayah->desa,
            ];

            $response = Http::timeout(5)->post('http://127.0.0.1:5000/predict', $payload);

            if (!$response->successful()) {
                throw new \Exception('Gagal menghubungi API klasifikasi. Status: ' . $response->status());
            }

            $hasil = $response->json();

            // Cek key hasil API
            if (!isset($hasil['hasil_prediksi'], $hasil['confidence'], $hasil['probabilitas'])) {
                throw new \Exception('Response API tidak lengkap.');
            }

            HasilKlasifikasi::updateOrCreate([
                'id_pernikahan' => $idPernikahan,
                'kategori_pernikahan' => $hasil['hasil_prediksi'],
                'confidence' => floatval(preg_replace('/[^0-9.]/', '', $hasil['confidence'])),
                'probabilitas' => json_encode($hasil['probabilitas']),
            ]);

            // Ambil tanggal max akad pernikahan
            $tanggalMax = DataPernikahan::max('tanggal_akad');
            if (!$tanggalMax) {
                throw new \Exception('Data pernikahan tidak ditemukan.');
            }

            // Ekstraksi tahun dan ubah ke format tanggal lengkap (YYYY-01-01) untuk tipe date MySQL
            $tahun = date('Y', strtotime($tanggalMax));
            $periode = "{$tahun}-01-01";

            $idWilayah = $dataInput['wilayah_id'];

            $jumlahPernikahan = DataPernikahan::where('wilayah_id', $idWilayah)
                ->whereYear('tanggal_akad', $tahun)
                ->count();

            $jumlahPernikahanDini = HasilKlasifikasi::whereHas('pernikahan', function ($query) use ($idWilayah, $tahun) {
                $query->where('wilayah_id', $idWilayah)
                    ->whereYear('tanggal_akad', $tahun);
            })->where('kategori_pernikahan', 'Pernikahan Dini')->count();

            $persentase = $jumlahPernikahan > 0
                ? ($jumlahPernikahanDini / $jumlahPernikahan) * 100
                : 0;

            $resiko = match (true) {
                $persentase <= 20 => 'rendah',
                $persentase <= 40 => 'sedang',
                default => 'tinggi',
            };

            Resiko_Wilayah::updateOrCreate(
                ['id_wilayah' => $idWilayah, 'periode' => $periode],
                [
                    'jumlah_pernikahan' => $jumlahPernikahan,
                    'jumlah_pernikahan_dini' => $jumlahPernikahanDini,
                    'resiko_wilayah' => $resiko,
                ]
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error pada proses klasifikasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $dataInput,
                'id_pernikahan' => $idPernikahan,
            ]);
            throw $e;
        }
    }

    public function prosesBranch($dataInput, $idPernikahan) {
        try {
            DB::beginTransaction();

            $payload = [
                'umur_suami' => $dataInput['usia_suami'],
                'umur_istri' => $dataInput['usia_istri'],
                'pendidikan_suami' => $dataInput['pendidikan_suami'],
                'pendidikan_istri' => $dataInput['pendidikan_istri'],
                'pekerjaan_suami' => $dataInput['pekerjaan_suami'],
                'pekerjaan_istri' => $dataInput['pekerjaan_istri'],
                'status_suami' => $dataInput['status_suami'],
                'status_istri' => $dataInput['status_istri'],
                'nama_kelurahan' => $dataInput['nama_kelurahan'],
            ];

            $response = Http::post('http://127.0.0.1:5000/predict', $payload);

            if ($response->failed()) {
                throw new \Exception('Request API gagal.');
            }

            $hasil = $response->json();

            if (!isset($hasil['hasil_prediksi']) || !isset($hasil['confidence']) || !isset($hasil['probabilitas'])) {
                throw new \Exception('Response API tidak lengkap.');
            }

            HasilKlasifikasi::create([
                'pernikahan_id' => $idPernikahan,
                'kategori_pernikahan' => $hasil['hasil_prediksi'],
                'confidence' => floatval(preg_replace('/[^0-9.]/', '', $hasil['confidence'])),
                'probabilitas' => json_encode($hasil['probabilitas']),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error pada proses klasifikasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $dataInput,
                'id_pernikahan' => $idPernikahan,
            ]);
            throw $e;
        }
    }
}
