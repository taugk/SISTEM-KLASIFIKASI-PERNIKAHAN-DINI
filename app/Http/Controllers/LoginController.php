<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index(){
        return view('auth.login');
    }

    public function authenticate(Request $request)
{
    // Validasi kredensial
    $credentials = $request->validate([
        'username' => ['required', 'string'],
        'password' => ['required'],
    ]);

    // Menggunakan guard 'pengguna' untuk autentikasi
    if (Auth::guard('pengguna')->attempt($credentials)) {
        // Regenerasi session untuk menghindari session fixation
        $request->session()->regenerate();

        // Mendapatkan session ID dan menyimpannya ke dalam session
        $sessionid = $request->session()->getId();
        $request->session()->put('sessionid', $sessionid);

        // Mengambil data pengguna yang sudah terautentikasi
        $pengguna = Auth::guard('pengguna')->user();

        // Menyimpan data pengguna ke dalam session
        $request->session()->put('pengguna', $pengguna);

        // Menambahkan flash session message "success"
        $request->session()->flash('success', 'Login Berhasil! Selamat datang.');

        // Redirect berdasarkan role pengguna
        if ($pengguna->role == 'admin') {
            // Jika role pengguna adalah admin, arahkan ke halaman admin
            return redirect()->intended(route('dashboard.index', absolute: false)); // Pastikan route admin dashboard sudah ada
        } else {
            // Jika role bukan admin, arahkan ke halaman dashboard umum
            return redirect()->intended(route('dashboard.index', absolute: false)); // Sesuaikan dengan route dashboard biasa
        }
    }

    // Jika login gagal
    return back()->with('loginError', 'Username atau Password Salah!');
}



    public function logout(Request $request){
        Auth::guard('pengguna')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logout Berhasil!');
    }


    public function tambahPengguna(){
        return view('auth.register');
    }

    public function simpanPengguna(Request $request){
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);


        //cek username sudah ada atau belum
        $pengguna = Pengguna::where('username', $request->username)->first();
        if ($pengguna) {
            return back()->with('error', 'Username sudah terdaftar!');
        }

        $pengguna = new Pengguna();
        $pengguna->username = $request->username;
        $pengguna->password = Hash::make($request->password);
        $pengguna->save();
    }

    public function forgotPassword(){
        return view('auth.forgot-password');
    }

    public function resetPassword(Request $request){
        $request->validate([
            'username' => ['required'],
            'password_old' => ['required'],
            'password_new' => ['required'],
            'password_new_confirm' => ['required'],
        ]);

        $pengguna = Pengguna::where('username', $request->username)->first();
        if ($pengguna) {
            if (Hash::check($request->password_old, $pengguna->password)) {
                $pengguna->password = Hash::make($request->password_new);
                $pengguna->save();
                return back()->with('success', 'Password berhasil diubah!');
            } elseif ($request->password_new != $request->password_new_confirm) {
                return back()->with('error', 'Password baru tidak sama!');
            }elseif ($request->password_old == $request->password_new) {
                return back()->with('error', 'Password baru tidak boleh sama dengan password lama!');
            }elseif ($request->password_old == $request->password_new_confirm) {
                return back()->with('error', 'Password baru tidak boleh sama dengan password lama!');
            }else {
                return back()->with('error', 'Password lama salah!');
            }
        }

        return back()->with('error', 'Username tidak ditemukan!');
    }
}
