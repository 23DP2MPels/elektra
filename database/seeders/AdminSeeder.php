<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@elektra.test');
        $password = env('ADMIN_PASSWORD', 'password');

        $existing = User::where('epasts', $email)->first();
        if ($existing) {
            $existing->update([
                'loma' => 'administrators',
                'parole' => Hash::make($password),
            ]);
            $this->command?->info("Administrators atjaunināts: {$email}");
            return;
        }

        if (User::where('loma', 'administrators')->exists()) {
            $this->command?->info('Administrators jau eksistē, neko nemainām.');
            return;
        }

        User::create([
            'vards' => 'Administrators',
            'epasts' => $email,
            'parole' => Hash::make($password),
            'loma' => 'administrators',
        ]);

        $this->command?->info("Administrators izveidots: {$email} / {$password}");
    }
}
