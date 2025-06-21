@extends('layouts.app')

@section('content')
<div class="page-inner">
    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Laporan Akhir Rekap Risiko Wilayah</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="{{ route('laporan.laporan_akhir') }}">Laporan Akhir</a></li>
                </ul>
            </div>

            <div class="card-header mt-4 px-0 border-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

                    {{-- Export Dropdown --}}
                   <div class="d-flex flex-wrap gap-2">
    <a class="btn btn-outline-secondary btn-round"
       href="{{ route('laporan.print', request()->only(['tahun', 'kategori_wilayah'])) }}"
       target="_blank">
        <i class="fa fa-download"></i> Cetak Laporan
    </a>
</div>

                    {{-- Filter Form --}}
                    <div class="d-flex flex-wrap gap-2">
                        <form action="{{ route('laporan.laporan_akhir') }}" method="GET" class="d-flex align-items-center gap-2">

                            <select name="tahun" class="form-control">
                                <option value="">Filter by Tahun</option>
                                @foreach($daftarTahun ?? [] as $th)
                                    <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>{{ $th }}</option>
                                @endforeach
                            </select>

                            <select name="kategori_wilayah" class="form-control">
                                <option value="">Filter by Risiko</option>
                                <option value="Tinggi" {{ request('kategori_wilayah') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                                <option value="Sedang" {{ request('kategori_wilayah') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="Rendah" {{ request('kategori_wilayah') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                            </select>

                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>

                        <a href="{{ route('laporan.laporan_akhir') }}" class="btn btn-secondary">Reset Filter</a>
                    </div>

                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive mt-3">
                @if(count($rekap) > 0)
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Wilayah</th>
                                <th>Tingkat Risiko</th>
                                <th>Jumlah Pernikahan Dini</th>
                                <th>Rata-Rata Usia Suami</th>
                                <th>Rata-Rata Usia Istri</th>
                                <th>Rata-Rata Pendidikan Suami</th>
                                <th>Rata-Rata Pendidikan Istri</th>
                                <th>Rata-Rata Pekerjaan Suami</th>
                                <th>Rata-Rata Pekerjaan Istri</th>
                                <th>Rekomendasi Penyuluhan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekap as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ ucfirst($item['wilayah']) }}</td>
                                    <td>{{ ucfirst($item['resiko']) }}</td>
                                    <td>{{ $item['jumlah_pernikahan_dini'] }}</td>
                                    <td>{{ $item['rata_usia_suami'] ?? '-' }}</td>
                                    <td>{{ $item['rata_usia_istri'] ?? '-' }}</td>
                                    <td>{{ $item['rata_pendidikan_suami'] ?? '-' }}</td>
                                    <td>{{ $item['rata_pendidikan_istri'] ?? '-' }}</td>
                                    <td>{{ $item['rata_pekerjaan_suami'] ?? '-' }}</td>
                                    <td>{{ $item['rata_pekerjaan_istri'] ?? '-' }}</td>
                                    <td>{{ $item['rekomendasi'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info">Data laporan tidak ditemukan untuk filter yang dipilih.</div>
                @endif
            </div>
        </div>
    </div>

@endsection
