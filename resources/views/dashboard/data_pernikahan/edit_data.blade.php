@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="page-header">
    <h3 class="fw-bold mb-3">Edit Data Pernikahan</h3>
    <ul class="breadcrumbs mb-3">
      <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
      <li class="separator"><i class="icon-arrow-right"></i></li>
      <li class="nav-item"><a href="{{ route('data_pernikahan.index') }}">Data Pernikahan</a></li>
    </ul>
  </div>

  <div class="col-md-12">
    <div class="card">
      <div class="card-header"><div class="card-title">Form Edit Data Pernikahan</div></div>
      <div class="card-body">
        <form action="{{ route('data_pernikahan.update', $data->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row">
            {{-- SUAMI --}}
            <div class="col-md-6 col-lg-4">
              <h5>Data Suami</h5>
              <div class="form-group">
                <label>Nama Suami</label>
                <input type="text" name="nama_suami" value="{{ old('nama_suami', $data->nama_suami) }}"
                  class="form-control @error('nama_suami') is-invalid @enderror" required>
                @error('nama_suami') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-group">
                <label>Tanggal Lahir Suami</label>
                <input type="date" name="tanggal_lahir_suami"
                  value="{{ old('tanggal_lahir_suami', \Carbon\Carbon::parse($data->tanggal_lahir_suami)->format('Y-m-d')) }}"
                  class="form-control @error('tanggal_lahir_suami') is-invalid @enderror" required>
                @error('tanggal_lahir_suami') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-group">
                <label>Usia Suami</label>
                <input type="number" name="usia_suami" min="15" id="usia_suami"
                  value="{{ old('usia_suami', $data->usia_suami) }}"
                  class="form-control @error('usia_suami') is-invalid @enderror" required>
                @error('usia_suami') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-group">
                <label>Pendidikan Suami</label>
                <select name="pendidikan_suami" id="pendidikan_suami"
                  class="form-control @error('pendidikan_suami') is-invalid @enderror" required>
                  @foreach([
                    'TIDAK/BELUM SEKOLAH', 'TIDAK TAMAT SD/SEDERAJAT', 'TAMAT SD/SEDERAJAT',
                    'SLTP/SEDERAJAT', 'SLTA/SEDERAJAT', 'DIPLOMA I/II',
                    'AKADEMI/DIPLOMA III/S. MUDA', 'DIPLOMA IV/STRATA I',
                    'STRATA II', 'STRATA III'
                  ] as $p)
                    <option value="{{ $p }}" {{ old('pendidikan_suami', $data->pendidikan_suami) == $p ? 'selected' : '' }}>{{ $p }}</option>
                  @endforeach
                </select>
                @error('pendidikan_suami') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-group">
                <label>Pekerjaan Suami</label>
                <select name="pekerjaan_suami" id="pekerjaan_suami"
                  class="form-control @error('pekerjaan_suami') is-invalid @enderror" required>
                  @foreach([
                    'TIDAK BEKERJA', 'PELAJAR/MAHASISWA', 'MENGURUS RUMAH TANGGA','WIRASWASTA',
                    'PETANI/PEKEBUN', 'NELAYAN/PERIKANAN', 'PNS', 'TNI', 'POLRI',
                    'KARYAWAN SWASTA', 'KARYAWAN BUMN', 'KARYAWAN HONORER', 'BURUH HARIAN LEPAS',
                    'BURUH TANI', 'BURUH NELAYAN', 'WIRAUSAHA', 'PEDAGANG', 'GURU', 'DOKTER',
                    'PERAWAT', 'SOPIR', 'MEKANIK', 'PENATA RIAS', 'PENJAHIT', 'PENSIUNAN', 'LAINNYA'
                  ] as $job)
                    <option value="{{ $job }}" {{ old('pekerjaan_suami', $data->pekerjaan_suami) == $job ? 'selected' : '' }}>{{ $job }}</option>
                  @endforeach
                </select>
                @error('pekerjaan_suami') <div class="invalid-feedback">{{ $message }}</div> @enderror

                <input type="text" name="pekerjaan_suami_lainnya" id="pekerjaan_suami_lainnya"
                    class="form-control mt-2 {{ old('pekerjaan_suami') == 'LAINNYA' ? '' : 'd-none' }}"
                    placeholder="Masukkan pekerjaan suami lainnya"
                    value="{{ old('pekerjaan_suami_lainnya', $data->pekerjaan_suami_lainnya ?? '') }}">
              </div>
              <div class="form-group">
                <label>Status Suami</label>
                <select name="status_suami"
                  class="form-control @error('status_suami') is-invalid @enderror" required>
                  @foreach(['BELUM KAWIN', 'CERAI HIDUP', 'CERAI MATI'] as $status)
                    <option value="{{ $status }}" {{ old('status_suami', $data->status_suami) == $status ? 'selected' : '' }}>{{ $status }}</option>
                  @endforeach
                </select>
                @error('status_suami') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- ISTRI --}}
            <div class="col-md-6 col-lg-4">
              <h5>Data Istri</h5>
              <div class="form-group">
                <label>Nama Istri</label>
                <input type="text" name="nama_istri" value="{{ old('nama_istri', $data->nama_istri) }}"
                  class="form-control @error('nama_istri') is-invalid @enderror" required>
                @error('nama_istri') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-group">
                <label>Tanggal Lahir Istri</label>
                <input type="date" name="tanggal_lahir_istri"
                  value="{{ old('tanggal_lahir_istri', \Carbon\Carbon::parse($data->tanggal_lahir_istri)->format('Y-m-d')) }}"
                  class="form-control @error('tanggal_lahir_istri') is-invalid @enderror" required>
                @error('tanggal_lahir_istri') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-group">
                <label>Usia Istri</label>
                <input type="number" name="usia_istri" min="15" id="usia_istri"
                  value="{{ old('usia_istri', $data->usia_istri) }}"
                  class="form-control @error('usia_istri') is-invalid @enderror" required>
                @error('usia_istri') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-group">
                <label>Pendidikan Istri</label>
                <select name="pendidikan_istri" id="pendidikan_istri"
                  class="form-control @error('pendidikan_istri') is-invalid @enderror" required>
                  @foreach([
                    'TIDAK/BELUM SEKOLAH', 'TIDAK TAMAT SD/SEDERAJAT', 'TAMAT SD/SEDERAJAT',
                    'SLTP/SEDERAJAT', 'SLTA/SEDERAJAT', 'DIPLOMA I/II',
                    'AKADEMI/DIPLOMA III/S. MUDA', 'DIPLOMA IV/STRATA I',
                    'STRATA II', 'STRATA III'
                  ] as $p)
                    <option value="{{ $p }}" {{ old('pendidikan_istri', $data->pendidikan_istri) == $p ? 'selected' : '' }}>{{ $p }}</option>
                  @endforeach
                </select>
                @error('pendidikan_istri') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-group">
                <label>Pekerjaan Istri</label>
                <select name="pekerjaan_istri" id="pekerjaan_istri"
                  class="form-control @error('pekerjaan_istri') is-invalid @enderror" required>
                  @foreach([
                    'TIDAK BEKERJA', 'PELAJAR/MAHASISWA', 'MENGURUS RUMAH TANGGA','WIRASWASTA',
                    'PETANI/PEKEBUN', 'NELAYAN/PERIKANAN', 'PNS', 'TNI', 'POLRI',
                    'KARYAWAN SWASTA', 'KARYAWAN BUMN', 'KARYAWAN HONORER', 'BURUH HARIAN LEPAS',
                    'BURUH TANI', 'BURUH NELAYAN', 'WIRAUSAHA', 'PEDAGANG', 'GURU', 'DOKTER',
                    'PERAWAT', 'SOPIR', 'MEKANIK', 'PENATA RIAS', 'PENJAHIT', 'PENSIUNAN', 'LAINNYA'
                  ] as $job)
                    <option value="{{ $job }}" {{ old('pekerjaan_istri', $data->pekerjaan_istri) == $job ? 'selected' : '' }}>{{ $job }}</option>
                  @endforeach
                </select>
                @error('pekerjaan_istri') <div class="invalid-feedback">{{ $message }}</div> @enderror

                <input type="text" name="pekerjaan_istri_lainnya" id="pekerjaan_istri_lainnya"
                class="form-control mt-2 {{ old('pekerjaan_istri') == 'LAINNYA' ? '' : 'd-none' }}"
                placeholder="Masukkan pekerjaan istri lainnya"
                value="{{ old('pekerjaan_istri_lainnya', $data->pekerjaan_istri_lainnya ?? '') }}">
              </div>
              <div class="form-group">
                <label>Status Istri</label>
                <select name="status_istri"
                  class="form-control @error('status_istri') is-invalid @enderror" required>
                  @foreach(['BELUM KAWIN', 'CERAI HIDUP', 'CERAI MATI'] as $status)
                    <option value="{{ $status }}" {{ old('status_istri', $data->status_istri) == $status ? 'selected' : '' }}>{{ $status }}</option>
                  @endforeach
                </select>
                @error('status_istri') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- PERNIKAHAN --}}
            <div class="col-md-6 col-lg-4">
              <h5>Data Pernikahan</h5>
              <div class="form-group">
                <label>Tanggal Akad</label>
                <input type="date" name="tanggal_akad"
                  value="{{ old('tanggal_akad', \Carbon\Carbon::parse($data->tanggal_akad)->format('Y-m-d')) }}"
                  class="form-control @error('tanggal_akad') is-invalid @enderror" required>
                @error('tanggal_akad') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="form-group">
                <label for="wilayah_id">Kelurahan/Desa</label>
                <select name="wilayah_id" id="wilayah_id"
                  class="form-control @error('wilayah_id') is-invalid @enderror" required>
                  <option value="">-- Pilih Kelurahan/Desa --</option>
                  @foreach ($kelurahan as $item)
                    <option value="{{ $item->id }}" {{ old('wilayah_id', $data->wilayah_id) == $item->id ? 'selected' : '' }}>
                      {{ $item->desa }}
                    </option>
                  @endforeach
                </select>
                @error('wilayah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
  function toggleLainnya(selectId, inputId) {
    const select = document.getElementById(selectId);
    const input = document.getElementById(inputId);

    if (select.value === 'LAINNYA') {
      input.classList.remove('d-none');
    } else {
      input.classList.add('d-none');
      input.value = ''; // kosongkan jika tidak dipakai
    }
  }

  function hitungUsia(tanggalLahir) {
    const today = new Date();
    const birthDate = new Date(tanggalLahir);
    let usia = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      usia--;
    }
    return usia;
  }

  document.addEventListener('DOMContentLoaded', function () {
    const selectPekerjaanSuami = document.getElementById('pekerjaan_suami');
    const selectPekerjaanIstri = document.getElementById('pekerjaan_istri');

    toggleLainnya('pekerjaan_suami', 'pekerjaan_suami_lainnya');
    toggleLainnya('pekerjaan_istri', 'pekerjaan_istri_lainnya');

    selectPekerjaanSuami.addEventListener('change', () =>
      toggleLainnya('pekerjaan_suami', 'pekerjaan_suami_lainnya'));
    selectPekerjaanIstri.addEventListener('change', () =>
      toggleLainnya('pekerjaan_istri', 'pekerjaan_istri_lainnya'));

    // Auto hitung usia
    const tanggalLahirSuami = document.querySelector('input[name="tanggal_lahir_suami"]');
    const usiaSuami = document.querySelector('input[name="usia_suami"]');
    const tanggalLahirIstri = document.querySelector('input[name="tanggal_lahir_istri"]');
    const usiaIstri = document.querySelector('input[name="usia_istri"]');

    tanggalLahirSuami.addEventListener('change', function () {
      if (this.value) {
        usiaSuami.value = hitungUsia(this.value);
      }
    });

    tanggalLahirIstri.addEventListener('change', function () {
      if (this.value) {
        usiaIstri.value = hitungUsia(this.value);
      }
    });

    // Isi ulang usia saat pertama kali load
    if (tanggalLahirSuami.value) {
      usiaSuami.value = hitungUsia(tanggalLahirSuami.value);
    }
    if (tanggalLahirIstri.value) {
      usiaIstri.value = hitungUsia(tanggalLahirIstri.value);
    }
  });
</script>
@endpush

