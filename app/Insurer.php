<?php

namespace App;

final class Insurer
{
    /**
     * Common Indian insurers / schemes for the insurance form dropdown.
     *
     * @return list<string>
     */
    public static function options(): array
    {
        return [
            'PMSBY (Pradhan Mantri Suraksha Bima Yojana)',
            'PMJJBY (Pradhan Mantri Jeevan Jyoti Bima Yojana)',
            'LIC',
            'HDFC Life',
            'ICICI Prudential',
            'SBI Life',
            'Max Life',
            'Bajaj Allianz Life',
            'Star Health',
            'Niva Bupa',
            'Care Health',
            'HDFC ERGO',
            'ICICI Lombard',
            'Bajaj Allianz General',
            'New India Assurance',
            'Oriental Insurance',
            'United India Insurance',
            'National Insurance',
            'Go Digit',
            'Acko',
            'Other',
        ];
    }
}
