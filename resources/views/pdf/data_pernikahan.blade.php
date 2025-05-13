<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pernikahan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .table-header {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .no-border {
            border: none;
        }
    </style>
</head>
<body>

    <h1>Data Pernikahan</h1>

    <!-- Filter Information Section -->
    <table class="no-border" style="width: 100%; margin-bottom: 10px;">
        <tr class="no-border">
            <td class="no-border" style="width: 50%;">{{ isset($filter_tahun) ? 'Tahun: ' . $filter_tahun : 'Tahun: -' }}</td>
            <td class="no-border" style="width: 50%;">{{ isset($filter_kelurahan) ? 'Kelurahan: ' . $filter_kelurahan : 'Kelurahan: -' }}</td>
        </tr>
        <tr class="no-border">
            <td class="no-border" style="width: 50%;">{{ isset($search) ? 'Search: ' . $search : 'Search: -' }}</td>
        </tr>
    </table>

    <!-- Table of Data -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Suami</th>
                <th>Nama Istri</th>
                <th>Tanggal Lahir Suami</th>
                <th>Tanggal Lahir Istri</th>
                <th>Usia Suami</th>
                <th>Usia Istri</th>
                <th>Pendidikan Suami</th>
                <th>Pendidikan Istri</th>
                <th>Pekerjaan Suami</th>
                <th>Pekerjaan Istri</th>
                <th>Status Suami</th>
                <th>Status Istri</th>
                <th>Nama Kelurahan</th>
                <th>Tanggal Akad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $pernikahan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $pernikahan->nama_suami }}</td>
                    <td>{{ $pernikahan->nama_istri }}</td>
                    <td>{{ \Carbon\Carbon::parse($pernikahan->tanggal_lahir_suami)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($pernikahan->tanggal_lahir_istri)->format('d M Y') }}</td>
                    <td>{{ $pernikahan->usia_suami }}</td>
                    <td>{{ $pernikahan->usia_istri }}</td>
                    <td>{{ $pernikahan->pendidikan_suami }}</td>
                    <td>{{ $pernikahan->pendidikan_istri }}</td>
                    <td>{{ $pernikahan->pekerjaan_suami }}</td>
                    <td>{{ $pernikahan->pekerjaan_istri }}</td>
                    <td>{{ $pernikahan->status_suami }}</td>
                    <td>{{ $pernikahan->status_istri }}</td>
                    <td>{{ $pernikahan->wilayah->desa ?? 'Tidak diketahui' }}</td>
                    <td>{{ \Carbon\Carbon::parse($pernikahan->tanggal_akad)->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
