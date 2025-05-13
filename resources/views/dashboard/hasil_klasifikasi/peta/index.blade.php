@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="card-body">
    <h4 class="card-title text-center">Peta Sebaran Pernikahan Dini</h4>
    <div class="col-md-10 ms-auto me-auto">
      <div class="mb-3 text-center">
        <label for="filterResiko" class="form-label me-2">Filter Risiko:</label>
        <select id="filterResiko" class="form-select w-auto d-inline-block me-4">
            <option value="semua">Semua</option>
            <option value="tinggi">Tinggi</option>
            <option value="sedang">Sedang</option>
            <option value="rendah">Rendah</option>
        </select>

        <label for="filterJumlah" class="form-label me-2">Jumlah Minimal:</label>
        <input type="number" id="filterJumlah" value="0" min="0" class="form-control w-auto d-inline-block">
      </div>

      <div class="mapcontainer">
        <div id="map" style="height: 1000px; width: 100%"></div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const geojson = @json($geojson);
    const map = L.map('map', {
        center: [-6.9796, 108.4847],
        zoom: 11,
        zoomControl: true,
        attributionControl: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    let geoLayer;

    function getColor(jumlah) {
        return jumlah > 5 ? '#800026' :
               jumlah > 3 ? '#BD0026' :
               jumlah > 2 ? '#E31A1C' :
               jumlah > 1 ? '#FC4E2A' :
               jumlah > 0 ? '#FD8D3C' :
                            '#FFEDA0';
    }

    function style(feature) {
        return {
            fillColor: getColor(feature.properties.jumlah_pernikahan_dini),
            weight: 1,
            opacity: 1,
            color: '#555',
            dashArray: '3',
            fillOpacity: 0.7
        };
    }

    function onEachFeature(feature, layer) {
        const p = feature.properties;
        layer.bindPopup(
            `<b>${p.NAMOBJ || 'Tidak diketahui'}</b><br>` +
            `Risiko: ${p.resiko || 'Tidak tersedia'}<br>` +
            `Jumlah Pernikahan Dini: ${p.jumlah_pernikahan_dini ?? 0}`
        );
    }

    function renderFilteredLayer() {
        const resiko = document.getElementById('filterResiko').value;
        const minJumlah = parseInt(document.getElementById('filterJumlah').value) || 0;

        const filteredFeatures = geojson.features.filter(f => {
            const jumlah = f.properties.jumlah_pernikahan_dini || 0;
            const resikoMatch = resiko === 'semua' || f.properties.resiko === resiko;
            const jumlahMatch = jumlah >= minJumlah;
            return resikoMatch && jumlahMatch;
        });

        if (geoLayer) map.removeLayer(geoLayer);

        geoLayer = L.geoJson({ type: "FeatureCollection", features: filteredFeatures }, {
            style: style,
            onEachFeature: onEachFeature
        }).addTo(map);

        if (filteredFeatures.length > 0) {
            map.fitBounds(geoLayer.getBounds());
        }
    }

    // Inisialisasi awal
    renderFilteredLayer();

    // Event listeners
    document.getElementById('filterResiko').addEventListener('change', renderFilteredLayer);
    document.getElementById('filterJumlah').addEventListener('input', renderFilteredLayer);

    // Legend
    const legend = L.control({position: 'bottomright'});
    legend.onAdd = function (map) {
        const div = L.DomUtil.create('div', 'info legend');
        const grades = [0, 1, 2, 3, 5];
        div.innerHTML = '<h6><strong>Jumlah Pernikahan Dini</strong></h6>';
        for (let i = 0; i < grades.length; i++) {
            div.innerHTML +=
                '<i style="background:' + getColor(grades[i] + 1) + '"></i> ' +
                grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
        }
        return div;
    };
    legend.addTo(map);
});
</script>

<style>
    .legend {
        line-height: 18px;
        color: #555;
        background: white;
        padding: 6px 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        font-size: 13px;
    }

    .legend i {
        width: 18px;
        height: 18px;
        float: left;
        margin-right: 8px;
        opacity: 0.7;
    }

    .form-select,
    .form-control {
        font-size: 14px;
        height: 36px;
    }
</style>
@endpush
