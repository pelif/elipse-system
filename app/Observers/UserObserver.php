<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Password;

class UserObserver
{

    public function created(User $user)
    {
        Password::sendResetLink(['email' => $user->email]);
    }
}
