<?php

namespace App\Traits;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait PayTabsPayment
{
    /**
     * PayTabs API base URL
     */
    protected string $payTabsUrl = 'https://secure-egypt.paytabs.com';

    /**
     * Create a payment request through PayTabs
     *
     * @param float $amount
     * @param string $currency
     * @param string $description
     * @param string $callbackUrl
     * @param string $returnUrl
     * @return array
     */
    public function createPayTabsPayment(
        float $amount,
        int $transaction_id,
        string $currency = 'EGP',
        string $description = null,
        string $callbackUrl = null,
        string $returnUrl = null
    ): array {
        try {
            $response = Http::withHeaders([
                'authorization' => env('PAYTABS_SERVER_KEY'),
                'content-type' => 'application/json',
            ])->post($this->payTabsUrl . '/payment/request', [
                'profile_id' => env('PAYTABS_PROFILE_ID'),
                'tran_type' => 'sale',
                'tran_class' => 'ecom',
                'cart_id' => (string) Str::uuid(),
                'cart_description' => $description ?? 'Order ' . time(),
                'cart_currency' => $currency,
                'cart_amount' => $amount,
                'callback' => "http://localhost:8000/api/callback",
                'return' => env('APP_URL') . "/api/callback?id=" . $transaction_id,
            ]);
            $decodeResponse = json_decode($response, true);
            $transaction = Transaction::find($transaction_id);
            $transaction->ref = $decodeResponse['tran_ref'];
            $transaction->save();
            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status_code' => $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function callback(string $reference){
        try {
            $response = Http::withHeaders([
                'authorization' => env('PAYTABS_SERVER_KEY'),
                'content-type' => 'application/json',
            ])->post($this->payTabsUrl . '/payment/query', [
                'profile_id' => env('PAYTABS_PROFILE_ID'),
                'tran_ref' => $reference
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status_code' => $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        } 
    }
}