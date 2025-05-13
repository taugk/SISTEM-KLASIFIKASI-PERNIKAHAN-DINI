<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2, p {
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .keterangan {
            margin: 10px 0;
            font-style: italic;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>

    <h2>{{ $judul ?? 'Laporan Data' }}</h2>

    @if(isset($keterangan))
    <p class="keterangan">
        {!! $keterangan !!}
    </p>
    @endif

    <table>
        <thead>
            <tr>
                @foreach($kolom as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    @foreach($kolom as $key => $value)
                        <td>{{ $row[$key] ?? '-' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($kolom) }}">Data tidak tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
