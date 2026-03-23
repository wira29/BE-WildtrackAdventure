<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageQuotaResource;
use App\Models\PackageQuota;
use Illuminate\Http\Request;

class PackageQuotaController extends Controller
{
    public function index()
    {
        $packageQuotas = PackageQuota::all();

        return response()->json([
            'message' => 'Package quotas retrieved successfully',
            'data' => PackageQuotaResource::collection($packageQuotas),
        ]);
    }

    public function update(Request $request, PackageQuota $packageQuota)
    {
        $request->validate([
            'max_quota' => 'required|integer|min:0',
        ]);

        $packageQuota->update($request->all());

        return response()->json([
            'message' => 'Package quota updated successfully',
            'data' => $packageQuota,
        ]);
    }
}
