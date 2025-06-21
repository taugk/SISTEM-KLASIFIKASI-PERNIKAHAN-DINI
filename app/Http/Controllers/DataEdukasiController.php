<?php

namespace App\Http\Controllers;

use App\Models\DataEdukasi;
use Illuminate\Http\Request;

class DataEdukasiController extends Controller
{
    public function index()
    {
        // Mengambil semua data edukasi dan memfilter berdasarkan judul atau kategori
        $data = DataEdukasi::all();

        // Filter data berdasarkan judul
        if (request('search')) {
            $data = DataEdukasi::where('judul', 'like', '%' . request('search') . '%')->get();
        }

        // Filter data berdasarkan kategori (langsung sebagai string)
        if (request('filter')) {
            $data = DataEdukasi::where('kategori', 'like', '%' . request('filter') . '%')->get();
        }

        return view('dashboard.data_edukasi.index', compact('data'));
    }

    public function tambahData()
    {
        // Ambil semua data edukasi
        $dataEdukasi = DataEdukasi::all();
        $kategoriList = [];

        // Loop untuk memisahkan dan mengambil kategori dari kolom kategori
        foreach ($dataEdukasi as $edukasi) {
            $kategoriNames = explode(',', $edukasi->kategori);  // Pisahkan kategori dengan koma
            foreach ($kategoriNames as $kategori) {
                $kategori = trim($kategori);  // Hapus spasi di awal/akhir
                if (!in_array($kategori, $kategoriList)) {
                    $kategoriList[] = $kategori;  // Tambahkan kategori ke dalam array
                }
            }
        }

        // Pass kategori ke view
        return view('dashboard.data_edukasi.tambah_data', compact('kategoriList'));
    }


    public function tambahDataPost(Request $request)
    {

        // Generate kode edukasi otomatis
        $last = DataEdukasi::latest('kd_edukasi')->first();
        $nextNumber = $last ? (int)substr($last->kd_edukasi, 3) + 1 : 1;
        $kode = 'EDU' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $penulis = auth()->guard('pengguna')->user()->id;

        $validatedData = $request->validate([
            'judul' => 'required|string',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kategori' => 'nullable|string' // kategori tetap ada
        ]);


        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $namaFile = str_replace(' ', '_', strtolower($validatedData['judul'])) . '.jpeg';
            $folderPath = storage_path('app/public/edukasi');

            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            $gambar->move($folderPath, $namaFile);
            $validatedData['gambar'] = $namaFile;
        } else {
            $validatedData['gambar'] = null;
        }

        // Proses kategori jika ada
        $kategoriNames = [];

        if ($request->filled('kategori')) {
            $kategoriNames = explode(',', $request->kategori); // Kategori dipisahkan koma

            // Jika Anda ingin memastikan tidak ada kategori kosong
            $kategoriNames = array_filter($kategoriNames, function ($value) {
                return !empty(trim($value));
            });
        }

        // Gabungkan kategori dengan koma dan simpan dalam kolom kategori
        $kategoriString = implode(', ', $kategoriNames);

        // Simpan data edukasi
        DataEdukasi::create([
            'kd_edukasi' => $kode,
            'judul' => $validatedData['judul'],
            'deskripsi' => $validatedData['deskripsi'],
            'gambar' => $validatedData['gambar'],
            'kategori' => $kategoriString,  // Simpan kategori dalam kolom kategori
            'pengguna_id' => $penulis
        ]);

        // Redirect ke halaman index dan beri pesan sukses
        return redirect()->route('data_edukasi.index')->with('success', 'Data edukasi berhasil ditambahkan.');
    }

    public function editData($id)
{
    $edukasi = DataEdukasi::findOrFail($id);

    // Daftar kategori statis
    $kategoriList = ['pernikahan dini', 'Gizi', 'Stunting'];

    // Pecah data kategori yang disimpan sebagai string ke array
    $kategoriListSelected = explode(', ', $edukasi->kategori);

    return view('dashboard.data_edukasi.edit_data', compact('edukasi', 'kategoriList', 'kategoriListSelected'));
}

public function updateData(Request $request, $id)
{
    $edukasi = DataEdukasi::findOrFail($id);

    $validatedData = $request->validate([
        'judul' => 'required|string',
        'deskripsi' => 'required|string',
        'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'kategori' => 'nullable|string' // kategori tetap ada
    ]);

    // Upload gambar jika ada
    if ($request->hasFile('gambar')) {
        $gambar = $request->file('gambar');
        $namaFile = str_replace(' ', '_', strtolower($validatedData['judul'])) . '.jpeg';
        $folderPath = storage_path('app/public/edukasi');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $gambar->move($folderPath, $namaFile);
        $validatedData['gambar'] = $namaFile;
    } else {
        $validatedData['gambar'] = $edukasi->gambar;
    }

    // Proses kategori jika ada
    $kategoriNames = [];

    if ($request->filled('kategori')) {
        $kategoriNames = explode(',', $request->kategori); // Kategori dipisahkan koma

        // Jika Anda ingin memastikan tidak ada kategori kosong
        $kategoriNames = array_filter($kategoriNames, function ($value) {
            return !empty(trim($value));
        });
    }

    // Gabungkan kategori dengan koma dan simpan dalam kolom kategori
    $kategoriString = implode(', ', $kategoriNames);

    // Simpan data edukasi
    $edukasi->update([
        'judul' => $validatedData['judul'],
        'deskripsi' => $validatedData['deskripsi'],
        'gambar' => $validatedData['gambar'],
        'kategori' => $kategoriString,  // Simpan kategori dalam kolom kategori
    ]);

    // Redirect ke halaman index dan beri pesan sukses
    return redirect()->route('data_edukasi.index')->with('success', 'Data edukasi berhasil diubah.');
}

public function detailData($id){
    $data = DataEdukasi::with('pengguna')->findOrFail($id);
    return view('dashboard.data_edukasi.detail_data', compact('data'));
}

public function deleteData($id){
    $data = DataEdukasi::findOrFail($id);
    $data->delete();
    return redirect()->route('data_edukasi.index')->with('success', 'Data edukasi berhasil dihapus.');
}
}
