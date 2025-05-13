@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="card">
            
            <div class="card-body">
                <div class="page-header">
                    <h3 class="fw-bold mb-3">Data Pengguna</h3>
                    <ul class="breadcrumbs mb-3">
                      <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                      </li>
                      <li class="separator"><i class="icon-arrow-right"></i></li>
                      <li class="nav-item"><a href="{{ route('data_pengguna.index') }}">Data Pengguna</a></li>
                    </ul>
                  </div>

                <div class="card-header mt-10">
                    <div class="card-header mt-4 px-0 border-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    
                            {{-- Bagian kiri: Tombol-tombol --}}
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('data_pengguna.tambahData') }}" class="btn btn-primary btn-round">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </a>
                    
                                <button type="button" class="btn btn-success btn-round" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="fa fa-upload"></i> Upload Excel
                                </button>
                    
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-round dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-download"></i> Export
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item">Export Excel (.xlsx)</a></li>
                                        <li><a class="dropdown-item">Export CSV</a></li>
                                        <li><a class="dropdown-item">Export PDF</a></li>
                                    </ul>
                                </div>
                            </div>
                    
                            {{-- Bagian kanan: Search & Filter --}}
                            <div class="d-flex flex-wrap gap-2">
                    
                                <form method="GET" action="{{ route('data_pengguna.index') }}" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request()->search }}">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                </form>
                    
                                <form method="GET" action="{{ route('data_pengguna.index') }}" class="d-flex align-items-center gap-2">
                                    <input type="hidden" name="search" value="{{ request()->search }}">
                                    <select name="filter_role" class="form-control">
                                        <option value="">Filter Role</option>
                                        <option value="admin" {{ request()->filter_role == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="kepala kua" {{ request()->filter_role == 'kepala kua' ? 'selected' : '' }}>Kepala KUA</option>
                                        <option value="penyuluh" {{ request()->filter_role == 'penyuluh' ? 'selected' : '' }}>Penyuluh</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </form>
                    
                            </div>
                        </div>
                    </div>
                    
                      
                {{-- table --}}
                <div class="table-responsive">
                    <table class="display table table-striped table-hover" id="basic-datatables">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama Lengkap</th>
                                <th>Jabatan</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $dt)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $dt->foto }}</td>
                                    <td>{{ $dt->nama }}</td>
                                    <td>{{ $dt->role }}</td>
                                    <td>
                                        <div class="form-button-action">
                                            <a href="{{ route('data_pengguna.edit', $dt->id) }}" class="btn btn-link btn-primary btn-lg">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" data-url="{{ route('data_pengguna.delete', $dt->id) }}" class="btn btn-link btn-danger btn-delete">
                                                <i class="fa fa-times"></i>
                                            </a>
                                            <a href="{{ route('data_pengguna.detail', $dt->id) }}" class="btn btn-link btn-info">
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

    <!-- Modal for File Upload -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Excel File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Choose Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
        
@endsection
