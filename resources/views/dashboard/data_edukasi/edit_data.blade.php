@extends('layouts.app')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Edit Data Edukasi</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('data_edukasi.index') }}">Data Edukasi</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Edit Edukasi</a></li>
        </ul>
    </div>

    <div class="row d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Form Edit Data Edukasi</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('data_edukasi.updateData', ['id' => $edukasi->kd_edukasi]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label>Judul</label>
                            <input type="text" name="judul" class="form-control" value="{{ $edukasi->judul }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Deskripsi</label>
                            <div id="deskripsi">{!! $edukasi->deskripsi !!}</div>
                            <input type="hidden" name="deskripsi" id="deskripsiInput" value="{{ $edukasi->deskripsi }}">
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>Gambar (Kosongkan jika tidak ingin diubah)</label>
                            <input type="file" name="gambar" class="form-control">
                            @if($edukasi->gambar)
                                <img src="{{ asset('storage/edukasi/' . $edukasi->gambar) }}" class="mt-2" width="100">
                            @endif
                        </div>

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

                        <div class="form-group mb-3">
                            <label>Pilih Kategori</label>
                            <input id="kategori-select" name="kategori" class="form-control" />
                            <input type="hidden" name="kategori" id="kategoriInput">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success" id="saveChangesBtn">Update</button>
                            <a href="{{ route('data_edukasi.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('input[name=kategori]');
    const hiddenInput = document.getElementById('kategoriInput');
    const kategoriList = {!! json_encode($kategoriList) !!};
    const selectedKategori = {!! json_encode($kategoriListSelected) !!};

    const tagify = new Tagify(input, {
        whitelist: kategoriList,
        maxTags: 5,
        dropdown: {
            maxItems: 10,
            enabled: 1,
            closeOnSelect: true
        }
    });

    tagify.addTags(selectedKategori);

    function updateHiddenInput() {
        const selectedTags = tagify.value.map(tag => tag.value);
        hiddenInput.value = selectedTags.join(', ');
    }

    tagify.on('add', updateHiddenInput);
    tagify.on('remove', updateHiddenInput);

    updateHiddenInput(); // initial set

    document.querySelectorAll('.kategori-badge').forEach(badge => {
        badge.addEventListener('click', function () {
            const kategoriName = this.dataset.kategori;
            if (!tagify.value.some(tag => tag.value === kategoriName)) {
                tagify.addTags([kategoriName]);
            }
        });
    });
});
</script>


@endsection
