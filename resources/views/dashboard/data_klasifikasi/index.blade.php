@extends('layouts.app')

@section('content')
<div class="page-inner">
    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Data Klasifikasi</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="{{ route('data_klasifikasi.index') }}">Data Klasifikasi</a></li>
                </ul>
            </div>

            <div class="card-header px-0 border-0 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-round dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('data_klasifikasi.exportExcel', request()->only('search', 'filter_kelurahan', 'filter_tahun', 'hasil_klasifikasi')) }}" class="dropdown-item">Export Excel (.xlsx)</a>
                                </li>
                                <li>
                                    <a href="{{ route('data_klasifikasi.exportCsv', request()->only('search', 'filter_kelurahan', 'filter_tahun')) }}" class="dropdown-item">Export Excel (.csv)</a>
                                </li>
                                <li>
                                    <a href="{{ route('data_klasifikasi.exportPdf', request()->only('search', 'filter_kelurahan', 'filter_tahun')) }}" class="dropdown-item">Export PDF</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <form method="GET" action="{{ route('data_klasifikasi.index') }}" class="d-flex align-items-center gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama suami/istri..." value="{{ request()->search }}">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </form>

                        <form method="GET" action="{{ route('data_klasifikasi.index') }}" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="search" value="{{ request()->search }}">
                            <select name="filter_kelurahan" class="form-control">
                                <option value="">Filter by Kelurahan</option>
                                @foreach ($kelurahans as $kelurahan)
                                    <option value="{{ $kelurahan->desa }}" {{ request()->filter_kelurahan == $kelurahan->desa ? 'selected' : '' }}>
                                        {{ $kelurahan->desa }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="filter_tahun" class="form-control">
                                <option value="">Filter by Tahun</option>
                                @foreach ($tahun as $th)
                                    <option value="{{ $th->tahun }}" {{ request()->filter_tahun == $th->tahun ? 'selected' : '' }}>
                                        {{ $th->tahun }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="hasil_klasifikasi" class="form-control">
                                <option value="">Filter by Kategori</option>
                                @foreach ($kategori as $k)
                                    <option value="{{ $k->kategori_pernikahan }}" {{ request()->hasil_klasifikasi == $k->kategori_pernikahan ? 'selected' : '' }}>
                                        {{ ucfirst($k->kategori_pernikahan) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>

                        <a href="{{ route('data_klasifikasi.index') }}" class="btn btn-secondary">Reset Filter</a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="display table table-striped table-hover" id="basic-datatables">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Suami</th>
                            <th>Usia Suami</th>
                            <th>Nama Istri</th>
                            <th>Usia Istri</th>
                            <th>Hasil Klasifikasi</th>
                            <th>Nama Kelurahan</th>
                            <th>Confidence</th>
                            <th>Tanggal Klasifikasi</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $dt)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $dt->pernikahan->nama_suami }}</td>
                            <td>{{ $dt->pernikahan->usia_suami }}</td>
                            <td>{{ $dt->pernikahan->nama_istri }}</td>
                            <td>{{ $dt->pernikahan->usia_istri }}</td>
                            <td>{{ $dt->kategori_pernikahan }}</td>
                            <td>{{ $dt->pernikahan->wilayah->desa ?? 'Tidak diketahui' }}</td>
                            <td>{{ $dt->confidence }} %</td>
                            <td>{{ \Carbon\Carbon::parse($dt->created_at)->format('d M Y') }}</td>
                            <td>
                                <div class="form-button-action">
                                    <a href="{{ route('data_pernikahan.edit', $dt->id) }}" class="btn btn-link btn-primary btn-lg">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" data-url="{{ route('data_pernikahan.delete', $dt->id) }}" class="btn btn-link btn-danger btn-delete">
                                        <i class="fa fa-times"></i>
                                    </a>
                                    <a href="{{ route('data_klasifikasi.detailKlasifikasi', $dt->id) }}" class="btn btn-link btn-info">
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
</div>

<!-- Modal Upload Excel -->
<di class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Excel File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('data_pernikahan.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="file">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>

@endsection
