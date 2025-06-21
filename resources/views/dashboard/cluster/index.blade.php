@extends('layouts.app')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Hasil Clustering Wilayah Berdasarkan Pernikahan Dini</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item">Clustering Wilayah</li>
                </ul>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover">
                    <thead class="bg-secondary text-white text-center">
                        <tr>
                            <th>No</th>
                            <th>Wilayah</th>
                            <th>Jumlah Pernikahan Dini</th>
                            <th>Rata-rata Usia Suami</th>
                            <th>Rata-rata Usia Istri</th>
                            <th>Rata-rata Pendidikan Suami</th>
                            <th>Rata-rata Pendidikan Istri</th>
                            <th>Rata-rata Pekerjaan Suami</th>
                            <th>Rata-rata Pekerjaan Istri</th>
                            <th>Rata-rata Status Suami</th>
                            <th>Rata-rata Status Istri</th>
                            <th>Cluster</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hasil_clustering as $i => $item)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $item['wilayah'] }}</td>
                            <td class="text-center">{{ $item['jumlah_pernikahan_dini'] }}</td>
                            <td class="text-center">{{ number_format($item['rata_rata_usia_suami'], 2) }}</td>
                            <td class="text-center">{{ number_format($item['rata_rata_usia_istri'], 2) }}</td>
                            <td class="text-center">{{ number_format($item['rata_rata_pendidikan_suami'], 2) }}</td>
                            <td class="text-center">{{ number_format($item['rata_rata_pendidikan_istri'], 2) }}</td>
                            <td class="text-center">{{ number_format($item['rata_rata_pekerjaan_suami'], 2) }}</td>
                            <td class="text-center">{{ number_format($item['rata_rata_pekerjaan_istri'], 2) }}</td>
                            <td class="text-center">{{ number_format($item['rata_rata_status_suami'], 2) }}</td>
                            <td class="text-center">{{ number_format($item['rata_rata_status_istri'], 2) }}</td>
                            <td class="text-center">
                                @php
                                    $label = $item['cluster_label'];
                                @endphp
                                @if ($label === 'Tinggi')
                                    <span class="badge bg-danger">{{ $label }}</span>
                                @elseif ($label === 'Sedang')
                                    <span class="badge bg-warning text-dark">{{ $label }}</span>
                                @else
                                    <span class="badge bg-success">{{ $label }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center">Tidak ada data clustering yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
