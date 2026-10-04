<?php

namespace Tests\Unit\Support;

use App\Support\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    #[DataProvider('indianAmounts')]
    public function test_it_formats_indian_currency(string|float $amount, string $expected): void
    {
        $this->assertSame($expected, Money::indian($amount));
    }

    /**
     * @return array<string, array{0: string|float, 1: string}>
     */
    public static function indianAmounts(): array
    {
        return [
            'thousands' => ['9250.00', '9,250.00'],
            'lakhs' => ['1500000.00', '15,00,000.00'],
            'one lakh' => ['107100.00', '1,07,100.00'],
            'under thousand' => ['860.00', '860.00'],
            'negative' => ['-1500.50', '-1,500.50'],
        ];
    }
}
