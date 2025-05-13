<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class DataPenggunaController extends Controller
{
    public function index(){
        $query = Pengguna::query();

        // Filtering
        if (request('search')) {
            $query->where(function ($q) {
                $q->where('username', 'like', '%' . request('search') . '%')
                    ->orWhere('role', 'like', '%' . request('search') . '%')
                    ->orWhere('nama', 'like', '%' . request('search') . '%');
            });
        }

        if (request('filter_role')) {
            $query->where('role', request('filter_role'));
        }

    
        $data = $query->paginate();


        return view('dashboard.data_pengguna.index', compact('data'));
    }

    public function tambahData(){
        return view('dashboard.data_pengguna.tambah_data');
    }


    public function tambahDataPost(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'username' => 'required',
            'role' => 'required',
            'nama' => 'required',
            'password' => 'required',
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'alamat' => 'required',
        ]);
    
        // Meng-hash password 
        $validated['password'] = Hash::make($validated['password']);
    
        // Proses upload foto
        $foto = $request->file('foto');
        $imagePath = $foto->getPathname(); // path sementara
        $imageType = $foto->getClientMimeType(); // cek tipe mime
    
        // Tentukan nama file untuk foto
        $namaFile = str_replace(' ', '_', strtolower($validated['nama'])) . '.jpeg';
        $folderPath = storage_path('app/public/foto');
    
        // Pastikan folder ada, jika belum buat
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true); // buat folder
        }
    
        $outputPath = $folderPath . '/' . $namaFile;
    
        // Konversi gambar ke JPEG menggunakan GD
        switch ($imageType) {
            case 'image/png':
                $imageResource = imagecreatefrompng($imagePath);
                break;
            case 'image/webp':
                $imageResource = imagecreatefromwebp($imagePath);
                break;
            case 'image/jpeg':
            case 'image/jpg':
                $imageResource = imagecreatefromjpeg($imagePath);
                break;
            default:
                return back()->withErrors(['foto' => 'Format gambar tidak didukung.']);
        }
    
        // Simpan gambar sebagai JPEG
        imagejpeg($imageResource, $outputPath, 90);
        imagedestroy($imageResource); // bersihkan memori
    
        // Menyimpan nama file foto ke database
        $validated['foto'] = $namaFile;
    
        // Menyimpan data pengguna ke database
        Pengguna::create($validated);
    
        return redirect()->route('data_pengguna.index')
                         ->with('success', 'Data Pengguna Berhasil Ditambahkan');
    }

public function edit($id){
    $pengguna = Pengguna::find($id);
    return view('dashboard.data_pengguna.edit_data', compact('pengguna'));  
}

public function editDataPost(Request $request, $id)
{
    $validated = $request->validate([
        'nama' => 'required',
        'username' => 'required',
        'password' => 'nullable|min:6',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'role' => 'required',
        'alamat' => 'required',
    ]);

    $pengguna = Pengguna::findOrFail($id);

    // Update field dasar
    $pengguna->nama = $validated['nama'];
    $pengguna->username = $validated['username'];
    $pengguna->role = $validated['role'];
    $pengguna->alamat = $validated['alamat'];

    // Update password jika diisi
    if (!empty($validated['password'])) {
        $pengguna->password = Hash::make($validated['password']);
    }

    // Proses foto jika ada
    if ($request->hasFile('foto')) {
        $foto = $request->file('foto');
        $imagePath = $foto->getPathname(); // path sementara
        $imageType = $foto->getClientMimeType();

        $namaFile = str_replace(' ', '_', strtolower($validated['nama'])) . '.jpeg';
        $outputPath = storage_path('app/public/foto/' . $namaFile);

        // Konversi ke JPEG pakai GD
        switch ($imageType) {
            case 'image/png':
                $imageResource = imagecreatefrompng($imagePath);
                break;
            case 'image/webp':
                $imageResource = imagecreatefromwebp($imagePath);
                break;
            case 'image/jpeg':
            case 'image/jpg':
                $imageResource = imagecreatefromjpeg($imagePath);
                break;
            default:
                return back()->withErrors(['foto' => 'Format gambar tidak didukung.']);
        }

        // Hapus foto lama jika ada
        if ($pengguna->foto && Storage::exists('app/public/foto/' . $pengguna->foto)) {
            Storage::delete('app/public/foto/' . $pengguna->foto);
        }

        // Simpan sebagai JPEG
        imagejpeg($imageResource, $outputPath, 90);
        imagedestroy($imageResource);

        $pengguna->foto = $namaFile;


    $pengguna->save();

    return redirect()->route('data_pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
}
}


public function delete($id)
{
    $data = Pengguna::findOrFail($id);

    // Hapus file foto jika ada
    if ($data->foto && Storage::exists('app/public/foto/' . $data->foto)) {
        Storage::delete('app/public/foto/' . $data->foto);
    }

    // Hapus data pengguna
    $data->delete();

    return redirect()->route('data_pengguna.index')
                     ->with('success', 'Data Pengguna Berhasil Dihapus');
}

public function detail($id){
    $pengguna = Pengguna::find($id);
    return view('dashboard.data_pengguna.detail_pengguna', compact('pengguna'));
}

}
