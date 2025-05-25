@extends('layouts.app')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Data Hasil Klasifikasi</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="{{ route('hasil_klasifikasi.index') }}">Data Hasil Klasifikasi</a></li>
                </ul>
            </div>

            <div class="card-header mt-4 px-0 border-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    {{-- Tombol Aksi --}}

                    <div class="d-flex flex-wrap gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-round dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('hasil_klasifikasi.exportExcel', request()->only(['search', 'filter_kelurahan', 'filter_tahun'])) }}" class="dropdown-item">Export Excel (.xlsx)</a>
                                </li>
                                <li>
                                    <a href="{{ route('hasil_klasifikasi.exportCsv', request()->only(['search', 'filter_kelurahan', 'filter_tahun'])) }}" class="dropdown-item">Export Excel (.csv)</a>
                                </li>
                                <li>
                                    <a href="{{ route('hasil_klasifikasi.exportPdf', request()->only(['search', 'filter_kelurahan', 'filter_tahun'])) }}" class="dropdown-item">Export PDF</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Search & Filter --}}
                    <div class="d-flex flex-wrap gap-2">
                        {{-- Form Search --}}
                        <form method="GET" action="{{ route('hasil_klasifikasi.index') }}" class="d-flex align-items-center gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama kelurahan..." value="{{ request()->search }}">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </form>

                        {{-- Form Filter --}}
                        <form method="GET" action="{{ route('hasil_klasifikasi.index') }}" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="search" value="{{ request()->search }}">

                            {{-- Filter Kelurahan --}}
                            <select name="filter_kelurahan" class="form-control">
                                <option value="">Filter by Kelurahan</option>
                                @foreach ($kelurahans as $kelurahan)
                                    <option value="{{ $kelurahan->desa }}" {{ request()->filter_kelurahan == $kelurahan->desa ? 'selected' : '' }}>
                                        {{ $kelurahan->desa }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Filter Tahun --}}
                            <select name="filter_tahun" class="form-control">
                                <option value="">Filter by Tahun</option>
                                @foreach ($tahun as $th)
                                    <option value="{{ $th->tahun }}" {{ request()->filter_tahun == $th->tahun ? 'selected' : '' }}>
                                        {{ $th->tahun }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Filter Kategori (Resiko Wilayah) --}}
                            <select name="filter_resiko" class="form-control">
                                <option value="">Filter by Kategori</option>
                                @foreach ($kategori as $k)
                                    <option value="{{ $k->kategori_pernikahan }}" {{ request()->filter_resiko == $k->kategori_pernikahan ? 'selected' : '' }}>
                                        {{ ucfirst($k->kategori_pernikahan) }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>

                        {{-- Reset Filter --}}
                        <a href="{{ route('data_klasifikasi.index') }}" class="btn btn-secondary">Reset Filter</a>
                    </div>

                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive mt-3">
                <table class="display table table-striped table-hover" id="basic-datatables">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Desa/Kelurahan</th>
                            <th>Jumlah Pernikahan</th>
                            <th>Jumlah Pernikahan Dini</th>
                            <th>Resiko Wilayah</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $i => $dt)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $dt->wilayah->desa ?? 'Tidak diketahui' }}</td>
                            <td>{{ $dt->jumlah_pernikahan ?? '0' }}</td>
                            <td>{{ $dt->jumlah_pernikahan_dini ?? '0' }}</td>
                            <td>{{ ucfirst($dt->resiko_wilayah?? 'Tidak diketahui') }}</td>
                            <td>
                                <div class="form-button-action">
                                    <a href="{{ route('hasil_klasifikasi.detail_hasil', $dt->wilayah->id) }}" class="btn btn-link btn-info">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <form id="form-delete" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>

        </div>
    </div>

@endsection
