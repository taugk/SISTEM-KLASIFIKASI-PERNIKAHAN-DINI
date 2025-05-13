<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataWilayah;

class DataWilayahController extends Controller
{
    public function index(Request $request)
    {
        $query = DataWilayah::query();

        // Filtering
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('desa', 'like', '%' . $request->search . '%')
                    ->orWhere('kecamatan', 'like', '%' . $request->search . '%')
                    ->orWhere('kabupaten', 'like', '%' . $request->search . '%')
                    ->orWhere('provinsi', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filter_kecamatan) {
            $query->where('kecamatan', $request->filter_kecamatan);
        }

        if ($request->filter_kabupaten) {
            $query->where('kabupaten', $request->filter_kabupaten);
        }

        if ($request->filter_provinsi) {
            $query->where('provinsi', $request->filter_provinsi);
        }

        $data = $query->latest()->get();

        return view('dashboard.data_wilayah.index', [
            'data' => $data,
            'kecamatans' => DataWilayah::select('kecamatan')->distinct()->pluck('kecamatan'),
            'kabupatens' => DataWilayah::select('kabupaten')->distinct()->pluck('kabupaten'),
            'provinsis' => DataWilayah::select('provinsi')->distinct()->pluck('provinsi'),
        ]);
    }

    public function tambahDataPost(Request $request)
{
    $request->validate([
        'desa' => 'required|string',
        'kecamatan' => 'required|string',
        'kabupaten' => 'required|string',
        'provinsi' => 'required|string',
    ]);

    try {
        // Cek apakah data sudah ada
        $exists = DataWilayah::where('desa', $request->desa)
            ->where('kecamatan', $request->kecamatan)
            ->where('kabupaten', $request->kabupaten)
            ->where('provinsi', $request->provinsi)
            ->exists();

        if ($exists) {
            return redirect()->route('data_wilayah.index')->with('error', 'Data wilayah sudah ada.');
        }

        // Simpan data
        DataWilayah::create([
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'provinsi' => $request->provinsi,
        ]);

        return redirect()->route('data_wilayah.index')->with('success', 'Data berhasil ditambahkan.');
    } catch (\Exception $e) {
        // Tangani error
        return redirect()->route('data_wilayah.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}


    public function updateData(Request $request, $id)
    {
        $request->validate([
            'desa' => 'required|string',
            'kecamatan' => 'required|string',
            'kabupaten' => 'required|string',
            'provinsi' => 'required|string',
        ]);

        $data = DataWilayah::findOrFail($id);
        $data->update($request->only(['desa', 'kecamatan', 'kabupaten', 'provinsi']));

        return redirect()->route('dashboard.data_wilayah.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function deleteData($id)
    {
        $data = DataWilayah::findOrFail($id);
        $data->delete();

        return redirect()->route('dashboard.data_wilayah.index')->with('success', 'Data berhasil dihapus.');
    }

    public function tambahData()
    {
        return view('dashboard.data_wilayah.tambah_data');
    }
}
