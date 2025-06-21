@extends('layouts.app')

@section('content')
<d class="col-md-12">
    <div class="card">
        <div class="card-body">
            {{-- Header --}}
            <div class="page-header">
                <h3 class="fw-bold mb-3">Rekomendasi Wilayah Risiko Pernikahan Dini</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item">
                        <a href="{{ route('hasil_klasifikasi.index') }}">Hasil Klasifikasi</a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item active">Rekomendasi Wilayah</li>
                </ul>
            </div>

            {{-- Filter --}}
            <div class="card-header mt-4 px-0 border-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex flex-wrap gap-2">
                        <form method="GET" action="{{ route('rekomendasi_penyuluhan.index') }}" class="d-flex align-items-center gap-2">
                            <select name="tahun" class="form-control">
                                <option value="">Filter by Tahun</option>
                                @foreach ($daftarTahun as $th)
                                    <option value="{{ $th }}" {{ $tahunDipilih == $th ? 'selected' : '' }}>
                                        {{ $th }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>
                        <a href="{{ route('rekomendasi_penyuluhan.index') }}" class="btn btn-secondary">Reset Filter</a>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive mt-3">
                <table class="display table table-striped table-hover" id="basic-datatables">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Wilayah</th>
                            <th>Jumlah Pernikahan Dini</th>
                            <th>Usia Suami Termuda</th>
                            <th>Usia Istri Termuda</th>
                            <th>Pendidikan Dominan Suami</th>
                            <th>Pendidikan Dominan Istri</th>
                            <th>Gender Dini Dominan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item['nama_wilayah'] }}</td>
                            <td>{{ $item['jumlah_pernikahan_dini'] }}</td>
                            <td>{{ $item['usia_terendah_suami'] ?? '-' }}</td>
                            <td>{{ $item['usia_terendah_istri'] ?? '-' }}</td>
                            <td>{{ $item['pendidikan_dominan_suami'] ?? '-' }}</td>
                            <td>{{ $item['pendidikan_dominan_istri'] ?? '-' }}</td>
                            <td>{{ $item['gender_dominan'] }}</td>
                            <td>
                                <a href="{{ route('rekomendasi.detail', $item['wilayah_id']) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('rekomendasi.detail', $item['wilayah_id']) }}" class="btn btn-sm btn-success" title="Rekomendasi Penyuluhan">
                                    <i class="fas fa-lightbulb"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Chart --}}
            @if($data->isNotEmpty())
            <div class="mt-5">
                <h4>Visualisasi Top 5 Wilayah (Jumlah Pernikahan Dini)</h4>
                <canvas id="chartRekomendasi" height="100"></canvas>
            </div>
            @endif
        </div>
@endsection

@section('scripts')
@if($data->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartRekomendasi').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($data->pluck('nama_wilayah')) !!},
            datasets: [{
                label: 'Jumlah Pernikahan Dini',
                data: {!! json_encode($data->pluck('jumlah_pernikahan_dini')) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Kasus'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Wilayah'
                    }
                }
            }
        }
    });
</script>
@endif
@endsection
