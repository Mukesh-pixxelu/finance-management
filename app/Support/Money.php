<?php

namespace App\Support;

final class Money
{
    public static function indian(string|float|int|null $amount): string
    {
        $value = number_format((float) ($amount ?? 0), 2, '.', '');
        $negative = str_starts_with($value, '-');
        $value = ltrim($value, '-');

        [$integer, $decimal] = explode('.', $value);

        if (strlen($integer) > 3) {
            $lastThree = substr($integer, -3);
            $remaining = substr($integer, 0, -3);
            $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
            $integer = $remaining.','.$lastThree;
        }

        return ($negative ? '-' : '').$integer.'.'.$decimal;
    }
}
