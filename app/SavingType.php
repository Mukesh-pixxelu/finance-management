<?php

namespace App;

enum SavingType: string
{
    case SavingsAccount = 'savings_account';
    case FixedDeposit = 'fd';
    case RecurringDeposit = 'rd';

    public function label(): string
    {
        return match ($this) {
            self::SavingsAccount => 'Savings account',
            self::FixedDeposit => 'FD',
            self::RecurringDeposit => 'RD',
        };
    }

    public function amountLabel(): string
    {
        return match ($this) {
            self::SavingsAccount => 'Balance',
            self::FixedDeposit => 'Principal',
            self::RecurringDeposit => 'Monthly installment',
        };
    }
}
