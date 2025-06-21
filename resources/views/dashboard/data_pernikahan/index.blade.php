@extends('layouts.app')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Data Pernikahan</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard.index') }}"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="{{ route('data_pernikahan.index') }}">Data Pernikahan</a></li>
                </ul>
            </div>

            <div class="card-header mt-10">
                <div class="card-header mt-4 px-0 border-0">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('data_pernikahan.tambahData') }}" class="btn btn-primary btn-round">
                                <i class="fa fa-plus"></i> Tambah Data
                            </a>

                            <button type="button" class="btn btn-success btn-round" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="fa fa-upload"></i> Upload Excel
                            </button>

                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-round dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-download"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('data_pernikahan.exportExcel', request()->only(['search', 'filter_kelurahan', 'filter_tahun'])) }}" class="dropdown-item">Export Excel (.xlsx)</a></li>
                                    <li><a href="{{ route('data_pernikahan.exportCsv', request()->only(['search', 'filter_kelurahan', 'filter_tahun'])) }}" class="dropdown-item">Export CSV (.csv)</a></li>
                                    <li><a href="{{ route('data_pernikahan.exportPdf', request()->only(['search', 'filter_kelurahan', 'filter_tahun'])) }}" class="dropdown-item">Export PDF</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <form method="GET" action="{{ route('data_pernikahan.index') }}" class="d-flex align-items-center gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request()->search }}">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                            </form>

                            <form method="GET" action="{{ route('data_pernikahan.index') }}" class="d-flex align-items-center gap-2">
                                <input type="hidden" name="search" value="{{ request()->search }}">
                                <select name="filter_kelurahan" class="form-control">
                                    <option value="">Filter by Kelurahan</option>
                                    @foreach ($kelurahans as $kelurahan)
                                        <option value="{{ $kelurahan->desa }}" {{ request()->filter_kelurahan == $kelurahan->desa ? 'selected' : '' }}>
                                            {{ $kelurahan->desa }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="filter_tahun" class="form-control">
                                    <option value="">Filter by Tahun</option>
                                    @foreach ($tahun as $th)
                                        <option value="{{ $th->tahun }}" {{ request()->filter_tahun == $th->tahun ? 'selected' : '' }}>
                                            {{ $th->tahun }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="display table table-striped table-hover" id="basic-datatables">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Suami</th>
                            <th>Usia Suami</th>
                            <th>Nama Istri</th>
                            <th>Usia Istri</th>
                            <th>Tanggal Akad</th>
                            <th>Nama Kelurahan</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $dt)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $dt->nama_suami }}</td>
                                <td>{{ $dt->usia_suami }}</td>
                                <td>{{ $dt->nama_istri }}</td>
                                <td>{{ $dt->usia_istri }}</td>
                                <td>{{ \Carbon\Carbon::parse($dt->tanggal_akad)->format('d-m-Y') ?? $dt->tanggal_akad }}</td>
                                <td>{{ $dt->wilayah->desa }}</td>
                                <td>
                                    <div class="form-button-action">
                                        <a href="{{ route('data_pernikahan.edit', $dt->id) }}" class="btn btn-link btn-primary btn-lg"><i class="fa fa-edit"></i></a>
                                        <a href="javascript:void(0)" data-url="{{ route('data_pernikahan.delete', $dt->id) }}" class="btn btn-link btn-danger btn-delete"><i class="fa fa-times"></i></a>
                                        <a href="{{ route('data_pernikahan.detail', $dt->id) }}" class="btn btn-link btn-info"><i class="fa fa-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <form id="form-delete" method="POST" style="display: none;">@csrf @method('DELETE')</form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for File Upload with Preview -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Excel File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" action="{{ route('data_pernikahan.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="file">Pilih File Excel</label>
                        <input type="file" name="file" id="excelFileInput" class="form-control" accept=".xlsx,.xls" required>
                    </div>

                    <!-- Preview -->
                    <div class="mt-4" id="excelPreviewContainer" style="display: none;">
                        <h6>Preview Data:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="excelPreviewTable">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<!-- SheetJS (XLSX Parser) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.getElementById('excelFileInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });

        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

        if (jsonData.length) {
            let thead = '';
            let tbody = '';

            // Header
            thead += '<tr>';
            jsonData[0].forEach(cell => {
                thead += `<th>${cell}</th>`;
            });
            thead += '</tr>';

            // Body (max 5 rows)
            jsonData.slice(1, 6).forEach(row => {
                tbody += '<tr>';
                row.forEach(cell => {
                    tbody += `<td>${cell ?? ''}</td>`;
                });
                tbody += '</tr>';
            });

            document.querySelector('#excelPreviewTable thead').innerHTML = thead;
            document.querySelector('#excelPreviewTable tbody').innerHTML = tbody;
            document.getElementById('excelPreviewContainer').style.display = 'block';
        }
    };
    reader.readAsArrayBuffer(file);
});
</script>
@endpush
