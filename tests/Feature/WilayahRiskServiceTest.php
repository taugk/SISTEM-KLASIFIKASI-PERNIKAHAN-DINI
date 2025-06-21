<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\DataWilayah;
use App\Models\DataPernikahan;
use App\Models\HasilKlasifikasi;
use App\Services\WilayahRiskService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WilayahRiskServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_risk_correctly_for_a_wilayah()
    {
        // Arrange: Buat data wilayah dan pernikahan
        $wilayah = DataWilayah::factory()->create();

        $pernikahan1 = DataPernikahan::factory()->create([
            'wilayah_id' => $wilayah->id,
            'tanggal_akad' => now(),
            'pendidikan_suami' => 3,
            'pendidikan_istri' => 4,
            'pekerjaan_suami' => 4,
            'pekerjaan_istri' => 5,
        ]);

        $pernikahan2 = DataPernikahan::factory()->create([
            'wilayah_id' => $wilayah->id,
            'tanggal_akad' => now(),
            'pendidikan_suami' => 2,
            'pendidikan_istri' => 2,
            'pekerjaan_suami' => 2,
            'pekerjaan_istri' => 3,
        ]);

        HasilKlasifikasi::factory()->create([
            'id_pernikahan' => $pernikahan1->id,
            'kategori_pernikahan' => 'Pernikahan Dini',
        ]);

        HasilKlasifikasi::factory()->create([
            'id_pernikahan' => $pernikahan2->id,
            'kategori_pernikahan' => 'Bukan Pernikahan Dini',
        ]);

        // Act
        $service = new WilayahRiskService();
        $result = $service->getKategoriResiko($wilayah->id, now()->format('Y'));

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('kategori', $result);
        $this->assertContains($result['kategori'], ['tinggi', 'sedang', 'rendah']);
    }
}
