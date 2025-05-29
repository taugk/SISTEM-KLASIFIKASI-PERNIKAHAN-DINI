@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="page-header">
    <h3 class="fw-bold mb-3">Tambah Data Wilayah</h3>
    <ul class="breadcrumbs mb-3">
      <li class="nav-home">
        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
      </li>
      <li class="separator"><i class="icon-arrow-right"></i></li>
      <li class="nav-item"><a href="{{ route('data_wilayah.index') }}">Data Wilayah</a></li>
      <li class="separator"><i class="icon-arrow-right"></i></li>
      <li class="nav-item">Tambah Wilayah</li>
    </ul>
  </div>

  <div class="row d-flex justify-content-center">
    <div class="col-md-10">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Form Tambah Data Wilayah</div>
        </div>
        <div class="card-body">
          <form id="formTambahWilayah">
            @csrf
            <div class="form-group">
              <label for="provinsi">Provinsi</label>
              <select class="form-control" id="provinsi" name="provinsi" required>
                <option value="">Pilih Provinsi</option>
              </select>
            </div>

            <div class="form-group">
              <label for="kabupaten">Kabupaten</label>
              <select class="form-control" id="kabupaten" name="kabupaten" required disabled>
                <option value="">Pilih Kabupaten</option>
              </select>
            </div>

            <div class="form-group">
              <label for="kecamatan">Kecamatan</label>
              <select class="form-control" id="kecamatan" name="kecamatan" required disabled>
                <option value="">Pilih Kecamatan</option>
              </select>
            </div>

            <div class="form-group">
              <label for="desa">Desa/Kelurahan</label>
              <select class="form-control" id="desa" name="desa" required disabled>
                <option value="">Pilih Desa/Kelurahan</option>
              </select>
            </div>

            <div class="form-group">
              <label>Koordinat GeoJSON</label>
              <div class="alert alert-info mb-2">
                <h6 class="mb-1"><i class="fas fa-info-circle"></i> Cara Menggambar Area Wilayah:</h6>
                <ol class="pl-3 mb-0">
                  <li>Klik tombol polygon (bentuk segi banyak) di sebelah kiri peta</li>
                  <li>Klik di peta untuk mulai menggambar area</li>
                  <li>Klik beberapa titik untuk membentuk area wilayah</li>
                  <li>Klik titik pertama atau klik ganda untuk menyelesaikan gambar</li>
                  <li>Jika ingin menggambar ulang, klik tombol hapus (tong sampah) lalu gambar lagi</li>
                </ol>
              </div>
              <div id="map" style="height: 400px"></div>
              <input type="hidden" id="geojson" name="geojson" required>
              <div class="geojson-info">
                <div class="d-flex align-items-center mb-2">
                  <span class="badge badge-info mr-2">Status</span>
                  <span id="drawingStatus" class="text-muted">Belum ada area yang digambar</span>
                </div>
                <div id="geojsonPreview"></div>
              </div>
            </div>

            <div class="card-action">
              <button type="submit" class="btn btn-success" id="btnSubmit">
                <span class="btn-label"><i class="fa fa-save"></i></span> Simpan
              </button>
              <a href="{{ route('data_wilayah.index') }}" class="btn btn-danger">
                <span class="btn-label"><i class="fa fa-times"></i></span> Batal
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script>
  $(document).ready(function() {
    // Inisialisasi peta
    var map = L.map('map').setView([-6.9755, 108.4828], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // Inisialisasi Leaflet Draw
    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    var drawControl = new L.Control.Draw({
      draw: {
        marker: false,
        circle: false,
        circlemarker: false,
        rectangle: false,
        polyline: false,
        polygon: {
          allowIntersection: false,
          drawError: {
            color: '#e1e100',
            message: '<strong>Polygon tidak boleh berpotongan!</strong>'
          },
          shapeOptions: {
            color: '#3388ff'
          }
        }
      },
      edit: {
        featureGroup: drawnItems,
        remove: true
      }
    });
    map.addControl(drawControl);

    // Event handler untuk menangkap koordinat yang digambar
    map.on(L.Draw.Event.CREATED, function(event) {
      drawnItems.clearLayers();
      var layer = event.layer;
      drawnItems.addLayer(layer);
      
      // Ambil koordinat dari polygon
      var coordinates = layer.getLatLngs()[0].map(function(latLng) {
        return [latLng.lng, latLng.lat];
      });
      
      // Tambahkan titik pertama di akhir untuk menutup polygon
      coordinates.push(coordinates[0]);
      
      // Simpan koordinat ke input hidden
      var geojsonValue = JSON.stringify(coordinates);
      $('#geojson').val(geojsonValue);
      
      // Update status dan preview
      $('#drawingStatus').html('<span class="text-success">Area wilayah berhasil digambar</span>');
      $('#geojsonPreview').html('<strong>Koordinat tersimpan:</strong> ' + geojsonValue);
    });

    // Event handler untuk penghapusan polygon
    map.on(L.Draw.Event.DELETED, function(event) {
      $('#geojson').val('');
      $('#drawingStatus').html('<span class="text-warning">Area wilayah dihapus, silakan gambar ulang</span>');
      $('#geojsonPreview').html('');
    });

    // Event handler untuk mode menggambar
    map.on('draw:drawstart', function(event) {
      $('#drawingStatus').html('<span class="text-info">Sedang menggambar area...</span>');
    });

    // Event handler untuk pembatalan menggambar
    map.on('draw:drawstop', function(event) {
      if (!$('#geojson').val()) {
        $('#drawingStatus').html('<span class="text-muted">Belum ada area yang digambar</span>');
      }
    });

    // Load data provinsi saat halaman dimuat
    loadProvinsi();

    // Event handler untuk perubahan provinsi
    $('#provinsi').on('change', function() {
      const provinsiId = $(this).val();
      if (provinsiId) {
        loadKabupaten(provinsiId);
        $('#kabupaten').prop('disabled', false);
        $('#kecamatan, #desa').prop('disabled', true).html('<option value="">Pilih...</option>');
      } else {
        $('#kabupaten, #kecamatan, #desa').prop('disabled', true).html('<option value="">Pilih...</option>');
      }
    });

    // Event handler untuk perubahan kabupaten
    $('#kabupaten').on('change', function() {
      const kabupatenId = $(this).val();
      if (kabupatenId) {
        loadKecamatan(kabupatenId);
        $('#kecamatan').prop('disabled', false);
        $('#desa').prop('disabled', true).html('<option value="">Pilih...</option>');
      } else {
        $('#kecamatan, #desa').prop('disabled', true).html('<option value="">Pilih...</option>');
      }
    });

    // Event handler untuk perubahan kecamatan
    $('#kecamatan').on('change', function() {
      const kecamatanId = $(this).val();
      if (kecamatanId) {
        loadDesa(kecamatanId);
        $('#desa').prop('disabled', false);
      } else {
        $('#desa').prop('disabled', true).html('<option value="">Pilih...</option>');
      }
    });

    // Fungsi untuk memuat data provinsi
    function loadProvinsi() {
      $.ajax({
        url: "{{ route('api.wilayah.provinsi') }}",
        method: 'GET',
        success: function(response) {
          let html = '<option value="">Pilih Provinsi</option>';
          response.forEach(function(item) {
            html += `<option value="${item.id}">${item.nama}</option>`;
          });
          $('#provinsi').html(html);
        },
        error: function(xhr) {
          console.error('Error loading provinsi:', xhr);
          Swal.fire('Error', 'Gagal memuat data provinsi', 'error');
        }
      });
    }

    // Fungsi untuk memuat data kabupaten
    function loadKabupaten(provinsiId) {
      $.ajax({
        url: "{{ route('api.wilayah.kabupaten') }}",
        method: 'GET',
        data: { provinsi_id: provinsiId },
        success: function(response) {
          let html = '<option value="">Pilih Kabupaten</option>';
          response.forEach(function(item) {
            html += `<option value="${item.id}">${item.nama}</option>`;
          });
          $('#kabupaten').html(html);
        },
        error: function(xhr) {
          console.error('Error loading kabupaten:', xhr);
          Swal.fire('Error', 'Gagal memuat data kabupaten', 'error');
        }
      });
    }

    // Fungsi untuk memuat data kecamatan
    function loadKecamatan(kabupatenId) {
      $.ajax({
        url: "{{ route('api.wilayah.kecamatan') }}",
        method: 'GET',
        data: { kabupaten_id: kabupatenId },
        success: function(response) {
          let html = '<option value="">Pilih Kecamatan</option>';
          response.forEach(function(item) {
            html += `<option value="${item.id}">${item.nama}</option>`;
          });
          $('#kecamatan').html(html);
        },
        error: function(xhr) {
          console.error('Error loading kecamatan:', xhr);
          Swal.fire('Error', 'Gagal memuat data kecamatan', 'error');
        }
      });
    }

    // Fungsi untuk memuat data desa
    function loadDesa(kecamatanId) {
      $.ajax({
        url: "{{ route('api.wilayah.desa') }}",
        method: 'GET',
        data: { kecamatan_id: kecamatanId },
        success: function(response) {
          let html = '<option value="">Pilih Desa/Kelurahan</option>';
          response.forEach(function(item) {
            html += `<option value="${item.id}">${item.nama}</option>`;
          });
          $('#desa').html(html);
        },
        error: function(xhr) {
          console.error('Error loading desa:', xhr);
          Swal.fire('Error', 'Gagal memuat data desa', 'error');
        }
      });
    }

    // Handle form submission
    $('#formTambahWilayah').on('submit', function(e) {
      e.preventDefault();
      
      var geojsonValue = $('#geojson').val();
      console.log('Current GeoJSON value:', geojsonValue);

      // Validasi apakah polygon sudah digambar
      if (!geojsonValue || geojsonValue.trim() === '') {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Silakan gambar wilayah pada peta terlebih dahulu!'
        });
        return;
      }

      // Tampilkan loading
      Swal.fire({
        title: 'Mohon Tunggu',
        text: 'Sedang menyimpan data...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // Siapkan data
      var formData = {
        _token: $('input[name="_token"]').val(),
        provinsi: $('#provinsi option:selected').text(),
        kabupaten: $('#kabupaten option:selected').text(),
        kecamatan: $('#kecamatan option:selected').text(),
        desa: $('#desa option:selected').text(),
        geojson: geojsonValue
      };

      console.log('Sending data:', formData);

      // Kirim data dengan AJAX
      $.ajax({
        url: "{{ route('data_wilayah.tambahDataPost') }}",
        type: 'POST',
        data: formData,
        success: function(response) {
          console.log('Success Response:', response);
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: response.message,
            showConfirmButton: true
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = "{{ route('data_wilayah.index') }}";
            }
          });
        },
        error: function(xhr) {
          console.log('Error Response:', xhr.responseJSON);
          let message = 'Terjadi kesalahan, silakan coba lagi.';
          
          if (xhr.responseJSON) {
            if (xhr.responseJSON.errors) {
              message = Object.values(xhr.responseJSON.errors).flat().join('\n');
            } else if (xhr.responseJSON.message) {
              message = xhr.responseJSON.message;
            }
          }
          
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: message
          });
        }
      });
    });
  });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<style>
#map {
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 10px;
}
.geojson-info {
    margin-top: 10px;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
}
.badge {
    padding: 5px 10px;
    margin-right: 10px;
}
#drawingStatus {
    font-size: 14px;
}
#geojsonPreview {
    margin-top: 10px;
    font-size: 12px;
    word-break: break-all;
}
</style>
@endpush
