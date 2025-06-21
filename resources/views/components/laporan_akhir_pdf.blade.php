<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Akhir</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2, h4 { text-align: center; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: right; font-size: 11px; }
    </style>
</head>
<body>

    <h2>LAPORAN AKHIR ANALISIS PERNIKAHAN DINI</h2>
    <h4>Periode: {{ $tahun ?? 'Semua Tahun' }} | Kategori Wilayah: {{ $kategori ? ucfirst($kategori) : 'Semua' }}</h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Wilayah</th>
                <th>Resiko</th>
                <th>Jumlah Pernikahan Dini</th>
                <th>Rata Usia Suami</th>
                <th>Rata Usia Istri</th>
                <th>Pendidikan Suami</th>
                <th>Pendidikan Istri</th>
                <th>Rekomendasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data['wilayah'] }}</td>
                    <td>{{ $data['resiko'] }}</td>
                    <td>{{ $data['jumlah_pernikahan_dini'] }}</td>
                    <td>{{ $data['rata_usia_suami'] }}</td>
                    <td>{{ $data['rata_usia_istri'] }}</td>
                    <td>{{ $data['rata_pendidikan_suami'] }}</td>
                    <td>{{ $data['rata_pendidikan_istri'] }}</td>
                    <td>{{ $data['rekomendasi'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>

</body>
</html>
