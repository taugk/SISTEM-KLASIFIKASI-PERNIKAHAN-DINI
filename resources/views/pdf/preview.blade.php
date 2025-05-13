@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Preview Data Pernikahan</h4>

    <form action="{{ route('data_pernikahan.exportPdf') }}" method="GET" target="_blank" class="mb-3">
        <input type="hidden" name="search" value="{{ $search }}">
        <input type="hidden" name="filter_kelurahan" value="{{ $filter_kelurahan }}">
        <input type="hidden" name="filter_tahun" value="{{ $filter_tahun }}">
        <button type="submit" class="btn btn-primary">Unduh / Cetak PDF</button>
    </form>

    <iframe
        src="{{ route('data_pernikahan.exportPdf', [
            'search' => $search,
            'filter_kelurahan' => $filter_kelurahan,
            'filter_tahun' => $filter_tahun
        ]) }}"
        width="100%"
        height="800px"
        style="border: 1px solid #ccc;">
    </iframe>
</div>
@endsection
