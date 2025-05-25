<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Statistik</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h3, h4 { margin-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 6px; text-align: left; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <h3>Laporan Statistik</h3>

    {{-- Statistik Wilayah --}}
    <h4>Statistik Wilayah</h4>
    <table>
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
                <tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Statistik Kategori --}}
    <h4>Statistik Kategori Pernikahan</h4>
    <table>
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
                <tr><td colspan="3" class="text-center">Data tidak ditemukan</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Statistik Usia --}}
    <h4>Distribusi Usia Pernikahan Dini</h4>
    <table>
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

    {{-- Statistik Pendidikan --}}
    <h4>Statistik Pendidikan Pelaku Pernikahan Dini</h4>
    <table>
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
                <tr><td colspan="3" class="text-center">Data tidak ditemukan</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Statistik Gender --}}
    <h4>Kasus Pernikahan Dini Berdasarkan Gender</h4>
    <table>
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

</body>
</html>
