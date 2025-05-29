@extends('layouts.app')

@section('content')
<div class="page-inner">
    <div class="card-body">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="page-header d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-3">Statistik Pernikahan Dini</h3>
                            <ul class="breadcrumbs mb-3">
                                <li class="nav-home">
                                    <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                                </li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item">Hasil Klasifikasi</li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item">Statistik</li>
                            </ul>
                        </div>
                        <button onclick="printCharts()" class="btn btn-primary d-print-none">
                            <i class="fas fa-print"></i> Cetak Statistik
                        </button>
                    </div>

                    <div class="d-none d-print-block mb-4 text-center">
                        <h4 class="mb-1">Statistik Pernikahan Dini</h4>
                        <p class="mb-3">{{ now()->format('d F Y') }}</p>
                    </div>

                    <form method="GET" action="{{ route('hasil_klasifikasi.chart') }}" class="mb-4 d-print-none">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="tahun" class="form-label">Tahun</label>
                                <select name="tahun" id="tahun" class="form-control">
                                    @foreach ($daftarTahun as $th)
                                        <option value="{{ $th }}" {{ $th == $tahunTerpilih ? 'selected' : '' }}>{{ $th }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="wilayah" class="form-label">Wilayah</label>
                                <select name="wilayah" id="wilayah" class="form-control">
                                    <option value="">Semua Wilayah</option>
                                    @foreach ($daftarWilayah as $wilayah)
                                        <option value="{{ $wilayah }}" {{ $wilayah == $wilayahTerpilih ? 'selected' : '' }}>
                                            {{ $wilayah }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="pendidikan" class="form-label">Pendidikan</label>
                                <select name="pendidikan" id="pendidikan" class="form-control">
                                    <option value="">Semua Pendidikan</option>
                                    @foreach ($daftarPendidikan as $pendidikan)
                                        <option value="{{ $pendidikan }}" {{ $pendidikan == $pendidikanTerpilih ? 'selected' : '' }}>
                                            {{ $pendidikan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    @foreach ($daftarStatus as $status)
                                        <option value="{{ $status }}" {{ $status == $statusTerpilih ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('hasil_klasifikasi.chart') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <h4 class="card-title text-center mb-4">
                        Statistik Pernikahan Dini
                        @if($tahunTerpilih) Tahun {{ $tahunTerpilih }} @endif
                        @if($wilayahTerpilih) di {{ $wilayahTerpilih }} @endif
                        @if($pendidikanTerpilih) (Pendidikan: {{ $pendidikanTerpilih }}) @endif
                        @if($statusTerpilih) (Status: {{ $statusTerpilih }}) @endif
                    </h4>

                    <div class="row print-charts">
                        <div class="col-md-6 mb-4">
                            <div class="card chart-card">
                                <div class="card-body">
                                    <h6 class="text-center mb-3">Perbandingan Pernikahan Dini vs Normal</h6>
                                    <div class="chart-container">
                                        <canvas id="pieChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card chart-card">
                                <div class="card-body">
                                    <h6 class="text-center mb-3">Distribusi Usia Pernikahan Dini</h6>
                                    <div class="chart-container">
                                        <canvas id="donutChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card chart-card">
                                <div class="card-body">
                                    <h6 class="text-center mb-3">Distribusi Pendidikan</h6>
                                    <div class="chart-container">
                                        <canvas id="polarChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card chart-card">
                                <div class="card-body">
                                    <h6 class="text-center mb-3">Distribusi Status</h6>
                                    <div class="chart-container">
                                        <canvas id="radarChart"></canvas>
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

@push('styles')
<style>
.chart-card {
    height: 100%;
    margin-bottom: 0;
}

.card-body {
    padding: 1.25rem;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.chart-container {
    position: relative;
    height: 350px;
    flex: 1;
    min-height: 0;
}

h6.text-center {
    margin-bottom: 1rem;
    font-weight: 600;
}

/* Print styles */
@media print {
    @page {
        size: landscape;
        margin: 1cm;
    }
    body {
        padding: 0;
        margin: 0;
    }
    .d-print-none {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
        break-inside: avoid;
        height: auto;
    }
    .card-body {
        padding: 10px !important;
        height: auto;
    }
    .print-charts {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        page-break-before: auto;
    }
    .print-charts > div {
        break-inside: avoid;
        page-break-inside: avoid;
    }
    .chart-container {
        height: 250px;
    }
    canvas {
        max-width: 100% !important;
        height: auto !important;
    }
    .breadcrumbs {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let charts = {};
    
    // Common options for all charts
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 10,
                top: 10,
                bottom: 10
            }
        }
    };

    // Pie Chart
    charts.pieChart = new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($kategoriLabels) !!},
            datasets: [{
                data: {!! json_encode($kategoriData) !!},
                backgroundColor: ['#FF6384', '#36A2EB']
            }]
        },
        options: {
            ...commonOptions,
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
                                        fillStyle: data.datasets[0].backgroundColor[i],
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

    // Donut Chart
    charts.donutChart = new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($usiaLabels) !!},
            datasets: [{
                data: {!! json_encode($usiaData) !!},
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            ...commonOptions,
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
                                        fillStyle: data.datasets[0].backgroundColor[i],
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

    // Polar Area Chart
    charts.polarChart = new Chart(document.getElementById('polarChart'), {
        type: 'polarArea',
        data: {
            labels: {!! json_encode($pendidikanLabels) !!},
            datasets: [{
                data: {!! json_encode($pendidikanData) !!},
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            }]
        },
        options: {
            ...commonOptions,
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
                                        fillStyle: data.datasets[0].backgroundColor[i],
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

    // Radar Chart
    charts.radarChart = new Chart(document.getElementById('radarChart'), {
        type: 'radar',
        data: {
            labels: {!! json_encode($statusLabels) !!},
            datasets: [{
                label: 'Jumlah Kasus',
                data: {!! json_encode($statusData) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgb(54, 162, 235)',
                pointBackgroundColor: 'rgb(54, 162, 235)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(54, 162, 235)',
                fill: true
            }]
        },
        options: {
            ...commonOptions,
            elements: {
                line: {
                    borderWidth: 3
                }
            },
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Print function
    window.printCharts = function() {
        const printWindow = window.open('', '_blank');
        const currentDate = new Date().toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        let htmlContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Statistik Pernikahan Dini</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        padding: 20px;
                        margin: 0;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 30px;
                    }
                    .charts-container {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 20px;
                    }
                    .chart-item {
                        break-inside: avoid;
                        page-break-inside: avoid;
                        margin-bottom: 20px;
                    }
                    .chart-title {
                        font-size: 14px;
                        font-weight: bold;
                        margin-bottom: 10px;
                        color: #333;
                    }
                    img {
                        max-width: 100%;
                        height: auto;
                    }
                    @media print {
                        @page {
                            size: landscape;
                            margin: 1cm;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2 style="margin-bottom: 5px;">Statistik Pernikahan Dini</h2>
                    <p style="margin-top: 0; color: #666;">${currentDate}</p>
                </div>
                <div class="charts-container">
                    <div class="chart-item">
                        <div class="chart-title">Perbandingan Pernikahan Dini vs Normal</div>
                        <img src="${charts.pieChart.toBase64Image()}" />
                    </div>
                    <div class="chart-item">
                        <div class="chart-title">Distribusi Usia Pernikahan Dini</div>
                        <img src="${charts.donutChart.toBase64Image()}" />
                    </div>
                    <div class="chart-item">
                        <div class="chart-title">Distribusi Pendidikan</div>
                        <img src="${charts.polarChart.toBase64Image()}" />
                    </div>
                    <div class="chart-item">
                        <div class="chart-title">Distribusi Status</div>
                        <img src="${charts.radarChart.toBase64Image()}" />
                    </div>
                </div>
            </body>
            </html>
        `;

        printWindow.document.write(htmlContent);
        printWindow.document.close();

        setTimeout(() => {
            printWindow.print();
        }, 500);
    };

    // Handle print media changes
    let mediaQueryList = window.matchMedia('print');
    mediaQueryList.addListener(function(mql) {
        if (!mql.matches) {
            setTimeout(() => {
                Object.values(charts).forEach(chart => {
                    if (chart && typeof chart.resize === 'function') {
                        chart.resize();
                    }
                });
            }, 500);
        }
    });
});
</script>
@endpush
