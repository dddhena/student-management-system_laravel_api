<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Student;
use App\Models\Fee;
use App\Models\Payment;
use Dagim\TelebirrApi\Telebirr;
use App\Services\TelebirrService;


class PaymentController extends Controller
{


public function initiate(Request $request, TelebirrService $telebirr)
{
    $payload = [
        'outTradeNo' => 'STU-' . $request->student_id,
        'subject' => 'Tuition Payment',
        'totalAmount' => $request->amount,
        'notifyUrl' => env('TELEBIRR_NOTIFY_URL'),
        'returnUrl' => 'https://yourapp.loca.lt/payment/return',
        'nonceStr' => uniqid(),
        'timestamp' => now()->timestamp,
    ];

    $finalPayload = $telebirr->buildPayload($payload);
    $response = $telebirr->sendToGateway($finalPayload);

    return response()->json([
        'paymentUrl' => $response['paymentUrl'] ?? null,
        'raw' => $response
    ]);
}



    public function callback(Request $request)
    {
        Log::info('Telebirr callback received:', $request->all());

        if ($request->status === 'SUCCESS') {
            Payment::create([
                'student_id' => $request->outTradeNo,
                'amount' => $request->totalAmount,
                'transaction_id' => $request->tradeNo,
                'status' => 'paid',
            ]);
        }

        return response()->json(['message' => 'Callback processed']);
    }

    
}
