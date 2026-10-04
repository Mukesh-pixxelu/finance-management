<?php

namespace App;

enum InsuranceType: string
{
    case Life = 'life';
    case Health = 'health';
    case Vehicle = 'vehicle';
    case Term = 'term';
    case Accident = 'accident';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Life => 'Life',
            self::Health => 'Health',
            self::Vehicle => 'Vehicle',
            self::Term => 'Term',
            self::Accident => 'Accident',
            self::Other => 'Other',
        };
    }
}
