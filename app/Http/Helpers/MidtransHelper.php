<?php

namespace App\Http\Helpers;

class MidtransHelper
{
    public static function createSnap($params)
    {
        \Midtrans\Config::$serverKey = config('app.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('app.midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('app.midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('app.midtrans.is_3ds');

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        return $snapToken;
    }
}
