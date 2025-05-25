@php
  $userRole = auth()->user()->role;

  $isDataPernikahan = request()->is('data_pernikahan*');
  $isDataEdukasi = request()->is('data_edukasi*');
  $isDataKlasifikasi = request()->is('data_klasifikasi*');
  $isDataPengguna = request()->is('data_pengguna*');
  $isDataWilayah = request()->is('data_wilayah*');
  $isLaporan = request()->is('laporan*');
  $isHasilKlasifikasi = request()->is('hasil_klasifikasi*');
@endphp

<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <div class="logo-header" data-background-color="light">
      <a href="/dashboard" class="logo">
        <img src="{{ asset('assets/Logo/logo.svg') }}" alt="navbar brand" class="navbar-brand" height="60" />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar"><i class="fas fa-bars"></i></button>
        <button class="btn btn-toggle sidenav-toggler"><i class="fas fa-bars"></i></button>
      </div>
      <button class="topbar-toggler more"><i class="fas fa-ellipsis-v"></i></button>
    </div>
  </div>

  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">

        {{-- Dashboard --}}
        <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
          <a href="/dashboard">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>

        {{-- Data Pernikahan --}}
        @if(in_array($userRole, ['admin']))
          <li class="nav-item {{ $isDataPernikahan ? 'active' : '' }}">
            <a data-bs-toggle="collapse" href="#kelolaDataPernikahan" aria-expanded="{{ $isDataPernikahan ? 'true' : 'false' }}">
              <i class="fas fa-heart"></i>
              <p>Kelola Data Pernikahan</p>
              <span class="caret"></span>
            </a>
            <div class="collapse {{ $isDataPernikahan ? 'show' : '' }}" id="kelolaDataPernikahan">
              <ul class="nav nav-collapse">
                <li class="{{ request()->is('data_pernikahan') ? 'active' : '' }}">
                  <a href="{{ route('data_pernikahan.index') }}">
                    <span class="sub-item">Data Pernikahan</span>
                  </a>
                </li>
                <li class="{{ request()->is('data_pernikahan/tambahData') ? 'active' : '' }}">
                  <a href="{{ route('data_pernikahan.tambahData') }}">
                    <span class="sub-item">Tambah Data</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- Data Edukasi --}}
        @if(in_array($userRole, ['admin', 'kepala kua', 'penyuluh']))
          <li class="nav-item {{ $isDataEdukasi ? 'active' : '' }}">
            <a data-bs-toggle="collapse" href="#kelolaDataEdukasi" aria-expanded="{{ $isDataEdukasi ? 'true' : 'false' }}">
              <i class="fas fa-book-open"></i>
              <p>Kelola Data Edukasi</p>
              <span class="caret"></span>
            </a>
            <div class="collapse {{ $isDataEdukasi ? 'show' : '' }}" id="kelolaDataEdukasi">
              <ul class="nav nav-collapse">
                <li class="{{ request()->is('data_edukasi') ? 'active' : '' }}">
                  <a href="{{ route('data_edukasi.index') }}">
                    <span class="sub-item">Edukasi Pernikahan</span>
                  </a>
                </li>
                @if(in_array($userRole, ['admin', 'kepala kua']))
                  <li class="{{ request()->is('data_edukasi/tambahData') ? 'active' : '' }}">
                    <a href="{{ route('data_edukasi.tambahData') }}">
                      <span class="sub-item">Tambah Data</span>
                    </a>
                  </li>
                @endif
              </ul>
            </div>
          </li>
        @endif

        {{-- Data Klasifikasi --}}
        @if($userRole === 'admin')
          <li class="nav-item {{ $isDataKlasifikasi ? 'active' : '' }}">
            <a data-bs-toggle="collapse" href="#kelolaDataKlasifikasi" aria-expanded="{{ $isDataKlasifikasi ? 'true' : 'false' }}">
              <i class="fas fa-stream"></i>
              <p>Kelola Data Klasifikasi</p>
              <span class="caret"></span>
            </a>
            <div class="collapse {{ $isDataKlasifikasi ? 'show' : '' }}" id="kelolaDataKlasifikasi">
              <ul class="nav nav-collapse">
                <li class="{{ request()->is('data_klasifikasi') ? 'active' : '' }}">
                  <a href="{{ route('data_klasifikasi.index') }}">
                    <span class="sub-item">Data Klasifikasi</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>

          {{-- Data Pengguna --}}
          <li class="nav-item {{ $isDataPengguna ? 'active' : '' }}">
            <a data-bs-toggle="collapse" href="#kelolaDataPengguna" aria-expanded="{{ $isDataPengguna ? 'true' : 'false' }}">
              <i class="fas fa-user-cog"></i>
              <p>Kelola Data Pengguna</p>
              <span class="caret"></span>
            </a>
            <div class="collapse {{ $isDataPengguna ? 'show' : '' }}" id="kelolaDataPengguna">
              <ul class="nav nav-collapse">
                <li class="{{ request()->is('data_pengguna') ? 'active' : '' }}">
                  <a href="{{ route('data_pengguna.index') }}">
                    <span class="sub-item">Data Pengguna</span>
                  </a>
                </li>
                <li class="{{ request()->is('data_pengguna/tambahData') ? 'active' : '' }}">
                  <a href="{{ route('data_pengguna.tambahData') }}">
                    <span class="sub-item">Tambah Data Pengguna</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>

          {{-- Data Wilayah --}}
          <li class="nav-item {{ $isDataWilayah ? 'active' : '' }}">
            <a href="{{ route('data_wilayah.index') }}">
              <i class="fas fa-map"></i>
              <p>Data Wilayah</p>
            </a>
          </li>
        @endif

        {{-- Laporan --}}
        @if(in_array($userRole, ['admin', 'kepala kua']))
          <li class="nav-item {{ $isLaporan ? 'active' : '' }}">
            <a data-bs-toggle="collapse" href="#laporanMenu" aria-expanded="{{ $isLaporan ? 'true' : 'false' }}">
              <i class="fas fa-file-alt"></i>
              <p>Laporan</p>
              <span class="caret"></span>
            </a>
            <div class="collapse {{ $isLaporan ? 'show' : '' }}" id="laporanMenu">
              <ul class="nav nav-collapse">
                <li class="{{ request()->is('laporan*') ? 'active' : '' }}">
                  <a href="{{ route('laporan.statistik') }}">
                    <span class="sub-item">Statistik Umum</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- Hasil Klasifikasi --}}
        <li class="nav-item {{ $isHasilKlasifikasi ? 'active' : '' }}">
          <a data-bs-toggle="collapse" href="#hasilKlasifikasi" aria-expanded="{{ $isHasilKlasifikasi ? 'true' : 'false' }}">
            <i class="fas fa-project-diagram"></i>
            <p>Hasil Klasifikasi</p>
            <span class="caret"></span>
          </a>
          <div class="collapse {{ $isHasilKlasifikasi ? 'show' : '' }}" id="hasilKlasifikasi">
            <ul class="nav nav-collapse">
              <li class="{{ request()->is('hasil_klasifikasi') || request()->is('hasil_klasifikasi/index') ? 'active' : '' }}">
                <a href="{{ route('hasil_klasifikasi.index') }}">
                  <span class="sub-item">Hasil Klasifikasi</span>
                </a>
              </li>
              <li class="{{ request()->is('hasil_klasifikasi/graphView*') ? 'active' : '' }}">
                <a href="{{ route('hasil_klasifikasi.graphView') }}">
                  <span class="sub-item">Grafik</span>
                </a>
              </li>
              <li class="{{ request()->is('hasil_klasifikasi/chart*') ? 'active' : '' }}">
                <a href="{{ route('hasil_klasifikasi.chart') }}">
                  <span class="sub-item">Chart</span>
                </a>
              </li>
              <li class="{{ request()->is('hasil_klasifikasi/peta_sebaran*') ? 'active' : '' }}">
                <a href="{{ route('hasil_klasifikasi.peta_sebaran') }}">
                  <span class="sub-item">Peta Sebaran</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

      </ul>
    </div>
  </div>
</div>
