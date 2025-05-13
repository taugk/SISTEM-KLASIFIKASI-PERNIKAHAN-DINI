@extends('layouts.app')

@section('content')
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Detail Pasangan</h3>
    </div>

    <div class="card">
      <div class="card-body row">
        <div class="col-md-6">
          <h5>Data Suami</h5>
          <p><strong>Nama:</strong> {{ $data->nama_suami }}</p>
          <p><strong>Tanggal Lahir:</strong> {{ Carbon\Carbon::parse($data->tanggal_lahir_suami)->format('d M Y') }}</p>
          <p><strong>Usia:</strong> {{ $data->usia_suami }} tahun</p>
          <p><strong>Pendidikan:</strong> {{ $data->pendidikan_suami }}</p>
          <p><strong>Pekerjaan:</strong> {{ $data->pekerjaan_suami }}</p>
          <p><strong>Status:</strong> {{ $data->status_suami }}</p>
        </div>

        <div class="col-md-6">
          <h5>Data Istri</h5>
          <p><strong>Nama:</strong> {{ $data->nama_istri }}</p>
          <p><strong>Tanggal Lahir:</strong> {{ Carbon\Carbon::parse($data->tanggal_lahir_istri)->format('d M Y') }}</p>
          <p><strong>Usia:</strong> {{ $data->usia_istri }} tahun</p>
          <p><strong>Pendidikan:</strong> {{ $data->pendidikan_istri }}</p>
          <p><strong>Pekerjaan:</strong> {{ $data->pekerjaan_istri }}</p>
          <p><strong>Status:</strong> {{ $data->status_istri }}</p>
        </div>

        <div class="col-md-12 mt-4">
          <h5>Informasi Pernikahan</h5>
          <p><strong>Tanggal Akad:</strong> {{ Carbon\Carbon::parse($data->tanggal_akad)->format('d M Y') }}</p>
          <p><strong>Nama Kelurahan:</strong> {{ $data->wilayah->desa }}</p>
        </div>

        <div class="col-md-12 mt-4 d-flex justify-content-between">
            <a href="{{ route('data_pernikahan.index') }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('data_pernikahan.edit', $data->id) }}" class="btn btn-primary">
              <i class="fa fa-edit"></i> Edit Data
            </a>
          </div>

      </div>
    </div>
  </div>

@endsection
