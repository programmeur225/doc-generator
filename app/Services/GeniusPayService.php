<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeniusPayService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $webhookSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.geniuspay.base_url');
        $this->apiKey = config('services.geniuspay.api_key');
        $this->apiSecret = config('services.geniuspay.api_secret');
        $this->webhookSecret = config('services.geniuspay.webhook_secret');
    }

    /**
     * Initie un paiement pour un document (montant en XOF).
     * Retourne ['reference' => ..., 'checkout_url' => ...] ou null en cas d'échec.
     */
    public function initierPaiement(int $montant, string $description, string $successUrl, string $errorUrl, array $metadata = []): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-API-Secret' => $this->apiSecret,
            ])->post("{$this->baseUrl}/payments", [
                'amount' => $montant,
                'description' => $description,
                'success_url' => $successUrl,
                'error_url' => $errorUrl,
                'metadata' => $metadata,
            ]);

            if (! $response->successful()) {
                Log::error('GeniusPay initierPaiement échec HTTP', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json('data');

            return [
                'reference' => $data['reference'] ?? null,
                'checkout_url' => $data['checkout_url'] ?? $data['payment_url'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('GeniusPay initierPaiement exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Récupère les détails d'une transaction via son reference (fallback / réconciliation).
     */
    public function getTransaction(string $reference): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-API-Secret' => $this->apiSecret,
            ])->get("{$this->baseUrl}/payments/{$reference}");

            return $response->successful() ? $response->json('data') : null;
        } catch (\Throwable $e) {
            Log::error('GeniusPay getTransaction exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Vérifie la signature HMAC d'un webhook entrant.
     */
    public function verifierSignatureWebhook(string $signature, string $timestamp, string $rawPayload): bool
    {
        $expected = hash_hmac('sha256', $timestamp . '.' . $rawPayload, $this->webhookSecret);

        if (! hash_equals($expected, $signature)) {
            Log::warning('GeniusPay webhook signature invalide (doc-generator)');
            return false;
        }

        return true;
    }
}