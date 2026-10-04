<?php

namespace App;

use Illuminate\Support\Str;

final class Bank
{
    /**
     * Top Indian banks for the savings form dropdown.
     *
     * @return list<string>
     */
    public static function options(): array
    {
        return [
            'State Bank of India',
            'HDFC Bank',
            'ICICI Bank',
            'Punjab National Bank',
            'Bank of Baroda',
            'Axis Bank',
            'Canara Bank',
            'Union Bank of India',
            'Bank of India',
            'Indian Bank',
            'Kotak Mahindra Bank',
            'Central Bank of India',
            'Indian Overseas Bank',
            'Yes Bank',
            'India Post Payments Bank',
            'Indian Post Office',
        ];
    }

    public static function slug(string $name): string
    {
        return Str::slug($name);
    }

    public static function nameFromSlug(string $slug): ?string
    {
        foreach (self::options() as $bank) {
            if (self::slug($bank) === $slug) {
                return $bank;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    public static function chartColors(): array
    {
        return [
            '#1d70e7',
            '#0ea5e9',
            '#6366f1',
            '#14b8a6',
            '#8b5cf6',
            '#f59e0b',
            '#ef4444',
            '#22c55e',
            '#ec4899',
            '#06b6d4',
            '#84cc16',
            '#f97316',
            '#a855f7',
            '#64748b',
            '#0f766e',
        ];
    }
}
