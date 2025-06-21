@extends('layouts.app')

@section('content')
<div class="page-inner">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="page-header mb-4">
                <h3 class="fw-bold">Detail Data Klasifikasi</h3>
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="{{ route('data_klasifikasi.index') }}">Data Klasifikasi</a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item">Detail</li>
                </ul>
            </div>

            <div class="row g-4">
                {{-- Suami --}}
                <div class="col-md-6">
                    <div class="bg-light p-4 rounded shadow-sm">
                        <h5 class="fw-bold mb-3 text-primary"><i class="fa fa-mars me-2"></i>Data Suami</h5>
                        @foreach ([
                            'Nama Suami' => $klasifikasi->pernikahan->nama_suami,
                            'Usia Suami' => $klasifikasi->pernikahan->usia_suami . ' tahun',
                            'Pendidikan Suami' => $klasifikasi->pernikahan->pendidikan_suami ?? 'Tidak diketahui',
                            'Pekerjaan Suami' => $klasifikasi->pernikahan->pekerjaan_suami ?? 'Tidak diketahui',
                            'Status Suami' => $klasifikasi->pernikahan->status_suami ?? 'Tidak diketahui',
                        ] as $label => $value)
                            <div class="mb-2"><strong>{{ $label }}:</strong> <p class="mb-0">{{ $value }}</p></div>
                        @endforeach
                    </div>
                </div>

                {{-- Istri --}}
                <div class="col-md-6">
                    <div class="bg-light p-4 rounded shadow-sm">
                        <h5 class="fw-bold mb-3 text-danger"><i class="fa fa-venus me-2"></i>Data Istri</h5>
                        @foreach ([
                            'Nama Istri' => $klasifikasi->pernikahan->nama_istri,
                            'Usia Istri' => $klasifikasi->pernikahan->usia_istri . ' tahun',
                            'Pendidikan Istri' => $klasifikasi->pernikahan->pendidikan_istri ?? 'Tidak diketahui',
                            'Pekerjaan Istri' => $klasifikasi->pernikahan->pekerjaan_istri ?? 'Tidak diketahui',
                            'Status Istri' => $klasifikasi->pernikahan->status_istri ?? 'Tidak diketahui',
                        ] as $label => $value)
                            <div class="mb-2"><strong>{{ $label }}:</strong> <p class="mb-0">{{ $value }}</p></div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Informasi Klasifikasi --}}
            <div class="mt-5">
                <h5 class="fw-bold mb-3"><i class="fa fa-info-circle me-2"></i>Informasi Klasifikasi</h5>
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <div>
                        <strong>Kelurahan:</strong>
                        <p>{{ $klasifikasi->pernikahan->wilayah->desa ?? 'Tidak diketahui' }}</p>
                    </div>
                    <div>
                        <strong>Tanggal Klasifikasi:</strong>
                        <p>{{ \Carbon\Carbon::parse($klasifikasi->created_at)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <strong>Hasil Klasifikasi:</strong>
                        <p><span class="badge bg-{{ $klasifikasi->kategori_pernikahan == 'Pernikahan Dini' ? 'danger' : 'success' }}">
                            {{ $klasifikasi->kategori_pernikahan }}
                        </span></p>
                    </div>
                    <div>
                        <strong>Confidence:</strong>
                        <p><span class="badge bg-primary">{{ $klasifikasi->confidence }}%</span></p>
                    </div>
                    <div>
                        <strong>Penyebab:</strong>
                        @php $penyebab = json_decode($klasifikasi->penyebab ?? '[]', true); @endphp
                        @if(is_array($penyebab) && count($penyebab) > 0)
                            <ul class="mb-0">
                                @foreach($penyebab as $p)
                                    <li>{{ $p }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p>Tidak diketahui</p>
                        @endif
                    </div>
                    <div>
                        <strong>Dampak:</strong>
                        @php $dampak = json_decode($klasifikasi->dampak ?? '[]', true); @endphp
                        @if(is_array($dampak) && count($dampak) > 0)
                            <ul class="mb-0">
                                @foreach($dampak as $d)
                                    <li>{{ $d }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p>{{ $klasifikasi->dampak ?? 'Tidak diketahui' }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('data_klasifikasi.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
