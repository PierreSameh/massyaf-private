<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Traits\PayTabsPayment;
use Illuminate\Http\Request;

class PayTabsController extends Controller
{
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
                // Payment is authorized
                return response()->json(['message' => 'Payment authorized successfully.'], 200);
            } else {
                // Payment failed or other status
                return response()->json(['message' => 'Payment not authorized.', 'status' => $responseStatus], 400);
            }
        }
    
        // If 'payment_result' or 'response_status' is missing
        return response()->json(['message' => 'Invalid callback response.'], 400);
    }
    
}
