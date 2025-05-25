@extends('layouts.app')

@section('content')
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Profil Saya</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Profil</li>
      </ul>
    </div>

    <div class="card mb-4">
      <div class="card-body row">
        <div class="col-md-4 text-center mb-4">
          @php $foto = session('pengguna.foto'); @endphp

          @if($foto && file_exists(public_path('storage/foto/' . $foto)))
            <img src="{{ asset('storage/foto/' . $foto) }}" class="img-fluid rounded" alt="Foto Pengguna" width="200">
          @else
            <svg class="avatar-img rounded w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
            </svg>
          @endif
        </div>

        <div class="col-md-8 mt-4">
          <p><strong>Nama:</strong> {{ session('pengguna.nama') }}</p>
          <p><strong>Username:</strong> {{ session('pengguna.username') }}</p>
          <p><strong>Jabatan:</strong> {{ ucfirst(session('pengguna.role')) }}</p>
          <p><strong>Alamat:</strong> {{ session('pengguna.alamat') }}</p>
        </div>

        <div class="col-md-12 mt-4 d-flex justify-content-between">
          <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Dashboard
          </a>
          <div>
            <a href="{{ route('edit', session('pengguna.id')) }}" class="btn btn-primary">
              <i class="fa fa-edit"></i> Edit Profil
            </a>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ubahPasswordModal">
              <i class="fa fa-lock"></i> Ubah Password
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ubah Password -->
<div class="modal fade" id="ubahPasswordModal" tabindex="-1" aria-labelledby="ubahPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-3">
      <div class="modal-header">
        <h5 class="modal-title" id="ubahPasswordModalLabel">Ubah Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>

      <form method="POST" action="{{ route('update_password') }}">
        @csrf
        <div class="modal-body">
          <!-- Password Lama -->
          <div class="mb-3">
            <label for="current_password" class="form-label">Password Lama</label>
            <div class="input-group">
              <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
              <span class="input-group-text toggle-password" data-target="current_password"><i class="fa fa-eye"></i></span>
              @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Password Baru -->
          <div class="mb-3">
            <label for="new_password" class="form-label">Password Baru</label>
            <div class="input-group">
              <input type="password" name="new_password" id="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
              <span class="input-group-text toggle-password" data-target="password"><i class="fa fa-eye"></i></span>
              @error('new_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Konfirmasi Password Baru -->
          <div class="mb-3">
            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
            <div class="input-group">
              <input type="password" name="new_password_confirmation" id="password_confirmation" class="form-control" required>
              <span class="input-group-text toggle-password" data-target="new_password_confirmation"><i class="fa fa-eye"></i></span>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

</div>
@endsection

@push('scripts')
<script>
  // Toggle Show/Hide Password
  $('.toggle-password').on('click', function () {
    const targetId = $(this).data('target');
    const input = $('#' + targetId);
    const icon = $(this).find('i');

    if (input.attr('type') === 'password') {
      input.attr('type', 'text');
      icon.removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
      input.attr('type', 'password');
      icon.removeClass('fa-eye').addClass('fa-eye-slash');
    }
  });

  // Tampilkan atau hapus ikon fa-eye berdasarkan input
  $('input[type="password"]').on('input', function () {
    const input = $(this);
    const iconWrapper = input.closest('.input-group').find('.toggle-password');
    const icon = iconWrapper.find('i');

    if (input.val().length > 0) {
      // Tambahkan ikon jika belum ada
      if (icon.length === 0) {
        iconWrapper.html('<i class="fa fa-eye-slash"></i>');
      }
    } else {
      // Hapus ikon jika kosong
      icon.remove();
    }
  });

  // Validasi konfirmasi password baru
  $('form').on('submit', function (e) {
    const pass = $('#new_password').val();
    const confirm = $('#new_password_confirmation').val();
    if (pass !== confirm) {
      e.preventDefault();
      alert('Konfirmasi password baru tidak cocok!');
    }
  });
</script>


@endpush
