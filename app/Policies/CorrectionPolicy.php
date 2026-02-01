<?php

namespace App\Policies;

use App\Models\Correction;
use App\Models\User;

class CorrectionPolicy
{
    public function approve(User $user, Correction $correction): bool
    {
        return $user->isEditor();
    }
}
