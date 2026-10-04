<?php

namespace App\Policies;

use App\Models\Insurance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InsurancePolicy
{
    public function update(User $user, Insurance $insurance): Response
    {
        return $this->owns($user, $insurance);
    }

    public function delete(User $user, Insurance $insurance): Response
    {
        return $this->owns($user, $insurance);
    }

    private function owns(User $user, Insurance $insurance): Response
    {
        return $insurance->user()->is($user)
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
