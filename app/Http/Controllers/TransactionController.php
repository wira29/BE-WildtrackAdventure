<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Http\Helpers\MidtransHelper;
use App\Http\Requests\StoreTransactionRequest;
use App\Mail\TransactionConfirmation;
use App\Mail\TransactionSuccess;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{

    public function __construct()
    {
        \Midtrans\Config::$serverKey = config('app.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('app.midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('app.midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('app.midtrans.is_3ds');
    }

    public function createTransaction(StoreTransactionRequest $request)
    {

        DB::beginTransaction();

        try {
            $customerDetails = $request->input('customerDetails');
            $tripDetails = $request->input('tripDetails');

            // Generate unique order ID
            $orderId = 'ORDER-' . time();

            // Prepare Midtrans parameter
            $parameter = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $request->trip_price,
                ],
                'credit_card' => [
                    'secure' => true
                ],
                'customer_details' => [
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'email' => $request->email,
                    'phone' => $request->phone
                ],
                'item_details' => [
                    [
                        'id' => $request->trip_id,
                        'price' => $request->trip_price,
                        'quantity' => 1,
                        'name' => $request->trip_name
                    ]
                ],
                "callbacks" => [
                    "finish" => "https://wildtrack-adventure.com/payment-success",
                ],
            ];

            // Create Snap transaction
            $snapToken = MidtransHelper::createSnap($parameter);

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            $imageUrl = Storage::disk('public')->putFileAs(
                'transactions',
                $file,
                $orderId . '.' . $extension
            );

            // Save transaction to database
            $transaction = Transaction::create([
                'order_id' => $orderId,
                'total' => $request->trip_price,
                'token' => $snapToken,
                'name' => $request->firstName . ' ' . $request->lastName,
                'email' => $request->email,
                'status' => 'pending',
                'redirect_url' => config('midtrans.is_production')
            ? "https://app.midtrans.com/snap/v2/vtweb/{$snapToken}"
            : "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}",
                'package_name' => $request->trip_name,
                'packgae_qty' => 1,
                'participants' => $request->camp_members,
                'payment_method' => '',
                'checkin' => Carbon::parse("2026-05-30 15:00:00"),
                'checkout' => Carbon::parse("2026-05-31 16:00:00"),
                'phone' => $request->phone,
                'image' => $imageUrl,
            ]);

            DB::commit();

            // Send email notification
            try {
                Mail::to($request->email)->send(
                    new TransactionConfirmation($transaction, $snapToken)
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send email: ' . $e->getMessage());
                // Don't fail the transaction if email fails
            }

            return response()->json([
                'success' => true,
                'token' => $snapToken,
                'order_id' => $orderId,
                'transaction' => $transaction,
            ]);

        } catch (\Exception $error) {
            DB::rollBack();

            \Log::error('Midtrans Error:', [
                'message' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function handleNotification(Request $request)
    {
        try {
            $notification = new \Midtrans\Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;

            \Log::info("Transaction notification received. Order ID: {$orderId}. Status: {$transactionStatus}");

            $transaction = Transaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Handle transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $transaction->update(['status' => 'success', 'payment_method' => $notification->payment_type]);
                }
            } elseif ($transactionStatus == 'settlement') {
                $transaction->update(['status' => 'success', 'payment_method' => $notification->payment_type]);
                try {
                    Mail::to($transaction->email)->send(
                        new TransactionSuccess($transaction, $notification)
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send email: ' . $e->getMessage());
                    // Don't fail the transaction if email fails
                }
            } elseif ($transactionStatus == 'pending') {
                $transaction->update(['status' => 'pending', 'payment_method' => $notification->payment_type]);
            } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                $transaction->update(['status' => 'failed']);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Notification Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAll(Request $request)
    {
        $transactions = Transaction::all();
        return response()->json([
            'data' => $transactions
        ]);
    }

    public function getTransactionByOrderId(Request $request, string $orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)->first();
        return response()->json([
            'data' => $transaction
        ]);
    }

    public function exportExcel(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|string',
            'search' => 'nullable|string',
        ]);

        $filename = 'laporan-transaksi-' . now()->format('Y-m-d-His') . '.xlsx';
        return Excel::download(new TransactionsExport($validated), $filename);
    }
}
