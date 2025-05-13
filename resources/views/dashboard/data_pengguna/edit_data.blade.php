@extends('layouts.app')

@section('content')
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Data Pengguna</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('data_pengguna.index') }}">Data Pengguna</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Edit Pengguna</li>
      </ul>
    </div>

    <div class="row d-flex justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Form Edit Pengguna</div>
          </div>
          <div class="card-body">
            <form action="{{ route('data_pengguna.editDataPost', $pengguna->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="id" value="{{ $pengguna->id }}">

              <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" value="{{ $pengguna->nama }}" required>
              </div>

              <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="{{ $pengguna->username }}" required>
              </div>

              <div class="form-group">
                <label>Password <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                <input type="password" name="password" class="form-control" placeholder="••••••">
              </div>

              <div class="form-group">
                <label>Jabatan</label>
                <select name="role" class="form-control" required>
                  @foreach (['admin' => 'Admin', 'kepala kua' => 'Kepala KUA', 'penyuluh' => 'Penyuluh'] as $key => $label)
                    <option value="{{ $key }}" {{ $pengguna->role == $key ? 'selected' : '' }}>{{ $label }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label>Foto</label>
                @if ($pengguna->foto)
                  <div class="mb-2">
                    <img src="{{ asset('storage/foto/' . $pengguna->foto) }}" alt="Foto Pengguna" width="100">
                  </div>
                @else
                  <div class="mb-2">
                    <img src="{{ asset('assets/img/no-image.jpg') }}" alt="Tidak Ada Foto" width="100">
                  </div>
                @endif
                <input type="file" name="foto" class="form-control">
              </div>

              <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="4" required>{{ old('alamat', $pengguna->alamat) }}</textarea>
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
    </div>
@endsection
