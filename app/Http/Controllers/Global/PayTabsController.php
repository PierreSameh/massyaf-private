<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\TransactionCallback;
use App\Traits\PayTabsPayment;
use Illuminate\Http\Request;

class PayTabsController extends Controller
{
    protected $transactionService;
    public function __construct(TransactionCallback $transactionCallback)
    {
        $this->transactionService = $transactionCallback;
    }
    use PayTabsPayment;
    public function handleCallBack(Request $request)
    {
        $transaction = Transaction::find($request->id);
        // Get the callback response using the reference from the request
        $callback = $this->callback($transaction->ref);

        // Check if the response contains 'payment_result' and 'response_status'
        if (isset($callback['data']['payment_result']['response_status'])) {
            $responseStatus = $callback['data']['payment_result']['response_status'];
    
            // Process based on the response status
            if ($responseStatus === 'A') {
                $this->transactionService->completePayment($transaction->id);
                $transaction->payment_method = $callback['data']['payment_info']['card_scheme'];
                $transaction->save();
                // Payment is authorized
                return view('payment-success');
            } else {
                $transaction->status = "failed";
                $transaction->updated_at = now();
                $transaction->save();
                // Payment failed or other status
                return view('payment-failure');
            }
        }
    
        // If 'payment_result' or 'response_status' is missing
        return response()->json(['message' => 'Invalid callback response.'], 400);
    }
    
}
