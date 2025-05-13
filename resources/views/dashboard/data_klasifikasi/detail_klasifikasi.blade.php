@extends('layouts.app')

@section('content')
<div class="page-inner">
    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Detail Data Klasifikasi</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="{{ route('data_klasifikasi.index') }}">Data Klasifikasi</a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item">Detail</li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Nama Suami:</strong>
                    <p>{{ $klasifikasi->pernikahan->nama_suami }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Usia Suami:</strong>
                    <p>{{ $klasifikasi->pernikahan->usia_suami }} tahun</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Nama Istri:</strong>
                    <p>{{ $klasifikasi->pernikahan->nama_istri }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Usia Istri:</strong>
                    <p>{{ $klasifikasi->pernikahan->usia_istri }} tahun</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Kelurahan:</strong>
                    <p>{{ $klasifikasi->pernikahan->wilayah->desa ?? 'Tidak diketahui' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Tanggal Klasifikasi:</strong>
                    <p>{{ \Carbon\Carbon::parse($klasifikasi->created_at)->translatedFormat('d F Y') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Hasil Klasifikasi:</strong>
                    <p class="text-capitalize">{{ $klasifikasi->kategori_pernikahan }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Confidence:</strong>
                    <p>{{ $klasifikasi->confidence }} %</p>
                </div>
            </div>

            <a href="{{ route('data_klasifikasi.index') }}" class="btn btn-secondary mt-3">
                <i class="fa fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection
