@extends('layouts.app')

@section('content')
<style>
    /* Match button heights with form controls */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        height: 31px;
        line-height: 1.5;
    }

    .form-control-sm {
        height: 31px;
    }

    /* Ensure dropdown button matches other buttons */
    .dropdown .btn-sm {
        display: inline-flex;
        align-items: center;
    }
</style>

<div class="col-md-12">
    <div class="card">

        <div class="card-body">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Laporan Statistik</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Laporan Statistik</a></li>
                </ul>
            </div>

            <div class="card-header px-0 border-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                     <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-round dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('laporan.exportExcel', request()->only(['search','kategori_wilayah', 'wilayah_id', 'tahun'])) }}" class="dropdown-item">Export Excel (.xlsx)</a>
                                </li>
                                <li>
                                    <a href="{{ route('laporan.exportCsv', request()->only(['search','kategori_wilayah', 'wilayah_id', 'tahun'])) }}" class="dropdown-item">Export Excel (.csv)</a>
                                </li>
                                <li>
                                    <a href="{{ route('laporan.exportPdf', request()->only(['search','kategori_wilayah', 'wilayah_id', 'tahun'])) }}" class="dropdown-item" target="_blank">Export PDF</a>
                                </li>
                            </ul>
                        </div>

                    {{-- Bagian kanan: Filter --}}
                    <div class="d-flex flex-wrap gap-2">
                    <div class="d-flex flex-wrap align-items-center gap-2">
    <form method="GET" action="{{ route('laporan.statistik') }}" class="d-flex align-items-center gap-2">
        <select name="wilayah_id" class="form-control">
            <option value="">Filter Wilayah</option>
            @foreach ($daftarWilayah as $w)
                <option value="{{ $w->id }}" {{ request('wilayah_id') == $w->id ? 'selected' : '' }}>
                    {{ Str::ucfirst(Str::lower($w->desa)) }} - {{ Str::ucfirst(Str::lower($w->kecamatan)) }}
                </option>
            @endforeach
        </select>

        <select name="tahun" class="form-control">
            <option value="">Filter Tahun</option>
            @foreach ($daftarTahun as $th)
                <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>
                    {{ $th }}
                </option>
            @endforeach
        </select>

        <select name="kategori_wilayah" class="form-control">
            <option value="">Filter Kategori Wilayah</option>
            @foreach ($kategoriWilayah as $kw )
            <option value="{{ $kw->resiko_wilayah }}" {{ request('kategori_wilayah') == $kw->resiko_wilayah ? 'selected' : '' }}">
                {{ ucfirst($kw->resiko_wilayah) }}
            </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">
            Filter
        </button>
    </form>

    <a href="{{ route('laporan.statistik') }}" class="btn btn-secondary">
        <i class="fas fa-sync"></i> Reset
    </a>
</div>


                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h4 class="fw-bold">Statistik Wilayah</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
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
                                    <td>{{ Str::ucfirst(Str::lower($data->desa)) }}, {{ Str::ucfirst(Str::lower($data->kecamatan)) }}</td>
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
                </div>
            </div>

            <div class="mt-5">
                <h4 class="fw-bold">Statistik Kategori Pernikahan</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
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
                </div>
            </div>

            <div class="mt-5">
    <h4 class="fw-bold">Distribusi Usia Pernikahan Dini</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
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
    </div>
</div>

<div class="mt-5">
    <h4 class="fw-bold">Statistik Pendidikan Pelaku Pernikahan Dini</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
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
    </div>
</div>

<div class="mt-5">
    <h4 class="fw-bold">Kasus Pernikahan Dini Berdasarkan Gender</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
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
    </div>
</div>
</div>
</div>
@endsection
