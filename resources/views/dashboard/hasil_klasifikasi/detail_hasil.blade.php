@extends('layouts.app')

@section('content')

<div class="page-inner">
    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Detail Hasil Klasifikasi</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="{{ route('hasil_klasifikasi.index') }}">Hasil Klasifikasi</a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item">Detail</li>
                </ul>
            </div>

            <!-- Informasi Wilayah -->
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
                        $risk = optional($resiko_wilayah_terbaru)->resiko_wilayah ?? '-';
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

            <!-- Statistik Pernikahan Dini -->
            <h5 class="mb-3">Statistik Pernikahan Dini</h5>
            <ul class="list-group mb-4">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Jumlah Kasus Pernikahan Dini</span>
                    <strong>{{ optional($resiko_wilayah_terbaru)->jumlah_pernikahan_dini ?? '-' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Periode Data</span>
                    <strong>
                        @if(optional($resiko_wilayah_terbaru)->periode)
                            {{ \Carbon\Carbon::parse($resiko_wilayah_terbaru->periode)->format('Y') }}
                        @else
                            -
                        @endif
                    </strong>
                </li>
            </ul>

            <!-- Statistik Pernikahan -->
            <h5 class="mb-3">Statistik Pernikahan</h5>
            <ul class="list-group mb-4">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Jumlah Pernikahan</span>
                    <strong>{{ optional($resiko_wilayah_terbaru)->jumlah_pernikahan ?? '-' }}</strong>
                </li>
            </ul>

            <!-- Statistik Rata-rata Usia -->
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

            <h5 class="mb-3">Penyebab Pernikahan Dini Terbanyak</h5>
                <ul class="list-group mb-4">
                    @forelse($penyebab_terbanyak as $penyebab => $jumlah)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $penyebab }}
                            <span class="badge bg-primary rounded-pill">{{ $jumlah }}</span>
                        </li>
                    @empty
                        <li class="list-group-item">Tidak ada data penyebab tersedia.</li>
                    @endforelse
                </ul>

                <h5 class="mb-3">Rekomendasi Penyuluhan</h5>
                <ul class="list-group mb-4">
                    @forelse($rekomendasi as $rk)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $rk }}
                            <span class="badge bg-info rounded-pill">Penyuluhan</span>
                        </li>
                    @empty
                        <li class="list-group-item">Tidak ada rekomendasi penyuluhan tersedia.</li>
                    @endforelse
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

@endsection
