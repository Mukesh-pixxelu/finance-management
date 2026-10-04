<?php

namespace App;

enum PensionScheme: string
{
    case AtalPension = 'apy';
    case Nps = 'nps';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::AtalPension => 'Atal Pension Yojana',
            self::Nps => 'NPS',
            self::Other => 'Other pension',
        };
    }
}
