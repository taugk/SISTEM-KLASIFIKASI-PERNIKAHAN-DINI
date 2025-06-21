@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="page-header">
    <h3 class="fw-bold mb-3">Tambah Data Pernikahan</h3>
    <ul class="breadcrumbs mb-3">
      <li class="nav-home">
        <a href="#"><i class="icon-home"></i></a>
      </li>
      <li class="separator"><i class="icon-arrow-right"></i></li>
      <li class="nav-item"><a href="{{ route('data_pernikahan.index') }}">Data Pernikahan</a></li>
      <li class="separator"><i class="icon-arrow-right"></i></li>
      <li class="nav-item"><a href="#">Tambah Data</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Form Data Pernikahan</div>
        </div>
        <div class="card-body">
          <form action="{{ route('data_pernikahan.tambahDataPost') }}" method="POST">
            @csrf
            <div class="row">

              {{-- SUAMI --}}
              <div class="col-md-6 col-lg-4">
                <h5>Data Suami</h5>
                <div class="form-group">
                  <label>Nama Suami</label>
                  <input type="text" name="nama_suami" class="form-control" value="{{ old('nama_suami') }}" required>
                </div>
                <div class="form-group">
                  <label>Tanggal Lahir Suami</label>
                  <input type="date" name="tanggal_lahir_suami" id="tanggal_lahir_suami" class="form-control" value="{{ old('tanggal_lahir_suami') }}" required>
                </div>
                <div class="form-group">
                  <label>Usia Suami</label>
                  <input type="number" id="usia_suami" name="usia_suami" class="form-control" value="{{ old('usia_suami') }}" min="15" required>
                  <small id="error_usia_suami" class="text-danger d-none">Usia suami minimal 15 tahun.</small>
                </div>
                <div class="form-group">
                  <label>Pendidikan Suami</label>
                  <select name="pendidikan_suami" class="form-control" required>
                    @foreach([
                      'TIDAK/BELUM SEKOLAH', 'TIDAK TAMAT SD/SEDERAJAT', 'TAMAT SD/SEDERAJAT',
                      'SLTP/SEDERAJAT', 'SLTA/SEDERAJAT', 'DIPLOMA I/II',
                      'AKADEMI/DIPLOMA III/S. MUDA', 'DIPLOMA IV/STRATA I',
                      'STRATA II', 'STRATA III'
                    ] as $p)
                      <option value="{{ $p }}" {{ old('pendidikan_suami') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>Pekerjaan Suami</label>
                  <select name="pekerjaan_suami" id="pekerjaan_suami" class="form-control @error('pekerjaan_suami') is-invalid @enderror" required>
                    @foreach([
                      'TIDAK BEKERJA', 'PELAJAR/MAHASISWA', 'MENGURUS RUMAH TANGGA','WIRASWASTA',
                      'PETANI/PEKEBUN', 'NELAYAN/PERIKANAN', 'PNS', 'TNI', 'POLRI',
                      'KARYAWAN SWASTA', 'KARYAWAN BUMN', 'KARYAWAN HONORER', 'BURUH HARIAN LEPAS',
                      'BURUH TANI', 'BURUH NELAYAN', 'WIRAUSAHA', 'PEDAGANG', 'GURU', 'DOKTER',
                      'PERAWAT', 'SOPIR', 'MEKANIK', 'PENATA RIAS', 'PENJAHIT', 'PENSIUNAN', 'LAINNYA'
                    ] as $job)
                      <option value="{{ $job }}" {{ old('pekerjaan_suami') == $job ? 'selected' : '' }}>{{ $job }}</option>
                    @endforeach
                  </select>
                  @error('pekerjaan_suami') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  <input type="text" name="pekerjaan_suami_lainnya" id="pekerjaan_suami_lainnya"
                    class="form-control mt-2 {{ old('pekerjaan_suami') == 'LAINNYA' ? '' : 'd-none' }}"
                    placeholder="Masukkan pekerjaan suami lainnya"
                    value="{{ old('pekerjaan_suami_lainnya') }}">
                </div>
                <div class="form-group">
                  <label>Status Suami</label>
                  <select name="status_suami" class="form-control" required>
                    <option value="BELUM KAWIN" {{ old('status_suami') == 'BELUM KAWIN' ? 'selected' : '' }}>BELUM KAWIN</option>
                    <option value="CERAI HIDUP" {{ old('status_suami') == 'CERAI HIDUP' ? 'selected' : '' }}>CERAI HIDUP</option>
                    <option value="CERAI MATI" {{ old('status_suami') == 'CERAI MATI' ? 'selected' : '' }}>CERAI MATI</option>
                  </select>
                </div>
              </div>

              {{-- ISTRI --}}
              <div class="col-md-6 col-lg-4">
                <h5>Data Istri</h5>
                <div class="form-group">
                  <label>Nama Istri</label>
                  <input type="text" name="nama_istri" class="form-control" value="{{ old('nama_istri') }}" required>
                </div>
                <div class="form-group">
                  <label>Tanggal Lahir Istri</label>
                  <input type="date" name="tanggal_lahir_istri" id="tanggal_lahir_istri" class="form-control" value="{{ old('tanggal_lahir_istri') }}" required>
                </div>
                <div class="form-group">
                  <label>Usia Istri</label>
                  <input type="number" id="usia_istri" name="usia_istri" class="form-control" value="{{ old('usia_istri') }}" min="15" required>
                  <small id="error_usia_istri" class="text-danger d-none">Usia istri minimal 15 tahun.</small>
                </div>
                <div class="form-group">
                  <label>Pendidikan Istri</label>
                  <select name="pendidikan_istri" class="form-control" required>
                    @foreach([
                      'TIDAK/BELUM SEKOLAH', 'TIDAK TAMAT SD/SEDERAJAT', 'TAMAT SD/SEDERAJAT',
                      'SLTP/SEDERAJAT', 'SLTA/SEDERAJAT', 'DIPLOMA I/II',
                      'AKADEMI/DIPLOMA III/S. MUDA', 'DIPLOMA IV/STRATA I',
                      'STRATA II', 'STRATA III'
                    ] as $p)
                      <option value="{{ $p }}" {{ old('pendidikan_istri') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>Pekerjaan Istri</label>
                  <select name="pekerjaan_istri" id="pekerjaan_istri" class="form-control @error('pekerjaan_istri') is-invalid @enderror" required>
                    @foreach([
                      'TIDAK BEKERJA', 'PELAJAR/MAHASISWA', 'MENGURUS RUMAH TANGGA','WIRASWASTA',
                      'PETANI/PEKEBUN', 'NELAYAN/PERIKANAN', 'PNS', 'TNI', 'POLRI',
                      'KARYAWAN SWASTA', 'KARYAWAN BUMN', 'KARYAWAN HONORER', 'BURUH HARIAN LEPAS',
                      'BURUH TANI', 'BURUH NELAYAN', 'WIRAUSAHA', 'PEDAGANG', 'GURU', 'DOKTER',
                      'PERAWAT', 'SOPIR', 'MEKANIK', 'PENATA RIAS', 'PENJAHIT', 'PENSIUNAN', 'LAINNYA'
                    ] as $job)
                      <option value="{{ $job }}" {{ old('pekerjaan_istri') == $job ? 'selected' : '' }}>{{ $job }}</option>
                    @endforeach
                  </select>
                  @error('pekerjaan_istri') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  <input type="text" name="pekerjaan_istri_lainnya" id="pekerjaan_istri_lainnya"
                    class="form-control mt-2 {{ old('pekerjaan_istri') == 'LAINNYA' ? '' : 'd-none' }}"
                    placeholder="Masukkan pekerjaan istri lainnya"
                    value="{{ old('pekerjaan_istri_lainnya') }}">
                </div>
                <div class="form-group">
                  <label>Status Istri</label>
                  <select name="status_istri" class="form-control" required>
                    <option value="BELUM KAWIN" {{ old('status_istri') == 'BELUM KAWIN' ? 'selected' : '' }}>BELUM KAWIN</option>
                    <option value="CERAI HIDUP" {{ old('status_istri') == 'CERAI HIDUP' ? 'selected' : '' }}>CERAI HIDUP</option>
                    <option value="CERAI MATI" {{ old('status_istri') == 'CERAI MATI' ? 'selected' : '' }}>CERAI MATI</option>
                  </select>
                </div>
              </div>

              {{-- DATA PERNIKAHAN --}}
              <div class="col-md-6 col-lg-4">
                <h5>Data Pernikahan</h5>
                <div class="form-group">
                  <label>Tanggal Akad</label>
                  <input type="date" name="tanggal_akad" class="form-control" value="{{ old('tanggal_akad') }}" required>
                </div>
                <div class="form-group">
                  <label for="wilayah_id">Kelurahan/Desa</label>
                  <select name="wilayah_id" id="wilayah_id" class="form-control" required>
                    <option value="">-- Pilih Kelurahan/Desa --</option>
                    @foreach ($kelurahan as $item)
                      <option value="{{ $item->id }}" {{ old('wilayah_id') == $item->id ? 'selected' : '' }}>{{ $item->desa }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group mt-4">
                  <button type="submit" id="btn_simpan" class="btn btn-primary">Simpan</button>
                  <a href="{{ route('data_pernikahan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
              </div>
          </form>
      </div>
    </div>
  </div>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

  function hitungUsia(tanggalLahir) {
    const today = new Date();
    const birthDate = new Date(tanggalLahir);
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    return age;
  }

  const inputTanggalSuami = document.getElementById('tanggal_lahir_suami');
  const inputTanggalIstri = document.getElementById('tanggal_lahir_istri');
  const inputUsiaSuami = document.getElementById('usia_suami');
  const inputUsiaIstri = document.getElementById('usia_istri');

  inputTanggalSuami.addEventListener('change', function () {
    const usia = hitungUsia(this.value);
    inputUsiaSuami.value = usia;
    document.getElementById('error_usia_suami').classList.toggle('d-none', usia >= 15);
  });

  inputTanggalIstri.addEventListener('change', function () {
    const usia = hitungUsia(this.value);
    inputUsiaIstri.value = usia;
    document.getElementById('error_usia_istri').classList.toggle('d-none', usia >= 15);
  });

  // Tampilkan input pekerjaan lain jika 'LAINNYA'
  document.getElementById('pekerjaan_suami').addEventListener('change', function () {
    document.getElementById('pekerjaan_suami_lainnya').classList.toggle('d-none', this.value !== 'LAINNYA');
  });

  document.getElementById('pekerjaan_istri').addEventListener('change', function () {
    document.getElementById('pekerjaan_istri_lainnya').classList.toggle('d-none', this.value !== 'LAINNYA');
  });

});
</script>
@endpush


