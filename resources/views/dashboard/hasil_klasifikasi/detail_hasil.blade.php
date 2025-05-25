@extends('layouts.app')

@section('content')

<div class="container">
  <div class="page-inner">
    <div class="page-header mb-4">
      <h3 class="fw-bold">Detail Hasil Klasifikasi Wilayah</h3>
      <hr>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">

        <!-- Info Wilayah -->
        <h5 class="mb-3">Informasi Wilayah</h5>
        <ul class="list-group mb-4">
          <li class="list-group-item d-flex justify-content-between">
            <span>Nama Kelurahan / Desa</span>
            <strong>{{ $data->desa ?? '-' }}</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Kecamatan</span>
            <strong>{{ $data->kecamatan ?? '-' }}</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Kabupaten / Kota</span>
            <strong>{{ $data->kabupaten ?? '-' }}</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Provinsi</span>
            <strong>{{ $data->provinsi ?? '-' }}</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>Tingkat Risiko Wilayah</span>
            @php
              $risk = optional($data->resiko_wilayah->first())->resiko_wilayah ?? '-';
              $badgeClass = match(strtolower($risk)) {
                'tinggi' => 'badge bg-danger',
                'sedang' => 'badge bg-warning text-dark',
                'rendah' => 'badge bg-success',
                default => 'badge bg-secondary'
              };
            @endphp
            <span class="{{ $badgeClass }}">{{ ucfirst($risk) }}</span>
          </li>
        </ul>

        <!-- Statistik Pernikahan -->
        <h5 class="mb-3">Statistik Pernikahan Dini</h5>
        <ul class="list-group mb-4">
          <li class="list-group-item d-flex justify-content-between">
            <span>Jumlah Kasus Pernikahan Dini</span>
            <strong>{{ optional($data->resiko_wilayah->first())->jumlah_pernikahan_dini ?? '-' }}</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Periode Data</span>
            <strong>{{ optional($data->resiko_wilayah->first())->periode ?? '-' }}</strong>
          </li>
        </ul>

        {{-- Statistik Pernikahan --}}
        <h5 class="mb-3">Statistik Pernikahan</h5>
        <ul class="list-group mb-4">
          <li class="list-group-item d-flex justify-content-between">
            <span>Jumlah Pernikahan</span>
            <strong>{{ $data->resiko_wilayah->first()->jumlah_pernikahan ?? '-' }}</strong>
          </li>
        </ul>

        {{-- Statistik rata-rata usia --}}
        <h5 class="mb-3">Statistik Rata-rata Usia</h5>
        <ul class="list-group mb-4">
          <li class="list-group-item d-flex justify-content-between">
            <span>Usia Suami</span>
            <strong>{{ $rataUsiaSuami ? number_format($rataUsiaSuami, 1) : '-' }}</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Usia Istri</span>
            <strong>{{ $rataUsiaIstri ? number_format($rataUsiaIstri, 1) : '-' }}</strong>
          </li>
        </ul>
        <!-- Tombol Aksi -->
        <div class="d-flex justify-content-start">
          <a href="{{ route('hasil_klasifikasi.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-2"></i> Kembali
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

@endsection
