<?php

namespace App\Services;

use App\Models\DataWilayah;
use App\Models\DataPernikahan;
use App\Models\Resiko_Wilayah;
use App\Models\HasilKlasifikasi;
use Illuminate\Support\Facades\Http;

class KlasifikasiService
{
    public function prosesDanSimpan(array $dataInput, int $idPernikahan): void
    {
        $wilayah = DataWilayah::find($dataInput['wilayah_id']);
        if (!$wilayah) {
            throw new \Exception('Wilayah tidak ditemukan.');
        }

        // Siapkan data ke API
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

        // Kirim ke API
        $response = Http::post('http://127.0.0.1:5000/predict', $payload);

        if (!$response->successful()) {
            throw new \Exception('Gagal menghubungi API klasifikasi.');
        }

        $hasil = $response->json();

        HasilKlasifikasi::create([
            'id_pernikahan' => $idPernikahan,
            'kategori_pernikahan' => $hasil['hasil_prediksi'],
            'confidence' => floatval(preg_replace('/[^0-9.]/', '', $hasil['confidence'])),
            'probabilitas' => json_encode($hasil['probabilitas']),
        ]);

       // Hitung risiko wilayah
$periode = DataPernikahan::max('tanggal_akad');
$idWilayah = $dataInput['wilayah_id'];

$jumlahPernikahan = DataPernikahan::where('wilayah_id', $idWilayah)
    ->whereYear('tanggal_akad', $periode)
    ->count();

$jumlahPernikahanDini = HasilKlasifikasi::whereHas('pernikahan', function ($query) use ($idWilayah, $periode) {
    $query->where('wilayah_id', $idWilayah)
          ->whereYear('tanggal_akad', $periode);
})->where('kategori_pernikahan', 'Pernikahan Dini')->count();

$persentase = $jumlahPernikahan != 0
    ? ($jumlahPernikahanDini / $jumlahPernikahan) * 100
    : 0;

$resiko = match (true) {
    $persentase <= 20 => 'rendah',
    $persentase <= 40 => 'sedang',
    default => 'tinggi',
};

// Gunakan updateOrCreate untuk memperbarui atau menambah data
Resiko_Wilayah::updateOrCreate(
    ['id_wilayah' => $idWilayah, 'periode' => $periode], // Kondisi pencarian
    [
        'jumlah_pernikahan' => $jumlahPernikahan,
        'jumlah_pernikahan_dini' => $jumlahPernikahanDini,
        'resiko_wilayah' => $resiko,
    ]
);

    }
}
