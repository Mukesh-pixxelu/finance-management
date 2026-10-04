<?php

namespace App\Policies;

use App\Models\Pension;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PensionPolicy
{
    public function update(User $user, Pension $pension): Response
    {
        return $this->owns($user, $pension);
    }

    public function delete(User $user, Pension $pension): Response
    {
        return $this->owns($user, $pension);
    }

    private function owns(User $user, Pension $pension): Response
    {
        return $pension->user()->is($user)
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
