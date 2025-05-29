@extends('layouts.app')

@section('content')
<div class="page-inner">
    <div class="card-body">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Statistik Pernikahan</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home">
                                <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                            </li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="{{ route('hasil_klasifikasi.chart') }}">Statistik Pernikahan</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="text-center mb-3">Perbandingan Kategori Pernikahan</h6>
                                    <div style="position: relative; height: 350px;">
                                        <canvas id="kategoriChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="text-center mb-3">Tren Pernikahan Dini per Tahun</h6>
                                    <div style="position: relative; height: 350px;">
                                        <canvas id="trendChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="text-center mb-3">Distribusi Usia Pernikahan Dini</h6>
                                    <div style="position: relative; height: 350px;">
                                        <canvas id="usiaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Pie Chart - Perbandingan Kategori Pernikahan
    new Chart(document.getElementById('kategoriChart'), {
        type: 'pie',
        data: {
            labels: @json($kategoriLabels),
            datasets: [{
                data: @json($kategoriData),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map(function(label, i) {
                                    const value = data.datasets[0].data[i];
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return {
                                        text: `${label}: ${value} (${percentage}%)`,
                                        fillStyle: chart.data.datasets[0].backgroundColor[i],
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                }
            }
        }
    });

    // Bar Chart - Tren per Tahun
    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels: @json($tahunLabels),
            datasets: [{
                label: 'Jumlah Kasus',
                data: @json($tahunData),
                backgroundColor: '#36A2EB',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Bar Chart - Distribusi Usia
    new Chart(document.getElementById('usiaChart'), {
        type: 'bar',
        data: {
            labels: @json($usiaLabels),
            datasets: [{
                label: 'Jumlah Kasus',
                data: @json($usiaData),
                backgroundColor: [
                    '#FF6384',
                    '#FFCE56',
                    '#4BC0C0'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush 