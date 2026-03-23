<?php

namespace Database\Seeders;

use App\Models\PackageQuota;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'package_name' => 'Village Executive Package',
                'max_quota' => 10,
            ],
            [
                'package_name' => 'Village Premium Package',
                'max_quota' => 10,
            ],
            [
                'package_name' => 'Landy Package',
                'max_quota' => 10,
            ],
            [
                'package_name' => 'Dome Regular Package',
                'max_quota' => 10,
            ],
        ];

        foreach ($packages as $package) {
            PackageQuota::create($package);
        }
    }
}
