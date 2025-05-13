@extends('layouts.app')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">

            {{-- Header --}}
            <div class="page-header">
                <h3 class="fw-bold mb-3">Data Wilayah</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item">Data Wilayah</li>
                </ul>
            </div>

            {{-- Tambah Data Button --}}
            <div class="mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahWilayah">
                    <i class="fa fa-plus"></i> Tambah Data Wilayah
                </button>
            </div>

            {{-- Filter and Search --}}
            <div class="card-header mt-4 px-0 border-0">
                <form method="GET" action="{{ route('data_wilayah.index') }}" class="d-flex align-items-center gap-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by Desa, Kecamatan, Kabupaten, Provinsi" value="{{ request()->search }}">
                    <select name="filter_kecamatan" class="form-control">
                        <option value="">Filter by Kecamatan</option>
                        @foreach ($kecamatans as $kecamatan)
                            <option value="{{ $kecamatan }}" {{ request()->filter_kecamatan == $kecamatan ? 'selected' : '' }}>
                                {{ $kecamatan }}
                            </option>
                        @endforeach
                    </select>

                    <select name="filter_kabupaten" class="form-control">
                        <option value="">Filter by Kabupaten</option>
                        @foreach ($kabupatens as $kabupaten)
                            <option value="{{ $kabupaten }}" {{ request()->filter_kabupaten == $kabupaten ? 'selected' : '' }}>
                                {{ $kabupaten }}
                            </option>
                        @endforeach
                    </select>

                    <select name="filter_provinsi" class="form-control">
                        <option value="">Filter by Provinsi</option>
                        @foreach ($provinsis as $provinsi)
                            <option value="{{ $provinsi }}" {{ request()->filter_provinsi == $provinsi ? 'selected' : '' }}>
                                {{ $provinsi }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>

            {{-- Table --}}
            <div class="table-responsive mt-3">
                <table class="display table table-striped table-hover" id="basic-datatables">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Desa</th>
                            <th>Kecamatan</th>
                            <th>Kabupaten</th>
                            <th>Provinsi</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->desa }}</td>
                            <td>{{ $item->kecamatan }}</td>
                            <td>{{ $item->kabupaten }}</td>
                            <td>{{ $item->provinsi }}</td>
                            <td>
                                <div class="form-button-action">
                                    <button type="button" class="btn btn-link btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalEditWilayah{{ $item->id }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <a href="javascript:void(0)" data-url="{{ route('data_wilayah.deleteData', $item->id) }}" class="btn btn-link btn-danger btn-delete">
                                        <i class="fa fa-times"></i>
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

            {{-- Modal Tambah Wilayah --}}
            <div class="modal fade" id="modalTambahWilayah" tabindex="-1" aria-labelledby="modalTambahWilayahLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('data_wilayah.tambahDataPost') }}" method="POST" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTambahWilayahLabel">Tambah Data Wilayah</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="desa">Nama Desa</label>
                                <input type="text" name="desa" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="kecamatan">Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="kabupaten">Kabupaten</label>
                                <input type="text" name="kabupaten" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="provinsi">Provinsi</label>
                                <input type="text" name="provinsi" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal Edit Wilayah (Pisah dari Tabel) --}}
            @foreach ($data as $item)
            <div class="modal fade" id="modalEditWilayah{{ $item->id }}" tabindex="-1" aria-labelledby="modalEditWilayahLabel{{ $item->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('data_wilayah.updateData', $item->id) }}" method="POST" class="modal-content">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditWilayahLabel{{ $item->id }}">Edit Data Wilayah</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="desa">Nama Desa</label>
                                <input type="text" name="desa" class="form-control" value="{{ $item->desa }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="kecamatan">Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control" value="{{ $item->kecamatan }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="kabupaten">Kabupaten</label>
                                <input type="text" name="kabupaten" class="form-control" value="{{ $item->kabupaten }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="provinsi">Provinsi</label>
                                <input type="text" name="provinsi" class="form-control" value="{{ $item->provinsi }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach

        </div>
    </div>

@endsection
