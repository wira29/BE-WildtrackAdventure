<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageQuotaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'package_name' => $this->package_name,
            'used' => Transaction::where(['package_name' => $this->package_name, 'status' => 'success'])->count(),
            'max_quota' => $this->max_quota,
        ];
    }
}
