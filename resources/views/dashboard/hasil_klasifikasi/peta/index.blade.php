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
                <button type="button" id="btnCetakPeta" class="btn btn-primary d-print-none">
                    <i class="fas fa-print"></i> Cetak Peta
                </button>
            </div>

            {{-- Filter Form --}}
            <div class="card mb-4 d-print-none">
                <div class="card-body">
                    <form method="GET" action="{{ route('hasil_klasifikasi.peta_sebaran') }}" class="row g-3">
                        <div class="col-md-2">
                            <select name="filter_tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach ($tahunOptions as $tahun)
                                    <option value="{{ $tahun }}" {{ $selectedTahun == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">

                            <select name="filter_kelurahan" id="filter_kelurahan" class="form-control">
                                <option value="">Semua Kelurahan</option>
                                @foreach($kelurahans as $kelurahan)
                                    <option value="{{ $kelurahan }}" {{ request('filter_kelurahan') == $kelurahan ? 'selected' : '' }}>
                                        {{ $kelurahan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">

                            <select name="filter_resiko" id="filter_resiko" class="form-control">
                                <option value="">Semua Resiko</option>
                                @foreach($resikoOptions as $resiko)
                                    <option value="{{ $resiko }}" {{ $selectedResiko == $resiko ? 'selected' : '' }}>
                                        {{ ucfirst($resiko) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-center gap-2">
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

    /* Form styles */
    .form-control {
        height: 38px;
        padding: .375rem .75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }

    .form-control:focus {
        color: #212529;
        background-color: #fff;
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
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

    function getBadgeClass(resiko) {
        switch(resiko.toLowerCase()) {
            case 'tinggi': return 'badge bg-danger';
            case 'sedang': return 'badge bg-warning text-dark';
            case 'rendah': return 'badge bg-success';
            default: return 'badge bg-secondary';
        }
    }

    function style(feature) {
        return {
            fillColor: getColor(feature.properties.resiko_wilayah),
            weight: 2,
            opacity: 1,
            color: 'white',
            dashArray: '3',
            fillOpacity: 0.7
        };
    }

    // Function to filter features based on selected criteria
    function filterFeatures(feature) {
        const selectedKelurahan = $('#filter_kelurahan').val();
        const selectedResiko = $('#filter_resiko').val();

        if (selectedKelurahan && feature.properties.NAMOBJ !== selectedKelurahan) return false;
        if (selectedResiko && feature.properties.resiko_wilayah !== selectedResiko) return false;

        return true;
    }

    // Function to update map with filtered data
    function updateMap() {
        if (window.geojsonLayer) {
            map.removeLayer(window.geojsonLayer);
        }

        window.geojsonLayer = L.geoJSON(geojsonData, {
        style: style,
            filter: filterFeatures,
        onEachFeature: function(feature, layer) {
            // Get properties with default values
            const properties = feature.properties || {};
            const namaDesa = properties.NAMOBJ || 'Data tidak tersedia';
            const kecamatan = properties.WADMKC || 'Data tidak tersedia';
            const kabupaten = properties.WADMKK || 'Data tidak tersedia';
            const provinsi = properties.WADMPR || 'Data tidak tersedia';
            const resiko = properties.resiko_wilayah || 'tidak tersedia';
            const jumlahPernikahanDini = properties.jumlah_pernikahan_dini || 0;
            const rekomendasi = properties.rekomendasi || 'Data tidak tersedia';

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

        // Fit map to filtered features
        const bounds = window.geojsonLayer.getBounds();
        if (bounds.isValid()) {
            map.fitBounds(bounds);
        }
    }

    // Initial map update
    updateMap();

    // Update map when filters change
    $('#filter_kelurahan, #filter_resiko').change(function() {
        updateMap();
    });

    // Print function
    $('#btnCetakPeta').click(function() {
        // Force map to refresh before capturing
        map.invalidateSize();

        // Wait for map to fully load
        setTimeout(function() {
            // Capture the map
            html2canvas(document.getElementById('map'), {
                useCORS: true,
                allowTaint: true,
                scrollX: 0,
                scrollY: 0,
                scale: 2
            }).then(function(canvas) {
                // Get filter values
                var kelurahan = $('#filter_kelurahan option:selected').text() || 'Semua Kelurahan';
                var resiko = $('#filter_resiko option:selected').text() || 'Semua Tingkat Resiko';

                // Get filtered data for statistics
                var filteredFeatures = geojsonData.features.filter(filterFeatures);
                var stats = {
                    tinggi: 0,
                    sedang: 0,
                    rendah: 0,
                    total_pernikahan_dini: 0
                };

                // Calculate statistics
                filteredFeatures.forEach(function(feature) {
                    var resiko = feature.properties.resiko_wilayah;
                    if (stats.hasOwnProperty(resiko)) {
                        stats[resiko]++;
                    }
                    stats.total_pernikahan_dini += parseInt(feature.properties.jumlah_pernikahan_dini || 0);
                });

                // Create table rows
                var tableRows = filteredFeatures.map(function(feature) {
                    var props = feature.properties;
                    return `
                        <tr>
                            <td>${props.NAMOBJ || '-'}</td>
                            <td>${props.WADMKC || '-'}</td>
                            <td>${props.WADMKK || '-'}</td>
                            <td>${props.WADMPR || '-'}</td>
                            <td><span class="${getBadgeClass(props.resiko_wilayah)}">${props.resiko_wilayah}</span></td>
                            <td class="text-end">${props.jumlah_pernikahan_dini || 0}</td>
                        </tr>
                    `;
                }).join('');

                // Create print window
                var printWindow = window.open('', '_blank');

                // Write the content to the print window
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Peta Sebaran Resiko Pernikahan Dini</title>
                        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
                        <style>
                            body { padding: 20px; font-family: Arial, sans-serif; }
                            .map-container { margin: 20px 0; }
                            .map-container img { width: 100%; height: auto; }
                            @media print {
                                @page { size: landscape; margin: 1cm; }
                                .no-print { display: none; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container-fluid">
                            <h4 class="text-center mb-3">Peta Sebaran Resiko Pernikahan Dini</h4>
                            <p class="text-center mb-4">${new Date().toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            })}</p>

                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Kelurahan:</strong> ${kelurahan}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Tingkat Resiko:</strong> ${resiko}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="map-container">
                                <img src="${canvas.toDataURL()}" alt="Peta Sebaran">
                            </div>

                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-danger text-white">
                                                <div class="card-body">
                                                    <h6>Resiko Tinggi</h6>
                                                    <h4>${stats.tinggi} area</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-warning">
                                                <div class="card-body">
                                                    <h6>Resiko Sedang</h6>
                                                    <h4>${stats.sedang} area</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body">
                                                    <h6>Resiko Rendah</h6>
                                                    <h4>${stats.rendah} area</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-info">
                                                <div class="card-body">
                                                    <h6>Total Pernikahan Dini</h6>
                                                    <h4>${stats.total_pernikahan_dini}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Desa/Kelurahan</th>
                                            <th>Kecamatan</th>
                                            <th>Kabupaten</th>
                                            <th>Provinsi</th>
                                            <th>Tingkat Resiko</th>
                                            <th class="text-end">Jumlah Pernikahan Dini</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${tableRows}
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 text-center no-print">
                                <button onclick="window.print()" class="btn btn-primary">
                                    <i class="fas fa-print"></i> Cetak
                                </button>
                            </div>
                        </div>
                    </body>
                    </html>
                `);

                printWindow.document.close();
            });
        }, 1000);
    });
});
</script>
@endpush
