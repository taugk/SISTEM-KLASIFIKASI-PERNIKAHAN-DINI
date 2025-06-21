@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="page-inner">
        <div class="page-header mb-4">
            <h3 class="fw-bold"><i class="fa fa-users me-2"></i>Detail Pasangan</h3>
        </div>

        <div class="row g-4">
            {{-- Card Suami --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3"><i class="fa fa-mars me-2"></i>Data Suami</h5>
                        <ul class="list-unstyled">
                            <li><strong>Nama:</strong> {{ $data->nama_suami }}</li>
                            <li><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($data->tanggal_lahir_suami)->format('d M Y') }}</li>
                            <li><strong>Usia:</strong> {{ $data->usia_suami }} tahun</li>
                            <li><strong>Pendidikan:</strong> {{ $data->pendidikan_suami }}</li>
                            <li><strong>Pekerjaan:</strong> {{ $data->pekerjaan_suami }}</li>
                            <li><strong>Status:</strong> {{ $data->status_suami }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Card Istri --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title text-danger mb-3"><i class="fa fa-venus me-2"></i>Data Istri</h5>
                        <ul class="list-unstyled">
                            <li><strong>Nama:</strong> {{ $data->nama_istri }}</li>
                            <li><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($data->tanggal_lahir_istri)->format('d M Y') }}</li>
                            <li><strong>Usia:</strong> {{ $data->usia_istri }} tahun</li>
                            <li><strong>Pendidikan:</strong> {{ $data->pendidikan_istri }}</li>
                            <li><strong>Pekerjaan:</strong> {{ $data->pekerjaan_istri }}</li>
                            <li><strong>Status:</strong> {{ $data->status_istri }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Informasi Pernikahan --}}
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-success mb-3"><i class="fa fa-ring me-2"></i>Informasi Pernikahan</h5>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Tanggal Akad:</strong> {{ \Carbon\Carbon::parse($data->tanggal_akad)->format('d M Y') }}</li>
                            <li><strong>Nama Kelurahan:</strong> {{ $data->wilayah->desa }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="col-12 d-flex justify-content-between align-items-center">
                <a href="{{ route('data_pernikahan.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('data_pernikahan.edit', $data->id) }}" class="btn btn-primary">
                    <i class="fa fa-edit me-1"></i> Edit Data
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
