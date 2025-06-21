<?php

namespace App\Http\Controllers;



use App\Models\DataWilayah;
use Illuminate\Http\Request;
use App\Models\DataPernikahan;
use App\Models\Resiko_Wilayah;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HasilKlasifikasi;
use App\Http\Controllers\Controller;
use App\Services\KlasifikasiService;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataPernikahanExport;
use App\Imports\DataPernikahanImport;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;
use Symfony\Component\HttpKernel\DependencyInjection\LazyLoadingFragmentHandler;



class DataPernikahanController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query untuk data pernikahan dengan relasi wilayah
        $query = DataPernikahan::with(['wilayah:id,desa']); // load relasi wilayah

        // Ambil daftar tahun yang tersedia berdasarkan tanggal_akad
        $tahun = DataPernikahan::selectRaw('YEAR(tanggal_akad) as tahun')
                                ->distinct()
                                ->get();

        // Apply filter search jika ada
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama_suami', 'like', "%$searchTerm%")
                  ->orWhere('nama_istri', 'like', "%$searchTerm%")
                  ->orWhereHas('wilayah', function($w) use ($searchTerm) {
                      $w->where('desa', 'like', "%$searchTerm%");
                  });
            });
        }

        // Filter berdasarkan kelurahan jika ada
        if ($request->has('filter_kelurahan') && $request->filter_kelurahan) {
            $query->whereHas('wilayah', function ($q) use ($request) {
                $q->where('desa', $request->filter_kelurahan);
            });
        }

        // Filter berdasarkan tahun jika ada
        if ($request->has('filter_tahun') && $request->filter_tahun) {
            $query->whereYear('tanggal_akad', $request->filter_tahun);
        }

        // Ambil data pernikahan sesuai query
        $data = $query->get()->sortDesc();
        // Ambil daftar kelurahan untuk filter kelurahan
        $kelurahans = DataWilayah::select('desa')->get();

        return view('dashboard.data_pernikahan.index', compact('data', 'kelurahans', 'tahun'));
    }

    public function tambahData(){

        $kelurahan = DataWilayah::all();

        return view('dashboard.data_pernikahan.tambah_data', compact('kelurahan'));
    }

    public function tambahDataPost(Request $request, KlasifikasiService $klasifikasiService) {
    try {
        // Validasi input
        $validateData = $request->validate([
            'nama_suami' => 'required',
            'nama_istri' => 'required',
            'tanggal_lahir_suami' => 'required|date',
            'tanggal_lahir_istri' => 'required|date',
            'usia_suami' => 'required|integer|min:0',
            'usia_istri' => 'required|integer|min:0',
            'pendidikan_suami' => 'required',
            'pendidikan_istri' => 'required',
            'pekerjaan_suami' => 'required',
            'pekerjaan_istri' => 'required',
            'status_suami' => 'required',
            'status_istri' => 'required',
            'wilayah_id' => 'required|exists:data_wilayah,id',
            'tanggal_akad' => 'required|date',
        ]);


        $pernikahan = DataPernikahan::create($validateData);

        $klasifikasiService->prosesDanSimpan($validateData, $pernikahan->id);

        return redirect()->route('data_pernikahan.index')
                         ->with('success', 'Data Pernikahan Berhasil Ditambahkan.');
    } catch (\Exception $e) {
        return redirect()->route('data_pernikahan.index')
                         ->with('error', 'Terjadi kesalahan, coba lagi!'. $e->getMessage());
    }
}

    public function detail($id){
    $data = DataPernikahan::find($id);
    return view('dashboard.data_pernikahan.detail_pasangan', compact('data'));
}

public function edit($id){
    $data = DataPernikahan::find($id);
    $kelurahan = DataWilayah::all();
    return view('dashboard.data_pernikahan.edit_data', [
        'data' => $data,
        'kelurahan' => $kelurahan,
    ]);
}

public function update(Request $request, $id, KlasifikasiService $klasifikasiService)
{
    $validator = Validator::make($request->all(), [
        'nama_suami' => 'required',
        'nama_istri' => 'required',
        'tanggal_lahir_suami' => 'required|date',
        'tanggal_lahir_istri' => 'required|date',
        'usia_suami' => 'required|integer|min:15',
        'usia_istri' => 'required|integer|min:15',
        'pendidikan_suami' => 'required',
        'pendidikan_istri' => 'required',
        'pekerjaan_suami' => 'required',
        'pekerjaan_istri' => 'required',
        'status_suami' => 'required',
        'status_istri' => 'required',
        'wilayah_id' => 'required|exists:data_wilayah,id',
        'tanggal_akad' => 'required|date',
    ]);

    $validator->sometimes('pekerjaan_suami_lainnya', 'required|string|max:255', function ($input) {
        return $input->pekerjaan_suami === 'LAINNYA';
    });

    $validator->sometimes('pekerjaan_istri_lainnya', 'required|string|max:255', function ($input) {
        return $input->pekerjaan_istri === 'LAINNYA';
    });

    $validated = $validator->validate();


    // Validasi kelurahan (asumsi wilayah_id = desa)
    $kelurahan = DataWilayah::where('id', $validated['wilayah_id'])->first();
    if (!$kelurahan) {
        return back()->withErrors(['wilayah_id' => 'Kelurahan tidak ditemukan.'])->withInput();
    }




    // Jika kolom di tabel `data_pernikahan` masih `nama_kelurahan`, lakukan mapping
    // $validated['nama_kelurahan'] = $validated['wilayah_id'];
    // unset($validated['wilayah_id']);

    // Normalisasi pekerjaan
    if ($validated['pekerjaan_suami'] === 'LAINNYA') {
        $validated['pekerjaan_suami'] = $validated['pekerjaan_suami_lainnya'];
    }
    if ($validated['pekerjaan_istri'] === 'LAINNYA') {
        $validated['pekerjaan_istri'] = $validated['pekerjaan_istri_lainnya'];
    }

    $data = DataPernikahan::findOrFail($id);
    $data->update($validated);

    $klasifikasiService->prosesDanSimpan($validated, $id);

    return redirect()->route('data_pernikahan.index')
                     ->with('success', 'Data Pernikahan Berhasil Diubah');
}



public function delete($id){
    $data = DataPernikahan::find($id);
    $data->delete();
    return redirect()->route('data_pernikahan.index')
                     ->with('success', 'Data Pernikahan Berhasil Dihapus');
}

public function exportExcel(Request $request)
{
    $search = $request->search;
    $filter_kelurahan = $request->filter_kelurahan;
    $filter_tahun = $request->filter_tahun;

    // Bangun nama file berdasarkan filter yang tersedia
    $filename = 'data_pernikahan';

    if ($filter_tahun) {
        $filename .= '_tahun_' . $filter_tahun;
    }

    if ($filter_kelurahan) {
        $filename .= '_kelurahan_' . str_replace(' ', '_', strtolower($filter_kelurahan));
    }

    if ($search) {
        $filename .= '_' . str_replace(' ', '_', strtolower($search));
    }

    $filename .= '.xlsx';

    return Excel::download(
        new DataPernikahanExport($filter_tahun, $filter_kelurahan, $search),
        $filename
    );
}

public function exportCsv(Request $request)
{
    $search = $request->search;
    $filter_kelurahan = $request->filter_kelurahan;
    $filter_tahun = $request->filter_tahun;

    // Bangun nama file berdasarkan filter yang tersedia
    $filename = 'data_pernikahan';

    if ($filter_tahun) {
        $filename .= '_tahun_' . $filter_tahun;
    }

    if ($filter_kelurahan) {
        $filename .= '_kelurahan_' . str_replace(' ', '_', strtolower($filter_kelurahan));
    }

    if ($search) {
        $filename .= '_' . str_replace(' ', '_', strtolower($search));
    }

    $filename .= '.csv';

    return Excel::download(
        new DataPernikahanExport($filter_tahun, $filter_kelurahan, $search),
        $filename
    );
}


public function exportPdf(Request $request)
{
    $search = $request->search;
    $filter_kelurahan = $request->filter_kelurahan;
    $filter_tahun = $request->filter_tahun;

    $query = DataPernikahan::with('wilayah');

    if ($filter_tahun) {
        $query->whereYear('tanggal_akad', $filter_tahun);
    }

    if ($filter_kelurahan) {
        $query->whereHas('wilayah', function ($q) use ($filter_kelurahan) {
            $q->where('desa', $filter_kelurahan);
        });
    }

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('nama_suami', 'like', "%$search%")
              ->orWhere('nama_istri', 'like', "%$search%")
              ->orWhereHas('wilayah', function ($w) use ($search) {
                  $w->where('desa', 'like', "%$search%");
              });
        });
    }

    $data = $query->get();

    // Generate PDF
    $pdf = Pdf::loadView('pdf.data_pernikahan', compact('data'))
              ->setPaper('A4', 'landscape');

   //preview
   return $pdf->stream('data_pernikahan.pdf');
}

public function import(Request $request)
{
    // Validasi file yang diunggah
    $request->validate([
        'file' => 'required|mimes:csv,xlsx,xls',
    ]);

    try {
        // Proses import file menggunakan DataPernikahanImport
        Excel::import(new DataPernikahanImport, $request->file('file'));

        //tambah loading
        sleep(40);


        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('data_pernikahan.index')
                         ->with('success', 'Data Pernikahan Berhasil Diimport');
    } catch (\Exception $e) {
        // Tangani kesalahan dan kembalikan pesan error beserta detail kesalahan
        return redirect()->route('data_pernikahan.index')
                         ->with('error', 'Terjadi kesalahan, coba lagi! Error: ' . $e->getMessage());
    }
}




}
