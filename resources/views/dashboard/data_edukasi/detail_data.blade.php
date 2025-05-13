@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <article>
        <!-- Header Artikel -->
        <header class="mb-4">
          <h1 class="fw-bold text-dark mb-2">{{ $data->judul }}</h1>
          <div class="text-muted small mb-3">
            <i class="fa fa-folder-open"></i> <strong>Kategori:</strong> {{ $data->kategori }} &nbsp;|&nbsp;
            <i class="fa fa-user"></i> <strong>Penulis:</strong> {{ $data->pengguna->nama ?? 'Admin' }} &nbsp;|&nbsp;
            <i class="fa fa-calendar"></i> <strong>Diterbitkan:</strong> {{ \Carbon\Carbon::parse($data->created_at)->format('d M Y') }}
          </div>
        </header>

        <!-- Gambar Banner -->
        @if ($data->gambar)
          <div class="text-center mb-4">
            <img src="{{ asset('storage/edukasi/' . $data->gambar) }}" class="img-fluid rounded shadow-lg" alt="Gambar Edukasi">
          </div>
        @endif

        <!-- Deskripsi Materi -->
        <section class="content mb-5" style="line-height: 1.75; font-size: 1.1rem; color: #333;">
          {!! $data->deskripsi !!}
        </section>

        <!-- Social Media Share -->
        <div class="social-share mb-4">
          <h5 class="mb-2">Bagikan ke Sosial Media:</h5>
          <div class="d-flex justify-content-start">
            <!-- Share to Facebook -->
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-outline-primary me-2">
              <i class="fa fa-facebook-f"></i> Facebook
            </a>

            <!-- Share to Twitter -->
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($data->judul) }}" target="_blank" class="btn btn-outline-info me-2">
              <i class="fa fa-twitter"></i> Twitter
            </a>

            <!-- Share to WhatsApp -->
            <a href="https://api.whatsapp.com/send?text={{ urlencode($data->judul . ' ' . url()->current()) }}" target="_blank" class="btn btn-outline-success">
              <i class="fa fa-whatsapp"></i> WhatsApp
            </a>
          </div>
        </div>

        <!-- Footer dengan Tombol -->
        <footer class="d-flex justify-content-between">
          <a href="{{ route('data_edukasi.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> Kembali ke Daftar
          </a>
          @if(auth()->user()->role == 'admin, kepala_kua')
          <a href="{{ route('data_edukasi.editData', ['id' => $data->kd_edukasi]) }}" class="btn btn-primary">
            <i class="fa fa-edit"></i> Edit Materi
          </a>
          @endif
        </footer>

      </article>
    </div>
</div>
@endsection
