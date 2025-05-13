@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="card-body">
    <h4 class="card-title text-center mb-4">Grafik Jumlah Pernikahan Dini</h4>

    <div class="row">
      <!-- Grafik 1: Per Wilayah -->
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <h6 class="text-center">Grafik Pernikahan Dini per Desa</h6>
            <canvas id="grafikWilayah"></canvas>
          </div>
        </div>
      </div>

      <!-- Grafik 2: Per Risiko -->
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <h6 class="text-center">Grafik Risiko Pernikahan Dini</h6>
            <canvas id="grafikRisiko"></canvas>
          </div>
        </div>
      </div>

      <!-- Grafik 3: Per Periode -->
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h6 class="text-center">Grafik Tren Pernikahan Dini per Tahun</h6>
            <canvas id="grafikTahun"></canvas>
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

    const tahunLabels = @json($tahunLabels);
    const tahunData = @json($tahunData);

    // Grafik 1: Bar Wilayah
    new Chart(document.getElementById('grafikWilayah'), {
        type: 'bar',
        data: {
            labels: wilayahLabels,
            datasets: [{
                label: 'Jumlah Pernikahan Dini',
                data: wilayahData,
                backgroundColor: '#36A2EB'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Grafik 2: Pie Risiko
    new Chart(document.getElementById('grafikRisiko'), {
        type: 'pie',
        data: {
            labels: resikoLabels,
            datasets: [{
                label: 'Distribusi Risiko',
                data: resikoData,
                backgroundColor: ['#e31a1c', '#fd8d3c', '#fecc5c']
            }]
        },
        options: { responsive: true }
    });

    // Grafik 3: Line Tahun
    new Chart(document.getElementById('grafikTahun'), {
        type: 'line',
        data: {
            labels: tahunLabels,
            datasets: [{
                label: 'Jumlah Pernikahan Dini',
                data: tahunData,
                fill: false,
                borderColor: '#4BC0C0',
                tension: 0.3
            }]
        },
        options: { responsive: true }
    });
});
</script>
@endpush
