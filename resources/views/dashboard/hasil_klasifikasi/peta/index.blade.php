@extends('layouts.app')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            {{-- Header with Print Button --}}
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-3">Peta Sebaran Resiko Pernikahan Dini</h3>
                    <ul class="breadcrumbs mb-3">
                        <li class="nav-home">
                            <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                        </li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item">Hasil Klasifikasi</li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item">Peta Sebaran</li>
                    </ul>
                </div>
                <button onclick="printMap()" class="btn btn-primary d-print-none">
                    <i class="fas fa-print"></i> Cetak Peta
                </button>
            </div>

            {{-- Filter Form --}}
            <div class="card mb-4 d-print-none">
                <div class="card-body">
                    <form method="GET" action="{{ route('hasil_klasifikasi.peta_sebaran') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="filter_provinsi" class="form-label">Provinsi</label>
                            <select name="filter_provinsi" id="filter_provinsi" class="form-control">
                                <option value="">Semua Provinsi</option>
                                @foreach($provinsis as $provinsi)
                                    <option value="{{ $provinsi }}" {{ $selectedProvinsi == $provinsi ? 'selected' : '' }}>
                                        {{ $provinsi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_kabupaten" class="form-label">Kabupaten</label>
                            <select name="filter_kabupaten" id="filter_kabupaten" class="form-control">
                                <option value="">Semua Kabupaten</option>
                                @foreach($kabupatens as $kabupaten)
                                    <option value="{{ $kabupaten }}" {{ $selectedKabupaten == $kabupaten ? 'selected' : '' }}>
                                        {{ $kabupaten }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_kecamatan" class="form-label">Kecamatan</label>
                            <select name="filter_kecamatan" id="filter_kecamatan" class="form-control">
                                <option value="">Semua Kecamatan</option>
                                @foreach($kecamatans as $kecamatan)
                                    <option value="{{ $kecamatan }}" {{ $selectedKecamatan == $kecamatan ? 'selected' : '' }}>
                                        {{ $kecamatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_resiko" class="form-label">Tingkat Resiko</label>
                            <select name="filter_resiko" id="filter_resiko" class="form-control">
                                <option value="">Semua Resiko</option>
                                @foreach($resikoOptions as $resiko)
                                    <option value="{{ $resiko }}" {{ $selectedResiko == $resiko ? 'selected' : '' }}>
                                        {{ ucfirst($resiko) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('hasil_klasifikasi.peta_sebaran') }}" class="btn btn-secondary">
                                <i class="fas fa-sync"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Print Header --}}
            <div class="d-none d-print-block mb-4 text-center">
                <h4 class="mb-1">Peta Sebaran Resiko Pernikahan Dini</h4>
                <p class="mb-0">{{ now()->format('d F Y') }}</p>
            </div>

            {{-- Map Container --}}
            <div class="card">
                <div class="card-body">
                    <div id="map" style="height: 600px;"></div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Keterangan</h5>
                    <div class="d-flex gap-4">
                        <div class="d-flex align-items-center">
                            <div style="width: 20px; height: 20px; background-color: #ff0000; margin-right: 8px;"></div>
                            <span>Resiko Tinggi</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div style="width: 20px; height: 20px; background-color: #ffa500; margin-right: 8px;"></div>
                            <span>Resiko Sedang</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div style="width: 20px; height: 20px; background-color: #008000; margin-right: 8px;"></div>
                            <span>Resiko Rendah</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    #map {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .legend {
        background: white;
        padding: 10px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    /* Override Leaflet's default popup styles */
    .leaflet-popup-content-wrapper {
        padding: 0 !important;
        border-radius: 4px !important;
        overflow: hidden;
    }
    .leaflet-popup-content {
        margin: 0 !important;
        line-height: 1.2;
    }
    .leaflet-container a.leaflet-popup-close-button {
        top: 2px;
        right: 2px;
        padding: 0;
        width: 14px;
        height: 14px;
        font: 14px/14px Tahoma, Verdana, sans-serif;
        color: #666;
        z-index: 1000;
    }
    .leaflet-popup-tip-container {
        margin-top: -1px;
    }
    /* Custom card styles */
    .leaflet-popup .card {
        margin: 0;
        border: none;
    }
    .leaflet-popup .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
    }
    .leaflet-popup .card-body {
        background-color: white;
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
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .card-body {
            padding: 0 !important;
        }
        #map {
            height: 500px !important;
            page-break-inside: avoid;
            border: 1px solid #ddd;
        }
        .breadcrumbs {
            display: none;
        }
        .leaflet-control-zoom {
            display: none;
        }
        .leaflet-control-attribution {
            display: none;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize the map
    var map = L.map('map').setView([-6.9755, 108.4828], 13);
    
    // Add the OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Parse the GeoJSON data
    var geojsonData = {!! $geojson !!};

    // Style function for the GeoJSON features
    function getColor(resiko) {
        return resiko === 'tinggi' ? '#ff0000' :
               resiko === 'sedang' ? '#ffa500' :
                                    '#008000';
    }

    function style(feature) {
        return {
            fillColor: getColor(feature.properties.resiko),
            weight: 2,
            opacity: 1,
            color: 'white',
            dashArray: '3',
            fillOpacity: 0.7
        };
    }

    // Add GeoJSON layer
    var geojsonLayer = L.geoJSON(geojsonData, {
        style: style,
        onEachFeature: function(feature, layer) {
            // Get properties with default values
            const properties = feature.properties || {};
            const namaDesa = properties.NAMOBJ || 'Data tidak tersedia';
            const kecamatan = properties.WADMKC || 'Data tidak tersedia';
            const kabupaten = properties.WADMKK || 'Data tidak tersedia';
            const provinsi = properties.WADMPR || 'Data tidak tersedia';
            const resiko = properties.resiko_wilayah || 'tidak tersedia';
            const jumlahPernikahanDini = properties.jumlah_pernikahan_dini || 0;

            // Get badge class based on risk level
            const getBadgeClass = (resiko) => {
                switch(resiko.toLowerCase()) {
                    case 'tinggi': return 'badge bg-danger';
                    case 'sedang': return 'badge bg-warning text-dark';
                    case 'rendah': return 'badge bg-success';
                    default: return 'badge bg-secondary';
                }
            };
            
            var popupContent = `
                <div class="card shadow-sm border-0" style="width: 160px;">
                    <div class="card-header py-1 px-2 bg-light">
                        <h6 class="card-title mb-0" style="font-size: 11px;">${namaDesa}</h6>
                    </div>
                    <div class="card-body p-1">
                        <div class="small" style="font-size: 10px;">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Kec.</span>
                                <span>${kecamatan}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Kab.</span>
                                <span>${kabupaten}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Prov.</span>
                                <span>${provinsi}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted">Resiko</span>
                                <span class="${getBadgeClass(resiko)}" style="font-size: 9px; padding: 3px 6px;">
                                    ${resiko.charAt(0).toUpperCase() + resiko.slice(1)}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Jml. Nikah Dini</span>
                                <span class="badge bg-info text-dark" style="font-size: 9px; padding: 3px 6px;">
                                    ${jumlahPernikahanDini}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            layer.bindPopup(popupContent);
        }
    }).addTo(map);

    // Fit the map to the GeoJSON bounds
    map.fitBounds(geojsonLayer.getBounds());

    // Cascade dropdowns
    $('#filter_provinsi').change(function() {
        var provinsi = $(this).val();
        if (provinsi) {
            $('#filter_kabupaten').prop('disabled', false);
            // You can add AJAX call here to load kabupaten based on provinsi
        } else {
            $('#filter_kabupaten').prop('disabled', true).val('');
            $('#filter_kecamatan').prop('disabled', true).val('');
        }
    });

    $('#filter_kabupaten').change(function() {
        var kabupaten = $(this).val();
        if (kabupaten) {
            $('#filter_kecamatan').prop('disabled', false);
            // You can add AJAX call here to load kecamatan based on kabupaten
        } else {
            $('#filter_kecamatan').prop('disabled', true).val('');
        }
    });

    // Print function
    window.printMap = function() {
        // Create print-specific content
        const timestamp = new Date().toLocaleString();
        const printHeader = document.createElement('div');
        printHeader.className = 'd-none d-print-block text-center mb-4';
        printHeader.innerHTML = `
            <h4 class="mb-1">Peta Sebaran Resiko Pernikahan Dini</h4>
            <p class="mb-0">${timestamp}</p>
        `;

        // Add print header temporarily
        const mapContainer = document.getElementById('map').parentElement;
        mapContainer.insertBefore(printHeader, document.getElementById('map'));

        // Trigger print
        window.print();

        // Remove print header after printing
        setTimeout(() => {
            printHeader.remove();
        }, 1000);
    };

    // Handle print media changes
    let mediaQueryList = window.matchMedia('print');
    mediaQueryList.addListener(function(mql) {
        if (mql.matches) {
            // Before print
            map.invalidateSize();
        } else {
            // After print
            setTimeout(() => {
                map.invalidateSize();
            }, 500);
        }
    });
});
</script>
@endpush
