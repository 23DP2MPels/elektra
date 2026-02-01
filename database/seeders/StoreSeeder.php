<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            ['nosaukums' => '1a.lv', 'url' => 'https://www.1a.lv'],
            ['nosaukums' => 'Dateks', 'url' => 'https://www.dateks.lv'],
            ['nosaukums' => 'M79', 'url' => 'https://www.m79.lv'],
        ];

        foreach ($stores as $store) {
            Store::firstOrCreate(
                ['nosaukums' => $store['nosaukums']],
                ['url' => $store['url'] ?? null]
            );
        }
    }
}
