@extends('layouts.app')

@section('content')

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="page-header">
                    <h3 class="fw-bold mb-3">Data Edukasi</h3>
                    <ul class="breadcrumbs mb-3">
                        <li class="nav-home">
                            <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                        </li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item"><a href="{{ route('data_edukasi.index') }}">Data Edukasi</a></li>
                    </ul>
                </div>

                <div class="card-header mt-4 px-0 border-0">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        {{-- Tombol kiri --}}
                        <div class="d-flex flex-wrap gap-2">
                            @if(in_array(auth()->user()->role, ['admin', 'kepala kua']))
                            <a href="{{ route('data_edukasi.tambahData') }}" class="btn btn-primary btn-round">
                                <i class="fa fa-plus"></i> Tambah Data
                            </a>
                            @endif
                        </div>

                        {{-- Search kanan --}}
                        <form method="GET" action="{{ route('data_edukasi.index') }}" class="d-flex align-items-center gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari Judul..." value="{{ request()->search }}">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="display table table-striped table-hover" id="basic-datatables">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Gambar</th>
                                <th>Kategori</th>
                                <th>Pembuat</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $dt)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $dt->kd_edukasi }}</td>
                                    <td>{{ $dt->judul }}</td>
                                    <td>{{ \Str::limit($dt->deskripsi, 50) }}</td>
                                    <td>
                                        @if($dt->gambar)
                                            <img src="{{ asset('storage/edukasi/' . $dt->gambar) }}" alt="Gambar" width="50">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $dt->kategori ?? '-' }}</td>
                                    <td>{{ $dt->pengguna->nama ?? '-' }}</td>
                                    <td>
                                        <div class="form-button-action">
                                            @if(in_array(auth()->user()->role, ['admin', 'kepala kua']))
                                                <a href="{{ route('data_edukasi.editData', $dt->kd_edukasi) }}" class="btn btn-link btn-primary btn-lg"><i class="fa fa-edit"></i></a>
                                                <a href="javascript:void(0)" data-url="{{ route('data_edukasi.deleteData', $dt->kd_edukasi) }}" class="btn btn-link btn-danger btn-delete"><i class="fa fa-times"></i></a>
                                                <a href="{{ route('data_edukasi.detailData', $dt->kd_edukasi) }}" class="btn btn-link btn-info"><i class="fa fa-eye"></i></a>
                                            @endif

                                            @if(auth()->user()->role == 'penyuluh')
                                                <a href="{{ route('data_edukasi.detailData', $dt->kd_edukasi) }}" class="btn btn-link btn-info"><i class="fa fa-eye"></i></a>
                                            @endif
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
