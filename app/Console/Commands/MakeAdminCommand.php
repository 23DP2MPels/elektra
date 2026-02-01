<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdminCommand extends Command
{
    protected $signature = 'elektra:make-admin {email : Lietotāja e-pasts}';

    protected $description = 'Piešķir lietotājam administratora lomu (loma = administrators)';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('epasts', $email)->first();

        if (!$user) {
            $this->error("Lietotājs ar e-pastu \"{$email}\" nav atrasts. Vispirms reģistrējieties.");
            return self::FAILURE;
        }

        if ($user->loma === 'administrators') {
            $this->info("Lietotājs {$email} jau ir administrators.");
            return self::SUCCESS;
        }

        $user->update(['loma' => 'administrators']);
        $this->info("Lietotājam {$email} piešķirta administratora loma. Varat pieslēgties un pievienot/dzēst preces.");

        return self::SUCCESS;
    }
}
