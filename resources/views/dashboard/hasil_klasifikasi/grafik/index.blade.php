@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="card-body">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="page-header">
            <h3 class="fw-bold mb-3">Grafik Jumlah Pernikahan Dini</h3>
            <ul class="breadcrumbs mb-3">
              <li class="nav-home">
                <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
              </li>
              <li class="separator"><i class="icon-arrow-right"></i></li>
              <li class="nav-item"><a href="{{ route('hasil_klasifikasi.graphView') }}">Grafik Jumlah Pernikahan Dini</a></li>
            </ul>
          </div>

          <form method="GET" action="{{ route('hasil_klasifikasi.graphView') }}" class="mb-4">
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
                <label for="risiko" class="form-label">Tingkat Risiko</label>
                <select name="risiko" id="risiko" class="form-control">
                  <option value="">Semua Risiko</option>
                  @foreach ($daftarRisiko as $risiko)
                    <option value="{{ $risiko }}" {{ $risiko == $risikoTerpilih ? 'selected' : '' }}>
                      {{ ucfirst($risiko) }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-3 mb-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select name="bulan" id="bulan" class="form-control">
                  <option value="">Semua Bulan</option>
                  @foreach ($namaBulan as $index => $bulan)
                    <option value="{{ $index + 1 }}" {{ ($index + 1) == $bulanTerpilih ? 'selected' : '' }}>
                      {{ $bulan }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-12">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('hasil_klasifikasi.graphView') }}" class="btn btn-secondary">
                  <i class="fas fa-sync"></i> Reset
                </a>
              </div>
            </div>
          </form>

          <h4 class="card-title text-center mb-4">
            Grafik Jumlah Pernikahan Dini 
            @if($tahunTerpilih) Tahun {{ $tahunTerpilih }} @endif
            @if($bulanTerpilih) Bulan {{ $namaBulan[$bulanTerpilih - 1] }} @endif
            @if($wilayahTerpilih) di {{ $wilayahTerpilih }} @endif
            @if($risikoTerpilih) (Risiko {{ ucfirst($risikoTerpilih) }}) @endif
          </h4>

          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="card graph-card">
                <div class="card-body">
                  <h6 class="text-center mb-3">Pernikahan Dini per Desa</h6>
                  <div class="graph-container">
                    <canvas id="grafikWilayah"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6 mb-4">
              <div class="card graph-card">
                <div class="card-body">
                  <h6 class="text-center mb-3">Distribusi Tingkat Risiko</h6>
                  <div class="graph-container">
                    <canvas id="grafikRisiko"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12 mb-4">
              <div class="card graph-card">
                <div class="card-body">
                  <h6 class="text-center mb-3">Tren Pernikahan Dini per Bulan</h6>
                  <div class="graph-container graph-container-lg">
                    <canvas id="grafikBulan"></canvas>
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
.graph-card {
  height: 100%;
  margin-bottom: 0;
}

.card-body {
  padding: 1.25rem;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.graph-container {
  position: relative;
  height: 350px;
  flex: 1;
  min-height: 0;
}

.graph-container-lg {
  height: 400px;
}

h6.text-center {
  margin-bottom: 1rem;
  font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Get the data
    const wilayahLabels = @json($wilayahLabels);
    const wilayahData = @json($wilayahData);

    // Create color array
    const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40',
        '#9966FF', '#FF99CC', '#99FF99', '#FF9966', '#99CCFF'
    ];

    // Common chart options
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    };

    // Donut Chart - Pernikahan Dini per Desa
    new Chart(document.getElementById('grafikWilayah'), {
        type: 'doughnut',
        data: {
            labels: wilayahLabels.length ? wilayahLabels : ['Tidak Ada Data'],
            datasets: [{
                data: wilayahData.length ? wilayahData : [1],
                backgroundColor: wilayahData.length ? 
                    colors.slice(0, wilayahData.length) : 
                    ['#E0E0E0'] // Gray color for no data
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
                                if (!wilayahData.length) {
                                    return [{
                                        text: 'Tidak Ada Data',
                                        fillStyle: '#E0E0E0',
                                        index: 0
                                    }];
                                }
                                return data.labels.map(function(label, i) {
                                    const value = data.datasets[0].data[i];
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return {
                                        text: `${label}: ${value} (${percentage}%)`,
                                        fillStyle: colors[i],
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

    // Bar Chart - Distribusi Risiko
    new Chart(document.getElementById('grafikRisiko'), {
        type: 'bar',
        data: {
            labels: @json($resikoLabels),
            datasets: [{
                label: 'Jumlah Wilayah',
                data: @json($resikoData->pluck('y')),
                backgroundColor: [
                    '#e31a1c',  // Merah untuk risiko tinggi
                    '#fd8d3c',  // Orange untuk risiko sedang
                    '#fecc5c'   // Kuning untuk risiko rendah
                ],
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Line Chart - Tren per Bulan
    new Chart(document.getElementById('grafikBulan'), {
        type: 'line',
        data: {
            labels: @json($bulanLabels),
            datasets: [{
                label: 'Jumlah Kasus',
                data: @json($bulanData),
                borderColor: '#36A2EB',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush
