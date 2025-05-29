@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Dashboard</h3>
      <h6 class="op-7 mb-2">Ringkasan Data Pernikahan dan Pernikahan Dini</h6>
    </div>
  </div>

  <!-- Statistik Ringkas -->
  <div class="row">
    @foreach ([
      ['title' => 'Total Pernikahan', 'icon' => 'fas fa-users', 'value' => $totalPernikahan, 'color' => 'primary'],
      ['title' => 'Pernikahan Dini', 'icon' => 'fas fa-user-clock', 'value' => $pernikahanDini, 'color' => 'danger'],
      ['title' => 'Wilayah Terdata', 'icon' => 'fas fa-map-marker-alt', 'value' => $jumlahWilayah, 'color' => 'success'],
      ['title' => 'Materi Edukasi', 'icon' => 'fas fa-book-open', 'value' => $jumlahMateri, 'color' => 'info'],
    ] as $card)
    <div class="col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center text-{{ $card['color'] }}">
                <i class="{{ $card['icon'] }}"></i>
              </div>
            </div>
            <div class="col col-stats ms-3">
              <div class="numbers">
                <p class="card-category">{{ $card['title'] }}</p>
                <h4 class="card-title">{{ $card['value'] }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <!-- Grafik -->
  <div class="row">
    <div class="col-md-8">
      <div class="card card-round">
        <div class="card-header">
          <div class="card-title">Grafik Pernikahan Dini per Bulan</div>
        </div>
        <div class="card-body">
          <canvas id="grafikPerBulan"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-round">
        <div class="card-header">
          <div class="card-title">Distribusi Risiko</div>
        </div>
        <div class="card-body">
          <canvas id="grafikResiko"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabel Wilayah Risiko Tinggi -->
  <div class="row">
    <div class="col-md-12">
      <div class="card card-round">
        <div class="card-header">
          <div class="card-title">Wilayah Risiko Tinggi</div>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>Desa</th>
                <th>Kecamatan</th>
                <th>Jumlah Pernikahan Dini</th>
                <th>Periode</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($wilayahRisikoTinggi as $i => $wilayah)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $wilayah->desa }}</td>
                <td>{{ $wilayah->kecamatan }}</td>
                <td>{{ $wilayah->jumlah_pernikahan_dini }}</td>
                <td>{{ $wilayah->periode }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const bulan = @json($bulan);
  const total = @json($total);
  const labelRisiko = @json($labelRisiko);
  const jumlahRisiko = @json($jumlahRisiko);

  new Chart(document.getElementById('grafikPerBulan'), {
    type: 'line',
    data: {
      labels: bulan,
      datasets: [{
        label: 'Pernikahan Dini',
        data: total,
        borderColor: '#e31a1c',
        backgroundColor: 'rgba(227, 26, 28, 0.1)',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#e31a1c',
        pointRadius: 4,
        pointHoverRadius: 6
      }]
    },
    options: { 
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return `Jumlah: ${context.raw} kasus`;
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0,
            callback: function(value) {
              if (value % 1 === 0) {
                return value;
              }
            }
          },
          title: {
            display: true,
            text: 'Jumlah Kasus'
          }
        },
        x: {
          ticks: {
            maxRotation: 45,
            minRotation: 45
          },
          title: {
            display: true,
            text: 'Bulan'
          }
        }
      }
    }
  });

  new Chart(document.getElementById('grafikResiko'), {
    type: 'pie',
    data: {
      labels: labelRisiko,
      datasets: [{
        label: 'Risiko Wilayah',
        data: jumlahRisiko,
        backgroundColor: ['#e31a1c','#fd8d3c','#fecc5c']
      }]
    },
    options: { 
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 20,
            font: {
              size: 12
            }
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              let label = context.label || '';
              let value = context.raw || 0;
              let total = context.dataset.data.reduce((a, b) => a + b, 0);
              let percentage = Math.round((value * 100) / total);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });
});
</script>
@endpush
