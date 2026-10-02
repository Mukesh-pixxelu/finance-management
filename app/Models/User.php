<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\TransactionType;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function savings(): HasMany
    {
        return $this->hasMany(Saving::class);
    }

    /**
     * @return array{income: string, expense: string, balance: string}
     */
    public function ledgerSummary(): array
    {
        $totals = $this->transactions()
            ->selectRaw('type, sum(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $income = $this->money($totals[TransactionType::Income->value] ?? '0');
        $expense = $this->money($totals[TransactionType::Expense->value] ?? '0');

        return [
            'income' => $income,
            'expense' => $expense,
            'balance' => bcsub($income, $expense, 2),
        ];
    }

    private function money(mixed $amount): string
    {
        return bcadd(is_numeric($amount) ? (string) $amount : '0', '0', 2);
    }
}
