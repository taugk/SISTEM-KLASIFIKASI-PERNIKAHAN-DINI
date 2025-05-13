<?php

namespace App\Exports;

use App\Models\Resiko_Wilayah;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HasilKlasifikasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, WithTitle
{
    protected $request;
    protected $data;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Resiko_Wilayah::with('wilayah');

        if ($this->request) {
            if (!empty($this->request['search'])) {
                $query->where(function ($q) {
                    $q->where('nama_suami', 'like', '%' . $this->request['search'] . '%')
                      ->orWhere('nama_istri', 'like', '%' . $this->request['search'] . '%');
                });
            }

            if (!empty($this->request['filter_kelurahan'])) {
                $query->whereHas('wilayah', function ($q) {
                    $q->where('desa', $this->request['filter_kelurahan']);
                });
            }

            if (!empty($this->request['filter_tahun'])) {
                $query->where('periode', $this->request['filter_tahun']);
            }

            if (!empty($this->request['filter_resiko'])) {
                $query->where('resiko_wilayah', $this->request['filter_resiko']);
            }
        }

        $this->data = $query->get()->unique(function ($item) {
            return $item->wilayah->desa;
        });

        return $this->data;
    }

    public function map($item): array
    {
        return [
            $item->wilayah->desa ?? '-',
            $item->jumlah_pernikahan ?? 0,
            $item->jumlah_pernikahan_dini ?? 0,
            ucfirst($item->resiko_wilayah ?? '-'),
        ];
    }

    public function headings(): array
    {
        return [
            'Kelurahan/Desa',
            'Jumlah Pernikahan',
            'Jumlah Pernikahan Dini',
            'Resiko Wilayah',
        ];
    }

    public function styles(Worksheet $sheet)
{
    $sheet->getColumnDimension('A')->setWidth(25);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(25);
    $sheet->getColumnDimension('D')->setWidth(20);

    return [];
}


    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Freeze header
                $event->sheet->freezePane('A4');

                // Judul
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'LAPORAN DATA PERNIKAHAN BERDASARKAN RESIKO');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Deskripsi filter
                $filterInfo = 'Data yang ditampilkan: ';
                if (!empty($this->request['filter_kelurahan'])) {
                    $filterInfo .= 'Kelurahan ' . $this->request['filter_kelurahan'] . ' ';
                }
                if (!empty($this->request['filter_tahun'])) {
                    $filterInfo .= 'Tahun ' . $this->request['filter_tahun'] . ' ';
                }
                if (!empty($this->request['filter_resiko'])) {
                    $filterInfo .= 'Resiko ' . ucfirst($this->request['filter_resiko']) . ' ';
                }
                if (!empty($this->request['search'])) {
                    $filterInfo .= '(Pencarian: ' . $this->request['search'] . ')';
                }

                $sheet->setCellValue('A2', trim($filterInfo) ?: 'Semua Data');
                $sheet->mergeCells('A2:D2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
            },
        ];
    }

    public function title(): string
    {
        return 'Rekap Pernikahan';
    }
}
