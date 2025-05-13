@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="card-body">
    <h4 class="card-title text-center mb-4">Statistik Pernikahan Dini</h4>

    <div class="row">
      <!-- Chart 1 -->
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <h6 class="text-center">Jumlah Pernikahan Dini per Wilayah</h6>
            <canvas id="chartWilayah"></canvas>
          </div>
        </div>
      </div>

      <!-- Chart 2 -->
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <h6 class="text-center">Distribusi Tingkat Risiko</h6>
            <canvas id="chartRisiko"></canvas>
          </div>
        </div>
      </div>

      <!-- Chart 3 -->
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h6 class="text-center">Tren Pernikahan Dini per Tahun</h6>
            <canvas id="chartTahun"></canvas>
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
    // Ambil data dari backend (Laravel controller)
    const labelsWilayah = @json($wilayahLabels);
    const dataWilayah = @json($wilayahData);

    const labelsRisiko = @json($resikoLabels);
    const dataRisiko = @json($resikoData);

    const labelsTahun = @json($tahunLabels);
    const dataTahun = @json($tahunData);

    // Chart 1: Bar Wilayah
    new Chart(document.getElementById('chartWilayah'), {
        type: 'bar',
        data: {
            labels: labelsWilayah,
            datasets: [{
                label: 'Jumlah Pernikahan Dini',
                data: dataWilayah,
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

    // Chart 2: Pie Risiko
    new Chart(document.getElementById('chartRisiko'), {
        type: 'pie',
        data: {
            labels: labelsRisiko,
            datasets: [{
                label: 'Distribusi Risiko',
                data: dataRisiko,
                backgroundColor: ['#e31a1c', '#fd8d3c', '#fecc5c']
            }]
        },
        options: {
            responsive: true
        }
    });

    // Chart 3: Line Tahun
    new Chart(document.getElementById('chartTahun'), {
        type: 'line',
        data: {
            labels: labelsTahun,
            datasets: [{
                label: 'Jumlah Pernikahan Dini',
                data: dataTahun,
                fill: false,
                borderColor: '#4BC0C0',
                tension: 0.1
            }]
        },
        options: {
            responsive: true
        }
    });
});
</script>
@endpush
