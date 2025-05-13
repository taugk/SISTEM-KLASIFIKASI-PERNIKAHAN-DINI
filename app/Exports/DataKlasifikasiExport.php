<?php

namespace App\Exports;

use App\Models\HasilKlasifikasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class DataKlasifikasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, WithTitle
{
    protected $filter;

    public function __construct(array $filter = [])
    {
        $this->filter = $filter;
    }

    public function collection()
    {
        $query = HasilKlasifikasi::with(['pernikahan.wilayah', 'pernikahan'])
            ->whereNotNull('id_pernikahan');

        if (!empty($this->filter['kelurahan'])) {
            $query->whereHas('pernikahan.wilayah', function($q) {
                $q->where('desa', $this->filter['kelurahan']);
            });
        }

        if (!empty($this->filter['tahun'])) {
            $query->whereYear('created_at', $this->filter['tahun']);
        }

        if (!empty($this->filter['kategori'])) {
            $query->where('kategori_pernikahan', $this->filter['kategori']);
        }

        return $query->get();
    }

    public function map($klasifikasi): array
    {
        return [
            $klasifikasi->id,
            $klasifikasi->pernikahan->nama_suami ?? '-',
            $klasifikasi->pernikahan->nama_istri ?? '-',
            $klasifikasi->pernikahan->usia_suami ?? '-',
            $klasifikasi->pernikahan->usia_istri ?? '-',
            $klasifikasi->pernikahan->wilayah->desa ?? '-',
            $klasifikasi->kategori_pernikahan ?? '-',
            $klasifikasi->confidence ? $klasifikasi->confidence . '%' : '-',
            $klasifikasi->created_at ? $klasifikasi->created_at->format('d-m-Y H:i') : '-',
            $klasifikasi->faktor_dominan ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Suami',
            'Nama Istri',
            'Usia Suami',
            'Usia Istri',
            'Kelurahan',
            'Hasil Klasifikasi',
            'Tingkat Keyakinan',
            'Tanggal Klasifikasi',
            'Faktor Dominan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set lebar kolom
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(30);

        return [
            3 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => Color::COLOR_WHITE]
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Freeze header row
                $event->sheet->freezePane('A4');

                // Tambahkan judul laporan
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'LAPORAN DATA KLASIFIKASI PERNIKAHAN');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '000000']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);

                // Tambahkan info filter
                $filterInfo = 'Data yang ditampilkan: ';
                if (!empty($this->filter['kelurahan'])) {
                    $filterInfo .= 'Kelurahan ' . $this->filter['kelurahan'] . ' ';
                }
                if (!empty($this->filter['tahun'])) {
                    $filterInfo .= 'Tahun ' . $this->filter['tahun'] . ' ';
                }
                if (!empty($this->filter['kategori'])) {
                    $filterInfo .= 'Kategori ' . $this->filter['kategori'] . ' ';
                }

                $sheet->setCellValue('A2', trim($filterInfo) ?: 'Semua Data');
                $sheet->mergeCells('A2:J2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT
                    ]
                ]);

                // Tambahkan border dan alignment semua data
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A3:J' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP
                    ]
                ]);

                // Warnai kolom G berdasarkan isi
                for ($row = 4; $row <= $highestRow; $row++) {
                    $kategori = $sheet->getCell('G' . $row)->getValue();
                    if ($kategori === 'Pernikahan Dini') {
                        $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('FF0000');
                    } else {
                        $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('007700');
                    }
                }

                // Aktifkan auto filter
                $sheet->setAutoFilter('A3:J3');
            }
        ];
    }

    public function title(): string
    {
        return 'Data Klasifikasi';
    }
}
