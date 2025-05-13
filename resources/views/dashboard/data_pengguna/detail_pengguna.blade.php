@extends('layouts.app')

@section('content')
<div class="container">
  <div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Detail Pengguna</h3>
        <ul class="breadcrumbs mb-3">
          <li class="nav-home">
            <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
          </li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item"><a href="{{ route('data_pengguna.index') }}">Data Pengguna</a></li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item">Detail Pengguna</li>
        </ul>
      </div>

    <div class="card">
      <div class="card-body row">
        <div class="col-md-4 text-center mb-4">
          @if($pengguna->foto && file_exists(public_path('storage/foto/' . $pengguna->foto)))
            <img src="{{ asset('storage/app/public/foto/' . $pengguna->foto) }}" class="img-fluid rounded" alt="Foto Pengguna" width="200">
          @else
            <img src="{{ asset('assets/img/no-image.jpg') }}" class="img-fluid rounded" alt="Tidak Ada Foto" width="200">
          @endif
        </div>

        <div class="col-md-8 mt-4">
          <p><strong>Nama:</strong> {{ $pengguna->nama }}</p>
          <p><strong>Username:</strong> {{ $pengguna->username }}</p>
          <p><strong>Role:</strong> {{ ucfirst($pengguna->role) }}</p>
          <p><strong>Alamat:</strong> {{ $pengguna->alamat }}</p>
        </div>

        <div class="col-md-12 mt-4 d-flex justify-content-between">
          <a href="{{ route('data_pengguna.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Kembali
          </a>
          <a href="{{ route('data_pengguna.edit', $pengguna->id) }}" class="btn btn-primary">
            <i class="fa fa-edit"></i> Edit Pengguna
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection
