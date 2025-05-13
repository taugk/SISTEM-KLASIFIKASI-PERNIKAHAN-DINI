@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="page-header">
    <h3 class="fw-bold mb-3">Tambah Data Wilayah</h3>
    <ul class="breadcrumbs mb-3">
      <li class="nav-home">
        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
      </li>
      <li class="separator"><i class="icon-arrow-right"></i></li>
      <li class="nav-item"><a href="{{ route('data_wilayah.index') }}">Data Wilayah</a></li>
      <li class="separator"><i class="icon-arrow-right"></i></li>
      <li class="nav-item">Tambah Wilayah</li>
    </ul>
  </div>

  <div class="row d-flex justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Form Tambah Wilayah</div>
        </div>
        <div class="card-body">
          <form action="{{ route('data_wilayah.tambahDataPost') }}" method="POST">
            @csrf

            <div class="form-group">
              <label for="desa">Nama Desa</label>
              <input type="text" name="desa" class="form-control @error('desa') is-invalid @enderror" required value="{{ old('desa') }}">
              @error('desa')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label for="kecamatan">Kecamatan</label>
              <input type="text" name="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror" required value="{{ old('kecamatan') }}">
              @error('kecamatan')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label for="kabupaten">Kabupaten</label>
              <input type="text" name="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror" required value="{{ old('kabupaten') }}">
              @error('kabupaten')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label for="provinsi">Provinsi</label>
              <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror" required value="{{ old('provinsi') }}">
              @error('provinsi')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group mt-4">
              <button type="submit" class="btn btn-primary">Simpan</button>
              <a href="{{ route('data_wilayah.index') }}" class="btn btn-secondary">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

@endsection
