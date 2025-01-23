<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class mitransController extends Controller
{


    // return [
    //     'server_key' => env('MIDTRANS_SERVER_KEY'),
    //     'client_key' => env('MIDTRANS_CLIENT_KEY'),
    //     'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    // ];


    public function __construct()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$clientKey = config('midtrans.client_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
    }

    public function createQrisTransaction(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'order_id' => 'required|string',
            'gross_amount' => 'required|numeric',
        ]);

        // Transaction details
        $transactionDetails = [
            'order_id' => $request->order_id,
            'gross_amount' => $request->gross_amount,
        ];

        // item details
        $itemDetails = [
            [
                'id' => 'Photobox',
                'price' => $request->gross_amount,
                'quantity' => 1,
                'name' => 'Payment for order #' . $request->order_id,
            ],
        ];

        // customer_details
        $customerDetails = [
            'first_name' => 'Reza',
            'last_name' => 'Kurnia',
            'email' => 'rezakurnia@gmail.com',
            'phone' => '081898898989',
        ];

        // bank transfer
        $bankTransfer = [
            'bank_transfer' => 'bca',
        ];

        // qris
        $qris = [
            'acquirer' => 'gopay',
        ];

        // QRIS payment type payload
        $transaction = [
            'payment_type' => 'qris',
            // 'payment_type' => 'bank_transfer',
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
            // 'bank_transfer' => $bankTransfer,
            'qris' => $qris,
        ];

        try {
            // Create QRIS transaction
            $qrisResponse = \Midtrans\CoreApi::charge($transaction);

            return response()->json([
                'success' => true,
                'qris_response' => $qrisResponse,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getStatus($id)
    {
        try {
            // Get transaction status
            $statusResponse = \Midtrans\Transaction::status($id);

            return response()->json([
                'success' => true,
                'status_response' => $statusResponse,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
