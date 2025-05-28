<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\DataWilayah;
use App\Models\DataPernikahan;
use App\Services\KlasifikasiService;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DataPernikahanImport implements ToCollection, WithHeadingRow
{
    protected $klasifikasiService;

    public function __construct()
    {
        HeadingRowFormatter::default('none');
        $this->klasifikasiService = new KlasifikasiService();
    }

    public function collection(Collection $rows)
    {
        $batchKlasifikasi = [];
        $total = 0;
        $diproses = 0;

        foreach ($rows as $row) {
            $total++;

            if (empty(array_filter($row->toArray()))) {
                continue;
            }


            $namaKelurahan = isset($row['Nama Kelurahan']) ? trim($row['Nama Kelurahan']) : null;

            if (!$this->isValidNamaKelurahan($namaKelurahan)) {
                Log::warning('Lewatkan baris karena nama kelurahan tidak valid: ' . json_encode($row));
                continue;
            }

            // Validasi umur
            if (!is_numeric($row['Umur Suami'] ?? null) || !is_numeric($row['Umur Istri'] ?? null)) {
                Log::warning('Lewatkan baris karena umur tidak valid: ' . json_encode($row));
                continue;
            }

            $usiaSuami = intval($row['Umur Suami']);
            $usiaIstri = intval($row['Umur Istri']);
            $pendidikanSuami = $this->validatePendidikan($row['Pendidikan Suami'] ?? '');
            $pendidikanIstri = $this->validatePendidikan($row['Pendidikan Istri'] ?? '');

            $wilayahId = DataWilayah::firstOrCreate(
                ['desa' => $namaKelurahan],
                [
                    'provinsi' => 'Jawa Barat',
                    'kabupaten' => 'Kuningan',
                    'kecamatan' => 'Kuningan',
                ]
            )->id;

            $dataPernikahan = DataPernikahan::create([
                'nama_suami' => $row['Nama Suami'] ?? '',
                'nama_istri' => $row['Nama Istri'] ?? '',
                'tanggal_lahir_suami' => $this->parseTanggal($row['Tanggal Lahir Suami'] ?? null),
                'tanggal_lahir_istri' => $this->parseTanggal($row['Tanggal Lahir Istri'] ?? null),
                'usia_suami' => $usiaSuami,
                'usia_istri' => $usiaIstri,
                'pendidikan_suami' => $pendidikanSuami,
                'pendidikan_istri' => $pendidikanIstri,
                'pekerjaan_suami' => $row['Pekerjaan Suami'] ?? '',
                'pekerjaan_istri' => $row['Pekerjaan Istri'] ?? '',
                'status_suami' => $row['Status Suami'] ?? '',
                'status_istri' => $row['Status Istri'] ?? '',
                'tanggal_akad' => $this->parseTanggal($row['Tanggal Akad'] ?? null),
                'wilayah_id' => $wilayahId,
            ]);

            $diproses++;
            Log::info("Data pernikahan berhasil disimpan: ID {$dataPernikahan->id}");

            $batchKlasifikasi[] = [
                'id' => $dataPernikahan->id,
                'usia_suami' => $dataPernikahan->usia_suami,
                'usia_istri' => $dataPernikahan->usia_istri,
                'pendidikan_suami' => $dataPernikahan->pendidikan_suami,
                'pendidikan_istri' => $dataPernikahan->pendidikan_istri,
                'pekerjaan_suami' => $dataPernikahan->pekerjaan_suami,
                'pekerjaan_istri' => $dataPernikahan->pekerjaan_istri,
                'status_suami' => $dataPernikahan->status_suami,
                'status_istri' => $dataPernikahan->status_istri,
                'wilayah_id' => $dataPernikahan->wilayah_id,
            ];
        }

        Log::info("Import selesai. Total: $total, Diproses: $diproses, Untuk klasifikasi: " . count($batchKlasifikasi));

        if (!empty($batchKlasifikasi)) {
            try {
                $this->klasifikasiService->prosesDanSimpanBatch($batchKlasifikasi);
                Log::info("Batch klasifikasi berhasil dikirim dan diproses.");
            } catch (\Exception $e) {
                Log::error('Gagal memproses batch klasifikasi: ' . $e->getMessage());
            }
        } else {
            Log::info('Tidak ada data valid yang diproses untuk klasifikasi.');
        }
    }

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
        if (!isset($map[$value])) {
            Log::warning("Pendidikan tidak dikenal: $value");
        }

        return $map[$value] ?? 'TIDAK/BELUM SEKOLAH';
    }

    protected function parseTanggal($value)
    {
        try {
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value))->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Format tanggal tidak valid: ' . json_encode($value));
            return null;
        }
    }

    protected function isValidNamaKelurahan(?string $nama): bool
    {
        if (empty($nama)) {
            return false;
        }

        $nama = trim($nama);
        return preg_match('/^[a-zA-Z\s\'.-]+$/', $nama) && strlen($nama) <= 50;
    }
}
