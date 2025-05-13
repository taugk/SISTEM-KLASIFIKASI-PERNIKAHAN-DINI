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
            <div class="row">
              {{-- SUAMI --}}
              <div class="col-md-6 col-lg-4">
                <h5>Data Suami</h5>
                <div class="form-group">
                  <label>Nama Suami</label>
                  <input type="text" name="nama_suami" value="{{ $data->nama_suami }}" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Tanggal Lahir Suami</label>
                  <input type="date" name="tanggal_lahir_suami" value="{{ \Carbon\Carbon::parse($data->tanggal_lahir_suami)->format('Y-m-d') }}" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Usia Suami</label>
                  <input type="number" name="usia_suami" value="{{ $data->usia_suami }}" class="form-control" required>
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
                      <option value="{{ $p }}" {{ $data->pendidikan_suami == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>Pekerjaan Suami</label>
                  <input type="text" name="pekerjaan_suami" value="{{ $data->pekerjaan_suami }}" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Status Suami</label>
                  <select name="status_suami" class="form-control" required>
                    @foreach(['BELUM KAWIN', 'CERAI HIDUP', 'CERAI MATI'] as $status)
                      <option value="{{ $status }}" {{ $data->status_suami == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              {{-- ISTRI --}}
              <div class="col-md-6 col-lg-4">
                <h5>Data Istri</h5>
                <div class="form-group">
                  <label>Nama Istri</label>
                  <input type="text" name="nama_istri" value="{{ $data->nama_istri }}" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Tanggal Lahir Istri</label>
                  <input type="date" name="tanggal_lahir_istri" value="{{ \Carbon\Carbon::parse($data->tanggal_lahir_istri)->format('Y-m-d') }}" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Usia Istri</label>
                  <input type="number" name="usia_istri" value="{{ $data->usia_istri }}" class="form-control" required>
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
                      <option value="{{ $p }}" {{ $data->pendidikan_istri == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>Pekerjaan Istri</label>
                  <input type="text" name="pekerjaan_istri" value="{{ $data->pekerjaan_istri }}" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Status Istri</label>
                  <select name="status_istri" class="form-control" required>
                    @foreach(['BELUM KAWIN', 'CERAI HIDUP', 'CERAI MATI'] as $status)
                      <option value="{{ $status }}" {{ $data->status_istri == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              {{-- DATA PERNIKAHAN --}}
              <div class="col-md-6 col-lg-4">
                <h5>Data Pernikahan</h5>
                <div class="form-group">
                  <label>Tanggal Akad</label>
                  <input type="date" name="tanggal_akad" value="{{ \Carbon\Carbon::parse($data->tanggal_akad)->format('Y-m-d') }}" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Nama Kelurahan</label>
                  <input type="text" name="nama_kelurahan" value="{{ $data->wilayah->desa }}" class="form-control" required>
                </div>
                <div class="form-group mt-4">
                  <button type="submit" id="saveChangesBtn" class="btn btn-primary">Simpan Perubahan</button>
                  <a href="{{ route('data_pernikahan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
@endsection
