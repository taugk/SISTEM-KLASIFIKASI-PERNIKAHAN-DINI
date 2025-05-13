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
        <li class="nav-item"><a href="{{ route('data_pernikahan.tambahData') }}">Tambah Data</a></li>
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
                    <input type="text" name="nama_suami" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Tanggal Lahir Suami</label>
                    <input type="date" name="tanggal_lahir_suami" id="tanggal_lahir_suami" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Usia Suami</label>
                    <input type="number" id="usia_suami" name="usia_suami" class="form-control"  required>
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
                        <option value="{{ $p }}">{{ $p }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Pekerjaan Suami</label>
                    <input type="text" name="pekerjaan_suami" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Status Suami</label>
                    <select name="status_suami" class="form-control" required>
                      <option value="BELUM KAWIN">BELUM KAWIN</option>
                      <option value="CERAI HIDUP">CERAI HIDUP</option>
                      <option value="CERAI MATI">CERAI MATI</option>
                    </select>
                  </div>
                </div>

                {{-- ISTRI --}}
                <div class="col-md-6 col-lg-4">
                  <h5>Data Istri</h5>
                  <div class="form-group">
                    <label>Nama Istri</label>
                    <input type="text" name="nama_istri" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Tanggal Lahir Istri</label>
                    <input type="date" name="tanggal_lahir_istri" id="tanggal_lahir_istri" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Usia Istri</label>
                    <input type="number" id="usia_istri" name="usia_istri" class="form-control" required>
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
                        <option value="{{ $p }}">{{ $p }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Pekerjaan Istri</label>
                    <input type="text" name="pekerjaan_istri" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Status Istri</label>
                    <select name="status_istri" class="form-control" required>
                      <option value="BELUM KAWIN">BELUM KAWIN</option>
                      <option value="CERAI HIDUP">CERAI HIDUP</option>
                      <option value="CERAI MATI">CERAI MATI</option>
                    </select>
                  </div>
                </div>

                {{-- DATA PERNIKAHAN --}}
                <div class="col-md-6 col-lg-4">
                  <h5>Data Pernikahan</h5>
                  <div class="form-group">
                    <label>Tanggal Akad</label>
                    <input type="date" name="tanggal_akad" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label for="wilayah_id">Kelurahan/Desa</label>
                    <select name="wilayah_id" id="wilayah_id" class="form-control" required>
                        <option value="">-- Pilih Kelurahan/Desa --</option>
                        @foreach ($kelurahan as $item)
                            <option value="{{ $item->id }}">{{ $item->desa }}</option>
                        @endforeach
                    </select>
                </div>

                  <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('data_pernikahan.index') }}" class="btn btn-secondary">Batal</a>
                  </div>
                </div>

              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script>
        function hitungUmur(tanggal_lahir) {
            const tgl = new Date(tanggal_lahir);
            const now = new Date();
            let usia = now.getFullYear() - tgl.getFullYear();
            const m = now.getMonth() - tgl.getMonth();
            if (m < 0 || (m === 0 && now.getDate() < tgl.getDate())) {
                usia--;
            }
            return usia;
        }

        document.getElementById('tanggal_lahir_suami').addEventListener('change', function() {
            const usia = hitungUmur(this.value);
            document.getElementById('usia_suami').value = usia;
        });

        document.getElementById('tanggal_lahir_istri').addEventListener('change', function() {
            const usia = hitungUmur(this.value);
            document.getElementById('usia_istri').value = usia;
        });
    </script>
@endsection
