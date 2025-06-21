<?php

namespace App\Http\Controllers;

use App\Models\DataWilayah;
use Illuminate\Http\Request;
use App\Models\DataPernikahan;
use App\Models\Resiko_Wilayah;
use Illuminate\Support\Collection;

class clusteriController extends Controller
{
    // Fungsi untuk label encoding otomatis + simpan detail mapping
    private function encodeKategoriWithMapping($data, $key, &$mappingCollection)
    {
        $kategori_unik = collect($data)->pluck($key)->unique()->values();
        $mapping = $kategori_unik->flip();

        // Simpan ke collection untuk ditampilkan di view jika perlu
        $mappingCollection[$key] = $mapping->flip(); // value: kode, key: label

        return collect($data)->map(function ($item) use ($mapping, $key) {
            $item[$key . '_encoded'] = $mapping[$item[$key]] ?? 0;
            return $item;
        })->values()->toArray();
    }

    private function clustering($grouped_data, $k = 3)
    {
        $data = array_map(function ($item) {
            return [
                (float)$item['rata_rata_usia_suami'],
                (float)$item['rata_rata_usia_istri'],
                (float)$item['rata_rata_pendidikan_suami'],
                (float)$item['rata_rata_pendidikan_istri'],
                (float)$item['rata_rata_pekerjaan_suami'],
                (float)$item['rata_rata_pekerjaan_istri'],
                (float)$item['rata_rata_status_suami'],
                (float)$item['rata_rata_status_istri'],
                (float)$item['jumlah_pernikahan_dini']
            ];
        }, $grouped_data);

        $n = count($data);
        if ($n < $k) return [];

        $centroids = array_slice($data, 0, $k);
        $max_iter = 100;
        $assignments = array_fill(0, $n, 0);

        for ($iter = 0; $iter < $max_iter; $iter++) {
            foreach ($data as $i => $point) {
                $min_dist = INF;
                $min_index = 0;
                foreach ($centroids as $j => $centroid) {
                    $dist = 0;
                    foreach ($point as $d => $val) {
                        $dist += pow($val - $centroid[$d], 2);
                    }
                    if ($dist < $min_dist) {
                        $min_dist = $dist;
                        $min_index = $j;
                    }
                }
                $assignments[$i] = $min_index;
            }

            $new_centroids = array_fill(0, $k, array_fill(0, count($data[0]), 0));
            $counts = array_fill(0, $k, 0);

            foreach ($data as $i => $point) {
                $cluster = $assignments[$i];
                $counts[$cluster]++;
                foreach ($point as $d => $val) {
                    $new_centroids[$cluster][$d] += $val;
                }
            }

            for ($j = 0; $j < $k; $j++) {
                if ($counts[$j] > 0) {
                    foreach ($new_centroids[$j] as $d => $val) {
                        $new_centroids[$j][$d] = $val / $counts[$j];
                    }
                } else {
                    $new_centroids[$j] = $data[array_rand($data)];
                }
            }

            if ($centroids === $new_centroids) break;
            $centroids = $new_centroids;
        }

        $cluster_avg_dini = [];
        for ($i = 0; $i < $k; $i++) $cluster_avg_dini[$i] = ['total' => 0, 'count' => 0];

        foreach ($assignments as $i => $cluster) {
            $cluster_avg_dini[$cluster]['total'] += $grouped_data[$i]['jumlah_pernikahan_dini'];
            $cluster_avg_dini[$cluster]['count']++;
        }

        foreach ($cluster_avg_dini as $i => $c) {
            $cluster_avg_dini[$i]['avg'] = $c['count'] > 0 ? $c['total'] / $c['count'] : 0;
        }

        uasort($cluster_avg_dini, function ($a, $b) {
            return $a['avg'] <=> $b['avg'];
        });

        $cluster_names = ['Rendah', 'Sedang', 'Tinggi'];
        $cluster_map = [];
        $i = 0;
        foreach ($cluster_avg_dini as $cluster_idx => $info) {
            $cluster_map[$cluster_idx] = $cluster_names[$i++];
        }

        $result = [];
        foreach ($assignments as $i => $cluster) {
            $result[] = array_merge($grouped_data[$i], [
                'cluster_id' => $cluster,
                'cluster_label' => $cluster_map[$cluster]
            ]);
        }

        // simpan hasil clustering ke resiko_wilayah

        return $result;
    }

    public function index()
    {
        $data_pernikahan = DataPernikahan::with('hasilKlasifikasi', 'wilayah')
            ->orderBy('id', 'desc')
            ->get();

        $data_pernikahan_dini = $data_pernikahan->filter(function ($item) {
            return $item->hasilKlasifikasi && $item->hasilKlasifikasi->kategori_pernikahan === 'Pernikahan Dini';
        });

        $data_array = $data_pernikahan_dini->toArray();

        // Mapping detail untuk view
        $mappings = [];
        $data_array = $this->encodeKategoriWithMapping($data_array, 'pendidikan_suami', $mappings);
        $data_array = $this->encodeKategoriWithMapping($data_array, 'pendidikan_istri', $mappings);
        $data_array = $this->encodeKategoriWithMapping($data_array, 'pekerjaan_suami', $mappings);
        $data_array = $this->encodeKategoriWithMapping($data_array, 'pekerjaan_istri', $mappings);
        $data_array = $this->encodeKategoriWithMapping($data_array, 'status_suami', $mappings);
        $data_array = $this->encodeKategoriWithMapping($data_array, 'status_istri', $mappings);

        $grouped = collect($data_array)->groupBy(fn($item) => $item['wilayah']['desa'] ?? 'Tidak Diketahui')
            ->map(function ($items, $wilayah) {
                return [
                    'wilayah' => $wilayah,
                    'jumlah_pernikahan_dini' => count($items),
                    'rata_rata_usia_suami' => collect($items)->avg('usia_suami') ?? 0,
                    'rata_rata_usia_istri' => collect($items)->avg('usia_istri') ?? 0,
                    'rata_rata_pendidikan_suami' => collect($items)->avg('pendidikan_suami_encoded') ?? 0,
                    'rata_rata_pendidikan_istri' => collect($items)->avg('pendidikan_istri_encoded') ?? 0,
                    'rata_rata_pekerjaan_suami' => collect($items)->avg('pekerjaan_suami_encoded') ?? 0,
                    'rata_rata_pekerjaan_istri' => collect($items)->avg('pekerjaan_istri_encoded') ?? 0,
                    'rata_rata_status_suami' => collect($items)->avg('status_suami_encoded') ?? 0,
                    'rata_rata_status_istri' => collect($items)->avg('status_istri_encoded') ?? 0,
                ];
            })->values()->toArray();

        $hasil_clustering = $this->clustering($grouped);

        foreach ($hasil_clustering as $item) {
    $wilayah = DataWilayah::where('desa', $item['wilayah'])->first();
    if ($wilayah) {
        Resiko_Wilayah::updateOrCreate(
            ['id_wilayah' => $wilayah->id],
            ['kategori_wilayah' => $item['cluster_label']]
        );
    }
}
        return view('dashboard.cluster.index', [
            'hasil_clustering' => $hasil_clustering,
            'mappings' => $mappings
        ]);
    }
}
