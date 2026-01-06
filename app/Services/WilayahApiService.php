<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WilayahApiService
{
    protected string $baseUrl = 'https://wilayah.id/api';

    public function getProvinces(): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/provinces.json");

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('WilayahApiService Error (Provinces): '.$e->getMessage());
        }

        return [];
    }

    public function getRegencies(string $provinceCode): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/regencies/{$provinceCode}.json");

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error("WilayahApiService Error (Regencies for {$provinceCode}): ".$e->getMessage());
        }

        return [];
    }

    public function getDistricts(string $regencyCode): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/districts/{$regencyCode}.json");

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error("WilayahApiService Error (Districts for {$regencyCode}): ".$e->getMessage());
        }

        return [];
    }
}
