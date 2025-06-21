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
            if (!isset($hasil['hasil_prediksi'], $hasil['confidence'], $hasil['probabilitas'], $hasil['penyebab'], $hasil['accuracy'], $hasil['dampak'])) {
                throw new \Exception('Response API tidak lengkap.');
            }

            if($hasil['hasil_prediksi'] === 1) {
                $hasil['hasil_prediksi'] = 'Pernikahan Dini';
            } elseif ($hasil['hasil_prediksi'] === 0) {
                $hasil['hasil_prediksi'] = 'Bukan Pernikahan Dini';
            } else {
                throw new \Exception('Hasil prediksi tidak valid.');
            }

            HasilKlasifikasi::updateOrCreate([
                'id_pernikahan' => $idPernikahan,
                'kategori_pernikahan' => $hasil['hasil_prediksi'],
                'confidence' => floatval(preg_replace('/[^0-9.]/', '', $hasil['confidence'])),
                'probabilitas' => json_encode($hasil['probabilitas']),
                'akurasi' => is_numeric($hasil['accuracy'] ?? null) ? floatval($hasil['accuracy']) : null,
                'penyebab' => json_encode($hasil['penyebab']),
                'dampak' => json_encode($hasil['dampak'] ?? []),
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

                $rasio_pernikahan_dini = $jumlahPernikahan > 0
                ? ($jumlahPernikahanDini / $jumlahPernikahan) * 100
                : 0;
            // Tentukan kategori risiko berdasarkan persentase
            if ($rasio_pernikahan_dini < 20) {
                $resiko = 'rendah';
            } elseif ($rasio_pernikahan_dini < 40) {
                $resiko = 'sedang';
            } else {
                $resiko = 'tinggi';
            }

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

    public function prosesDanSimpanBatch(array $dataBatch): void
{
    DB::beginTransaction();

    try {
        $payload = [];
        $mapping = [];

        foreach ($dataBatch as $item) {
            if (empty($item['wilayah_id']) || empty($item['id'])) {
                throw new \InvalidArgumentException("Data batch tidak lengkap. 'wilayah_id' atau 'id' kosong.");
            }

            $wilayah = DataWilayah::find($item['wilayah_id']);
            if (!$wilayah) {
                throw new \Exception("Wilayah dengan ID {$item['wilayah_id']} tidak ditemukan.");
            }

            $payload[] = [
                'umur_suami' => $item['usia_suami'],
                'umur_istri' => $item['usia_istri'],
                'pendidikan_suami' => $item['pendidikan_suami'],
                'pendidikan_istri' => $item['pendidikan_istri'],
                'status_suami' => $item['status_suami'],
                'status_istri' => $item['status_istri'],
                'nama_kelurahan' => $wilayah->desa,
                'pekerjaan_suami' => $item['pekerjaan_suami'],
                'pekerjaan_istri' => $item['pekerjaan_istri'],
            ];


            $mapping[] = [
                'id_pernikahan' => $item['id'],
                'wilayah_id' => $item['wilayah_id'],
            ];
        }

        $response = Http::timeout(120)->post('http://127.0.0.1:5000/predict-batch', [
            'data' => $payload
        ]);

        if (!$response->successful()) {
            throw new \Exception("Gagal menghubungi API klasifikasi batch. Status HTTP: " . $response->status());

        }

        $hasilBatch = $response->json();

        if (!isset($hasilBatch['results']) || !is_array($hasilBatch['results'])) {
            throw new \Exception("Respons API tidak valid. Key 'results' tidak ditemukan atau bukan array.");
        }

        if (count($hasilBatch['results']) !== count($mapping)) {
            throw new \Exception("Jumlah hasil klasifikasi (" . count($hasilBatch['results']) . ") tidak sesuai dengan jumlah input (" . count($mapping) . ").");
        }

        foreach ($hasilBatch['results'] as $i => $hasil) {
            $prediksi = $hasil['hasil_prediksi'] ?? '-';
            if ($prediksi === 1) {
                $prediksi = 'Pernikahan Dini';
            } elseif ($prediksi === 0) {
                $prediksi = 'Bukan Pernikahan Dini';
            }

            $confidence = isset($hasil['confidence'])
                ? floatval(preg_replace('/[^0-9.]/', '', $hasil['confidence']))
                : 0.0;

            HasilKlasifikasi::updateOrCreate(
                ['id_pernikahan' => $mapping[$i]['id_pernikahan']],
                [
                    'kategori_pernikahan' => $prediksi,
                    'confidence' => $confidence,
                    'probabilitas' => json_encode($hasil['probabilitas'] ?? []),
                    'akurasi' => is_numeric($hasil['accuracy'] ?? null) ? floatval($hasil['accuracy']) : null,
                    'penyebab' => json_encode($hasil['penyebab'] ?? null),
                    'dampak' => json_encode($hasil['dampak'] ?? null),
                ]
            );
        }

        // Perhitungan risiko wilayah
        $kelompokWilayah = collect($mapping)->groupBy('wilayah_id');
        $tanggalMax = DataPernikahan::max('tanggal_akad');

        if (!$tanggalMax) {
            throw new \Exception('Data pernikahan tidak ditemukan untuk menentukan periode.');
        }

        $tahun = date('Y', strtotime($tanggalMax));
        $periode = "{$tahun}-01-01";

        foreach ($kelompokWilayah as $idWilayah => $items) {
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

                $rasio_pernikahan_dini = $jumlahPernikahan > 0
                ? ($jumlahPernikahanDini / $jumlahPernikahan) * 100
                : 0;

                if ($rasio_pernikahan_dini < 20) {
                    $resiko = 'rendah';
                } elseif ($rasio_pernikahan_dini < 40) {
                    $resiko = 'sedang';
                } else {
                    $resiko = 'tinggi';
                }

            Resiko_Wilayah::updateOrCreate(
                ['id_wilayah' => $idWilayah, 'periode' => $periode],
                [
                    'jumlah_pernikahan' => $jumlahPernikahan,
                    'jumlah_pernikahan_dini' => $jumlahPernikahanDini,
                    'resiko_wilayah' => $resiko,
                ]
            );
        }

        Log::info("Batch klasifikasi berhasil diproses untuk " . count($dataBatch) . " entri.");
        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error("Terjadi kesalahan saat proses batch klasifikasi:", [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        throw $e;
    }
}

}
