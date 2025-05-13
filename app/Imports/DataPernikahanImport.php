<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\DataWilayah;
use App\Models\DataPernikahan;
use App\Services\KlasifikasiService;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataPernikahanImport implements ToModel, WithHeadingRow
{
    protected $klasifikasiService;

    public function __construct()
    {
        $this->klasifikasiService = new KlasifikasiService();
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            return null; // Abaikan baris kosong
        }

        $tanggalLahirSuami = $this->parseTanggal($row['tanggal_lahir_suami']);
        $tanggalLahirIstri = $this->parseTanggal($row['tanggal_lahir_istri']);
        $pendidikanSuami = $this->validatePendidikan($row['pendidikan_suami'] ?? '');
        $pendidikanIstri = $this->validatePendidikan($row['pendidikan_istri'] ?? '');

        $wilayahId = DataWilayah::where('desa', $row['nama_kelurahan'])->value('id');
        if (!$wilayahId) {
            $wilayah = DataWilayah::create([
                'provinsi' => 'Jawa Barat',
                'kabupaten' => 'Kuningan',
                'kecamatan' => 'Kuningan',
                'desa' => $row['nama_kelurahan'],
            ]);
            $wilayahId = $wilayah->id;

            Log::info("Wilayah baru ditambahkan: " . $row['nama_kelurahan']);
        }

        $dataPernikahan = DataPernikahan::create([
            'nama_suami' => $row['nama_suami'],
            'nama_istri' => $row['nama_istri'],
            'tanggal_lahir_suami' => $tanggalLahirSuami,
            'tanggal_lahir_istri' => $tanggalLahirIstri,
            'usia_suami' => Carbon::parse($tanggalLahirSuami)->age,
            'usia_istri' => Carbon::parse($tanggalLahirIstri)->age,
            'pendidikan_suami' => $pendidikanSuami,
            'pendidikan_istri' => $pendidikanIstri,
            'pekerjaan_suami' => $row['pekerjaan_suami'],
            'pekerjaan_istri' => $row['pekerjaan_istri'],
            'status_suami' => $row['status_suami'],
            'status_istri' => $row['status_istri'],
            'tanggal_akad' => $this->parseTanggal($row['tanggal_akad']),
            'wilayah_id' => $wilayahId,
        ]);

        try {
            $this->klasifikasiService->prosesDanSimpan([
                'usia_suami' => $dataPernikahan->usia_suami,
                'usia_istri' => $dataPernikahan->usia_istri,
                'pendidikan_suami' => $dataPernikahan->pendidikan_suami,
                'pendidikan_istri' => $dataPernikahan->pendidikan_istri,
                'pekerjaan_suami' => $dataPernikahan->pekerjaan_suami,
                'pekerjaan_istri' => $dataPernikahan->pekerjaan_istri,
                'status_suami' => $dataPernikahan->status_suami,
                'status_istri' => $dataPernikahan->status_istri,
                'wilayah_id' => $dataPernikahan->wilayah_id,
            ], $dataPernikahan->id);
        } catch (\Exception $e) {
            Log::error('Gagal proses klasifikasi: ' . $e->getMessage());
        }

        return $dataPernikahan;
    }

    /**
     * Validasi dan mapping pendidikan.
     */
    protected function validatePendidikan($value)
    {
        $map = [
            'TIDAK/BELUM SEKOLAH' => 'TIDAK/BELUM SEKOLAH',
            'TIDAK TAMAT SD/SEDERAJAT' => 'TIDAK TAMAT SD/SEDERAJAT',
            'TAMAT SD/SEDERAJAT' => 'TAMAT SD/SEDERAJAT',
            'SLTP/SEDERAJAT' => 'SLTP/SEDERAJAT',
            'SLTA/SEDERAJAT' => 'SLTA/SEDERAJAT',
            'DIPLOMA I/II' => 'DIPLOMA I/II',
            'AKADEMI/DIPLOMA III/S. MUDA' => 'AKADEMI/DIPLOMA III/S. MUDA',
            'DIPLOMA IV/STRATA I' => 'DIPLOMA IV/STRATA I',
            'STRATA II' => 'STRATA II',
            'STRATA III' => 'STRATA III',
        ];

        $value = strtoupper(trim($value));
        return $map[$value] ?? 'TIDAK/BELUM SEKOLAH';
    }

    /**
     * Parsing tanggal dari Excel (baik format string maupun numeric).
     */
    protected function parseTanggal($value)
    {
        try {
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value))->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Format tanggal tidak valid: ' . $value);
            return null;
        }
    }
}
