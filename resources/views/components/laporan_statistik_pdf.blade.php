<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Statistik Pernikahan Dini</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 11px;
            color: #666;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN STATISTIK PERNIKAHAN DINI</h1>

        <p>Periode: {{ request('tahun') ? request('tahun') : 'Semua Tahun' }}</p>
    </div>

    <div class="section-title">1. Statistik Wilayah</div>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Wilayah</th>
                <th>Jumlah Pernikahan</th>
                <th>Jumlah Pernikahan Dini</th>
                <th>Resiko Wilayah</th>
                <th>Periode</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($statistikWilayah as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data->desa }}, {{ $data->kecamatan }}</td>
                    <td>{{ $data->jumlah_pernikahan }}</td>
                    <td>{{ $data->resiko_wilayah->first()->jumlah_pernikahan_dini ?? 0 }}</td>
                    <td>{{ $data->resiko_wilayah->first()->resiko_wilayah ?? '-' }}</td>
                    <td>{{ $data->resiko_wilayah->first()->periode ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Data tidak ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. Statistik Kategori Pernikahan</div>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori Pernikahan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($statistikKategori as $index => $kategori)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $kategori->kategori_pernikahan }}</td>
                    <td>{{ $kategori->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Data tidak ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">3. Distribusi Usia Pernikahan Dini</div>
    <table class="table">
        <thead>
            <tr>
                <th>Rata-rata Usia Suami</th>
                <th>Rata-rata Usia Istri</th>
                <th>Usia Suami Termuda</th>
                <th>Usia Istri Termuda</th>
                <th>Usia Suami Tertua</th>
                <th>Usia Istri Tertua</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $statistikUsia['avg_suami'] }} tahun</td>
                <td>{{ $statistikUsia['avg_istri'] }} tahun</td>
                <td>{{ $statistikUsia['min_suami'] }} tahun</td>
                <td>{{ $statistikUsia['min_istri'] }} tahun</td>
                <td>{{ $statistikUsia['max_suami'] }} tahun</td>
                <td>{{ $statistikUsia['max_istri'] }} tahun</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">4. Statistik Pendidikan Pelaku Pernikahan Dini</div>
    <table class="table">
        <thead>
            <tr>
                <th>Pendidikan</th>
                <th>Jumlah Suami</th>
                <th>Jumlah Istri</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($statistikPendidikan as $pendidikan)
                <tr>
                    <td>{{ $pendidikan->pendidikan }}</td>
                    <td>{{ $pendidikan->jumlah_suami }}</td>
                    <td>{{ $pendidikan->jumlah_istri }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Data tidak ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">5. Kasus Pernikahan Dini Berdasarkan Gender</div>
    <table class="table">
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah Kasus</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Suami di bawah 19 tahun</td>
                <td>{{ $statistikGender['suami_dini'] }}</td>
            </tr>
            <tr>
                <td>Istri di bawah 19 tahun</td>
                <td>{{ $statistikGender['istri_dini'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>
