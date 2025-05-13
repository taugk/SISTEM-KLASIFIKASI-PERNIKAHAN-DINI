@extends('layouts.app')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Tambah Data Edukasi</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('data_edukasi.index') }}">Data Edukasi</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('data_edukasi.tambahData') }}">Tambah Edukasi</a></li>
        </ul>
    </div>

    <div class="row d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Form Tambah Data Edukasi</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('data_edukasi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label>Judul</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Deskripsi</label>
                            <div id="deskripsi"></div>
                            <input type="hidden" name="deskripsi" id="deskripsiInput" required>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>Gambar</label>
                            <input type="file" name="gambar" class="form-control">
                        </div>

                        <!-- Daftar Kategori -->
                        <div class="form-group mb-4">
                            <label>Daftar Kategori</label>
                            <div id="kategoriList" class="mt-2">
                                @foreach ($kategoriList as $kategori)
                                    <span class="badge rounded-pill bg-primary text-white me-2 mb-2 kategori-badge"
                                          data-kategori="{{ $kategori }}"
                                          role="button">
                                        {{ $kategori }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Pilih Kategori -->
                        <div class="form-group mb-3">
                            <label>Pilih Kategori</label>
                            <input id="kategori-select" name="kategori" placeholder="Pilih kategori" class="form-control" />
                            <input type="hidden" name="kategori" id="kategoriInput">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('data_edukasi.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


<!-- Tagify CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('input[name=kategori]');
    const hiddenInput = document.getElementById('kategoriInput');

    // List of categories from the controller
    const kategoriList = {!! json_encode($kategoriList) !!}; // This will be a plain array in JavaScript

    // Initialize Tagify with the list of categories
    const tagify = new Tagify(input, {
        whitelist: kategoriList,  // Passing the categories list directly
        maxTags: 5,
        dropdown: {
            maxItems: 10,
            enabled: 1,
            closeOnSelect: true
        }
    });

    // Function to update hidden input with selected categories
    function updateHiddenInput() {
        const selectedTags = tagify.value.map(tag => tag.value);
        hiddenInput.value = selectedTags.join(', '); // Join selected tags as a comma-separated string
    }

    tagify.on('add', updateHiddenInput);
    tagify.on('remove', updateHiddenInput);

    // Handle category badge clicks
    document.querySelectorAll('.kategori-badge').forEach(badge => {
        badge.addEventListener('click', function () {
            const kategoriName = this.dataset.kategori; // Get category name from data attribute

            // Add the category name to Tagify input if not already present
            if (!tagify.value.some(tag => tag.value === kategoriName)) {
                tagify.addTags([kategoriName]); // Add the category to Tagify input
            }
        });
    });
});
</script>

@endsection
