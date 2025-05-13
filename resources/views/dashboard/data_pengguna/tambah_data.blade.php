@extends('layouts.app')

@section('content')
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Tambah Data Pengguna</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('data_pengguna.index') }}">Data Pengguna</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('data_pengguna.tambahData') }}">Tambah Pengguna</a></li>
      </ul>
    </div>

    <div class="row d-flex justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Form Tambah Pengguna</div>
          </div>
          <div class="card-body">
            <form action="{{ route('data_pengguna.tambahDataPost') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Jabatan</label>
                <select name="role" class="form-control" required>
                  <option value="admin">Admin</option>
                  <option value="kepala kua">Kepala KUA</option>
                  <option value="penyuluh">Penyuluh</option>
                </select>
              </div>
              <div class="form-group">
                <label>Foto</label>
                <input type="file" name="foto" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="4" required>{{ old('alamat') }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
              <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('data_pengguna.index') }}" class="btn btn-secondary">Batal</a>
              </div>
            </form>
          </div>
        </div>
      </div>
@endsection
