<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WilayahController extends Controller
{
    private $baseUrl = 'https://api.binderbyte.com/wilayah';
    private $apiKey = 'b6ff0c9799def46d3a3f5adc3d4fb1ae25605e2fb83b8f8ed6be1aab945764c0';

    public function provinsi()
    {
        try {
            $response = Http::get($this->baseUrl . '/provinsi', [
                'api_key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['value']) && is_array($data['value'])) {
                    // Transform the response to match the expected format
                    $transformedData = array_map(function($item) {
                        return [
                            'id' => $item['id'],
                            'nama' => $item['name']  // API returns 'name', we convert it to 'nama'
                        ];
                    }, $data['value']);
                    
                    return response()->json($transformedData);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Format response tidak sesuai'
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data provinsi: ' . ($response->json()['messages'] ?? 'Unknown error')
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error di API Provinsi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function kabupaten(Request $request)
    {
        try {
            $provinsiId = $request->provinsi_id;
            if (!$provinsiId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID provinsi diperlukan'
                ], 400);
            }

            $response = Http::get($this->baseUrl . '/kabupaten', [
                'api_key' => $this->apiKey,
                'id_provinsi' => $provinsiId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['value']) && is_array($data['value'])) {
                    // Transform the response to match the expected format
                    $transformedData = array_map(function($item) {
                        return [
                            'id' => $item['id'],
                            'nama' => $item['name']  // API returns 'name', we convert it to 'nama'
                        ];
                    }, $data['value']);
                    
                    return response()->json($transformedData);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Format response tidak sesuai'
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kabupaten: ' . ($response->json()['messages'] ?? 'Unknown error')
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error di API Kabupaten: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function kecamatan(Request $request)
    {
        try {
            $kabupatenId = $request->kabupaten_id;
            if (!$kabupatenId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID kabupaten diperlukan'
                ], 400);
            }

            $response = Http::get($this->baseUrl . '/kecamatan', [
                'api_key' => $this->apiKey,
                'id_kabupaten' => $kabupatenId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['value']) && is_array($data['value'])) {
                    // Transform the response to match the expected format
                    $transformedData = array_map(function($item) {
                        return [
                            'id' => $item['id'],
                            'nama' => $item['name']  // API returns 'name', we convert it to 'nama'
                        ];
                    }, $data['value']);
                    
                    return response()->json($transformedData);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Format response tidak sesuai'
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kecamatan: ' . ($response->json()['messages'] ?? 'Unknown error')
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error di API Kecamatan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function desa(Request $request)
    {
        try {
            $kecamatanId = $request->kecamatan_id;
            if (!$kecamatanId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID kecamatan diperlukan'
                ], 400);
            }

            $response = Http::get($this->baseUrl . '/kelurahan', [
                'api_key' => $this->apiKey,
                'id_kecamatan' => $kecamatanId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['value']) && is_array($data['value'])) {
                    // Transform the response to match the expected format
                    $transformedData = array_map(function($item) {
                        return [
                            'id' => $item['id'],
                            'nama' => $item['name']  // API returns 'name', we convert it to 'nama'
                        ];
                    }, $data['value']);
                    
                    return response()->json($transformedData);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Format response tidak sesuai'
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data desa: ' . ($response->json()['messages'] ?? 'Unknown error')
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error di API Desa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 