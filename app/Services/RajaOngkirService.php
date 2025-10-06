<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected string $base;
    protected string $key;
    protected string $keyHeader;
    protected int $timeout;
    protected int $retries;
    protected int $backoff;

    public function __construct()
    {
        $cfg = config('rajaongkir');
        $this->base      = rtrim((string) $cfg['base'], '/');
        $this->key       = (string) $cfg['key'];
        $this->keyHeader = (string) ($cfg['auth'] ?: 'key');
        $this->timeout   = (int) ($cfg['timeout'] ?? 20);
        $this->retries   = (int) ($cfg['retries'] ?? 2);
        $this->backoff   = (int) ($cfg['backoff'] ?? 200);

        if ($this->base === '' || $this->key === '') {
            throw new \RuntimeException("Set RAJAONGKIR_BASE & RAJAONGKIR_KEY di .env");
        }
    }

    protected function client()
    {
        return Http::withHeaders([
                'Accept' => 'application/json',
                $this->keyHeader => $this->key,
            ])
            ->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
            ])
            ->retry($this->retries, $this->backoff)
            ->timeout($this->timeout);
    }

    protected function extract(Response $resp, ?string $path = null)
    {
        if ($resp->status() === 410) {
            throw new \RuntimeException('Endpoint nonaktif (410).');
        }
        $resp->throw();
        return $path ? data_get($resp->json(), $path) : $resp->json();
    }

    /** 
     * AUTOCOMPLETE tujuan (Direct Search Method)
     * Endpoint: GET /destination/domestic-destination
     */
    public function search(string $q): array
    {
        $url = $this->base . '/destination/domestic-destination';
        
        Log::info('Komerce Search Request', [
            'url' => $url,
            'search' => $q,
        ]);
        
        $resp = $this->client()->get($url, ['search' => $q]);
        
        Log::info('Komerce Search Response', [
            'status' => $resp->status(),
            'body' => $resp->body(),
        ]);
        
        $json = $this->extract($resp);
        
        // Response structure dari dokumentasi:
        // { "data": [ { "id": 123, "label": "Kuta, Badung, Bali" }, ... ] }
        $rows = (array) data_get($json, 'data', []);
        
        // Normalisasi output
        return array_values(array_filter(array_map(function ($r) {
            return [
                'id'    => $r['id'] ?? null,
                'label' => $r['label'] ?? null,
            ];
        }, $rows), fn($x) => $x['id'] && $x['label']));
    }

    /** 
     * HITUNG ONGKIR domestik
     * Endpoint: POST /calculate/domestic-cost
     */
    public function calculateDomestic(int $originId, int $destinationId, int $weightGram, string $courier): array
    {
        $url = $this->base . '/calculate/domestic-cost';
        
        $payload = [
            'origin'      => $originId,
            'destination' => $destinationId,
            'weight'      => max(1, $weightGram),
            'courier'     => Str::lower($courier),
        ];
        
        Log::info('Komerce Calculate Request', $payload);
        
        $resp = $this->client()->asForm()->post($url, $payload);
        
        Log::info('Komerce Calculate Response', [
            'status' => $resp->status(),
            'body' => $resp->body(),
        ]);
        
        return (array) $this->extract($resp, 'data');
    }
}