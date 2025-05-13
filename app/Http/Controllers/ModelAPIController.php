<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ModelAPIController extends Controller
{
    // Fungsi utama untuk mengirim request ke FastAPI
    public function sendRequestToFastAPI(Request $request)
    {
        try {
            // Kirim permintaan POST ke FastAPI
            $response = Http::post('http://127.0.0.1:5000/predict', $request->all());

            // Periksa status respons dan kembalikan hasilnya
            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            // Jika ada error dalam permintaan API
            return response()->json(['error' => 'Request Failed', 'message' => $e->getMessage()], 500);
        }
    }

    // Fungsi untuk menangani respons API berdasarkan status
    private function handleApiResponse($response)
    {
        // Mengecek status code dan mengembalikan response sesuai
        if ($response->successful()) {
            // Status 200 atau 2xx
            return response()->json($response->json());
        }

        // Jika status code 400 (Bad Request)
        elseif ($response->status() == 400) {
            return response()->json([
                'error' => 'Bad Request',
                'message' => $response->json(),
            ], 400);
        }

        // Jika status code 500 (Internal Server Error)
        elseif ($response->status() == 500) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $response->json(),
            ], 500);
        }

        // Jika status code 404 (Not Found)
        elseif ($response->status() == 404) {
            return response()->json([
                'error' => 'Not Found',
                'message' => $response->json(),
            ], 404);
        }

        // Jika status code 401 (Unauthorized)
        elseif ($response->status() == 401) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => $response->json(),
            ], 401);
        }

        // Untuk status code lainnya (misalnya 403, 405, dll)
        else {
            return response()->json([
                'error' => 'Unexpected Error',
                'message' => $response->json(),
            ], $response->status());
        }
    }

    // Fungsi untuk memanggil prediksi langsung
    public function predict(Request $request)
    {
        try {
            // Mengirimkan request ke API FastAPI
            $response = Http::post('http://127.0.0.1:5000/predict', $request->all());

            // Mengecek status code dan mengembalikan response sesuai
            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            // Menangani kesalahan lain, seperti masalah koneksi ke API
            return response()->json([
                'error' => 'Request Failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
