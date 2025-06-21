@extends('layouts.app')

@section('content')
<div class="page-inner">
    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Detail Rekomendasi Wilayah Risiko Pernikahan Dini</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="{{ route('rekomendasi_penyuluhan.index') }}">Rekomendasi Wilayah</a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item">Detail</li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Informasi Wilayah</h5>
                            <ul class="list-group mb-4">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Nama Wilayah</span>
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
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Tahun</span>
                                    <strong>{{ $data->tahun ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Tingkat Risiko Wilayah</span>
                                    @php
                                        $risk = optional($data->resiko_wilayah_terbaru)->resiko_wilayah ?? '-';
                                        $badgeClass = match(strtolower($risk)) {
                                            'tinggi' => 'badge bg-danger',
                                            'sedang' => 'badge bg-warning text-dark',
                                            'rendah' => 'badge bg-success',
                                            default => 'badge bg-secondary'
                                        };
                                    @endphp
                                    <span class="{{ $badgeClass }}">{{ ucfirst($risk) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Jumlah Pernikahan Dini</span>
                                    <strong>{{ optional($data->resiko_wilayah_terbaru)->jumlah_pernikahan_dini ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Usia Suami Termuda</span>
                                    <strong>{{ optional($data->resiko_wilayah_terbaru)->usia_suami_termuda ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Usia Istri Termuda</span>
                                    <strong>{{ optional($data->resiko_wilayah_terbaru)->usia_istri_termuda ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Pendidikan Dominan Suami</span>
                                    <strong>{{ optional($data->resiko_wilayah_terbaru)->pendidikan_suami ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Pendidikan Dominan Istri</span>
                                    <strong>{{ optional($data->resiko_wilayah_terbaru)->pendidikan_istri ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Pekerjaan Dominan Suami</span>
                                    <strong>{{ optional($data->resiko_wilayah_terbaru)->pekerjaan_suami ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Pekerjaan Dominan Istri</span>
                                    <strong>{{ optional($data->resiko_wilayah_terbaru)->pekerjaan_istri ?? '-' }}</strong>
                                </li>

                            </ul>
                            <h5 class="mb-3">Rekomendasi Penyuluhan</h5>
                            <ul class="list-group mb-4">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Rekomendasi Penyuluhan</span>
                                    <strong>{{ $data->rekomendasi_penyuluhan ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Periode Rekomendasi</span>
                                    <strong>{{ $data->periode_rekomendasi ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Jumlah Penyuluhan</span>
                                    <strong>{{ $data->jumlah_penyuluhan ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Jumlah Peserta Penyuluhan</span>
                                    <strong>{{ $data->jumlah_peserta_penyuluhan ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Feedback Peserta</span>
                                    <strong>{{ $data->feedback_peserta ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Evaluasi Efektivitas</span>
                                    <strong>{{ $data->evaluasi_efektivitas ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Catatan Tambahan</span>
                                    <strong>{{ $data->catatan_tambahan ?? '-' }}</strong>
                                </li>
                            </ul>
                            <a href="{{ route('rekomendasi_penyuluhan.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left me-2"></i> Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTables jika diperlukan
        $('#basic-datatables').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            info: true,
        });
    });
</script>
