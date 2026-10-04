<?php

namespace App\Policies;

use App\Models\Saving;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SavingPolicy
{
    public function update(User $user, Saving $saving): Response
    {
        return $this->owns($user, $saving);
    }

    public function delete(User $user, Saving $saving): Response
    {
        return $this->owns($user, $saving);
    }

    private function owns(User $user, Saving $saving): Response
    {
        return $saving->user()->is($user)
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
