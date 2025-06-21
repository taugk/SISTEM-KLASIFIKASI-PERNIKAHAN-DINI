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
        try {
            $request->validate([
                'desa' => 'required|string',
                'kecamatan' => 'required|string',
                'kabupaten' => 'required|string',
                'provinsi' => 'required|string'
            ]);

            // Convert input to uppercase
            $desa = strtoupper($request->desa);
            $kecamatan = strtoupper($request->kecamatan);
            $kabupaten = strtoupper($request->kabupaten);
            $provinsi = strtoupper($request->provinsi);

            // Cek apakah data sudah ada
            $exists = DataWilayah::where('desa', $desa)
                ->where('kecamatan', $kecamatan)
                ->where('kabupaten', $kabupaten)
                ->where('provinsi', $provinsi)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data wilayah sudah ada dalam database.'
                ], 400);
            }

            // Simpan data wilayah ke database
            DataWilayah::create([
                'desa' => $desa,
                'kecamatan' => $kecamatan,
                'kabupaten' => $kabupaten,
                'provinsi' => $provinsi,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data wilayah berhasil ditambahkan.'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {


            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
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

        // Convert input to uppercase
        $data = [
            'desa' => strtoupper($request->desa),
            'kecamatan' => strtoupper($request->kecamatan),
            'kabupaten' => strtoupper($request->kabupaten),
            'provinsi' => strtoupper($request->provinsi),
        ];

        $wilayah = DataWilayah::findOrFail($id);
        $wilayah->update($data);

        return redirect()->route('data_wilayah.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function deleteData($id)
    {
        $data = DataWilayah::findOrFail($id);
        $data->delete();

        return redirect()->route('data_wilayah.index')->with('success', 'Data berhasil dihapus.');
    }

    public function tambahData()
    {
        return view('dashboard.data_wilayah.tambah_data');
    }
}
