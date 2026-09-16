<?php

namespace App\Http\Controllers;

use App\Models\GeneratedDocument;
use App\Services\GeniusPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function pay(GeneratedDocument $generatedDocument, GeniusPayService $geniuspay)
    {
        abort_unless($generatedDocument->status === 'generated', 404);

        if ($generatedDocument->is_paid) {
            return redirect()->route('generate.success', $generatedDocument);
        }

        $documentType = $generatedDocument->documentVersion->documentType;

        $result = $geniuspay->initierPaiement(
            montant: $documentType->price,
            description: 'Document : ' . $documentType->name,
            successUrl: route('generate.success', $generatedDocument),
            errorUrl: route('generate.success', $generatedDocument),
            metadata: ['generated_document_id' => $generatedDocument->id],
        );

        if (! $result || ! $result['checkout_url']) {
            return redirect()
                ->route('generate.success', $generatedDocument)
                ->with('error', 'Le paiement n\'a pas pu être initié. Réessayez.');
        }

        $generatedDocument->update([
            'geniuspay_reference' => $result['reference'],
        ]);

        return redirect($result['checkout_url']);
    }

    public function webhook(Request $request, GeniusPayService $geniuspay)
    {
        $signature = $request->header('X-Webhook-Signature', '');
        $timestamp = $request->header('X-Webhook-Timestamp', '');
        $rawPayload = $request->getContent();

        if (! $geniuspay->verifierSignatureWebhook($signature, $timestamp, $rawPayload)) {
            return response()->json(['error' => 'invalid signature'], 401);
        }

        $payload = json_decode($rawPayload, true);
        $reference = $payload['data']['transaction']['reference'] ?? null;
        $status = $payload['data']['transaction']['status'] ?? null;

        if (! $reference) {
            return response()->json(['error' => 'missing reference'], 422);
        }

        $generatedDocument = GeneratedDocument::where('geniuspay_reference', $reference)->first();

        if (! $generatedDocument) {
            Log::warning('GeniusPay webhook (doc-generator) : document introuvable', ['reference' => $reference]);
            return response()->json(['error' => 'not found'], 404);
        }

        if ($status === 'completed') {
            $generatedDocument->update([
                'is_paid' => true,
                'paid_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}