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
            <div class="form-group row">
              <label for="tahun" class="col-sm-2 col-form-label">Pilih Tahun</label>
              <div class="col-sm-4">
                <select name="tahun" id="tahun" class="form-control" onchange="this.form.submit()">
                  @foreach ($daftarTahun as $th)
                    <option value="{{ $th }}" {{ $th == $tahunTerpilih ? 'selected' : '' }}>{{ $th }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </form>

          <h4 class="card-title text-center mb-4">Grafik Jumlah Pernikahan Dini Tahun {{ $tahunTerpilih }}</h4>

          <div class="row">
            <!-- Donut Chart per Wilayah -->
            <div class="col-md-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <h6 class="text-center">Grafik Pernikahan Dini per Desa</h6>
                  <canvas id="grafikWilayah"></canvas>
                </div>
              </div>
            </div>

            <!-- Bubble Chart Risiko -->
            <div class="col-md-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <h6 class="text-center">Grafik Risiko Pernikahan Dini</h6>
                  <canvas id="grafikRisiko"></canvas>
                  <div class="text-center mt-3">
                    <span class="badge bg-danger">Tinggi</span>
                    <span class="badge bg-warning text-dark">Sedang</span>
                    <span class="badge bg-info text-dark">Rendah</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Scatter Chart per Bulan -->
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <h6 class="text-center">Grafik Pernikahan Dini per Bulan</h6>
                  <canvas id="grafikBulan"></canvas>
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
    const wilayahLabels = @json($wilayahLabels);
    const wilayahData = @json($wilayahData);

    const resikoLabels = @json($resikoLabels);
    const resikoData = @json($resikoData);

    const bulanLabels = @json($bulanLabels);
    const bulanData = @json($bulanData);

    new Chart(document.getElementById('grafikWilayah'), {
        type: 'doughnut',
        data: {
            labels: wilayahLabels,
            datasets: [{
                label: 'Jumlah Pernikahan Dini',
                data: wilayahData,
                backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#FF9F40'],
                borderColor: ['#ffffff'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { label: (tooltipItem) => `${tooltipItem.label}: ${tooltipItem.raw}` } }
            }
        }
    });

    new Chart(document.getElementById('grafikRisiko'), {
        type: 'bubble',
        data: {
            labels: resikoLabels,
            datasets: [{
                label: 'Distribusi Risiko Pernikahan Dini',
                data: resikoData,
                backgroundColor: resikoData.map(d =>
                    d.kategori === 'Tinggi' ? '#e31a1c' :
                    d.kategori === 'Sedang' ? '#fd8d3c' : '#fecc5c'
                ),
                borderColor: '#ffffff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return `Jumlah: ${tooltipItem.raw.y} (${resikoLabels[tooltipItem.dataIndex]})`;
                        }
                    }
                }
            },
            scales: {
                x: { title: { display: true, text: 'Kategori Risiko' } },
                y: { title: { display: true, text: 'Jumlah' }, beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('grafikBulan'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Jumlah Pernikahan Dini per Bulan',
                data: bulanData.map((value, index) => ({ x: index, y: value })),
                backgroundColor: '#36A2EB',
                borderColor: '#ffffff',
                borderWidth: 1,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return `Jumlah: ${tooltipItem.raw.y} per bulan`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    type: 'linear',
                    title: { display: true, text: 'Bulan' },
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return bulanLabels[value] || value;
                        }
                    }
                },
                y: {
                    title: { display: true, text: 'Jumlah Pernikahan Dini' },
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
