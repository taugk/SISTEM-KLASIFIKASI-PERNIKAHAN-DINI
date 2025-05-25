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
                @php
                    $foto = $pengguna->foto;
                @endphp
                <div class="mb-3">
                    @if ($foto && file_exists(public_path('storage/foto/' . $foto)))
                    <img src="{{ asset('storage/foto/' . $foto) }}"
                        alt="Foto Pengguna"
                        class="rounded shadow"
                        style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                    <div class="d-flex justify-content-center align-items-center rounded bg-light border"
                        style="width: 150px; height: 150px;">
                        <svg class="text-secondary" width="64" height="64" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                @if ($pengguna->foto)
                <input type="file" name="foto" placeholder="Pilih Foto" value="{{ $pengguna->foto }}" class="form-control">
                @else
                <input type="file" name="foto" placeholder="Pilih Foto" class="form-control">
                @endif
                @error('foto')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror


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
