<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\DataPernikahan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataPernikahanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tahun;
    protected $kelurahan;
    protected $search;

    // Menerima parameter untuk filtering
    public function __construct($tahun = null, $kelurahan = null, $search = null)
    {
        $this->tahun = $tahun;
        $this->kelurahan = $kelurahan;
        $this->search = $search;
    }

    public function collection()
{
    $query = DataPernikahan::with('wilayah');

    if ($this->tahun !== null) {
        $query->whereYear('tanggal_akad', $this->tahun);
    }

    if ($this->kelurahan !== null) {
        $query->whereHas('wilayah', function ($q) {
            $q->where('desa', $this->kelurahan);
        });
    }

    if (!is_null($this->search) && $this->search !== '') {
        $search = $this->search;
        $query->where(function ($q) use ($search) {
            $q->where('nama_suami', 'like', "%$search%")
              ->orWhere('nama_istri', 'like', "%$search%")
              ->orWhereHas('wilayah', function ($w) use ($search) {
                  $w->where('desa', 'like', "%$search%");
              });
        });
    }

    return $query->get();
}


    public function map($data): array
    {
        static $no = 1;
        return [
            $no++,
            $data->nama_suami,
            $data->nama_istri,
            Carbon::parse($data->tanggal_lahir_suami)->format('d M Y'),
            Carbon::parse($data->tanggal_lahir_istri)->format('d M Y'),
            $data->usia_suami,
            $data->usia_istri,
            $data->pendidikan_suami,
            $data->pendidikan_istri,
            $data->pekerjaan_suami,
            $data->pekerjaan_istri,
            $data->status_suami,
            $data->status_istri,
            $data->wilayah->desa ?? 'Tidak diketahui',
            Carbon::parse($data->tanggal_akad)->format('d M Y'),
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Suami',
            'Nama Istri',
            'Tanggal Lahir Suami',
            'Tanggal Lahir Istri',
            'Usia Suami',
            'Usia Istri',
            'Pendidikan Suami',
            'Pendidikan Istri',
            'Pekerjaan Suami',
            'Pekerjaan Istri',
            'Status Suami',
            'Status Istri',
            'Nama Kelurahan',
            'Tanggal Akad',
        ];
    }
}
