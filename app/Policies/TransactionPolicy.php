<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    public function delete(User $user, Transaction $transaction): Response
    {
        return $transaction->user()->is($user)
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
