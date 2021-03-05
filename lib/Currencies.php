<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2021 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar;

class Currencies
{
    const CZK = 'CZK';
    const DKK = 'DKK';
    const EUR = 'EUR';
    const GBP = 'GBP';
    const HUF = 'HUF';
    const CHF = 'CHF';
    const PLN = 'PLN';
    const USD = 'USD';

    private static $currencies = [
        'CZK' => [
            'currency' => 'Czech koruna',
            'alpha_code' => 'CZK',
            'numeric_code' => 203,
            'minor_unit' => 2
        ],
        'DKK' => [
            'currency' => 'Danish krone',
            'alpha_code' => 'DKK',
            'numeric_code' => 208,
            'minor_unit' => 2
        ],
        'EUR' => [
            'currency' => 'Euro',
            'alpha_code' => 'EUR',
            'numeric_code' => 978,
            'minor_unit' => 2
        ],
        'GBP' => [
            'currency' => 'Pound sterling',
            'alpha_code' => 'GBP',
            'numeric_code' => 826,
            'minor_unit' => 2
        ],
        'HUF' => [
            'currency' => 'Hungarian forint',
            'alpha_code' => 'HUF',
            'numeric_code' => 348,
            'minor_unit' => 2
        ],
        'CHF' => [
            'currency' => 'Swiff franc',
            'alpha_code' => 'CHF',
            'numeric_code' => 756,
            'minor_unit' => 2
        ],
        'PLN' => [
            'currency' => 'Polish złoty',
            'alpha_code' => 'PLN',
            'numeric_code' => 985,
            'minor_unit' => 2
        ],
        'USD' => [
            'currency' => 'United States dollar',
            'alpha_code' => 'USD',
            'numeric_code' => 840,
            'minor_unit' => 2
        ]
    ];

    public static function get($code)
    {
        if (is_string($code)) {
            return isset(self::$currencies[ $code ])
                ? self::$currencies[ $code ]
                : null;
        }

        if (is_numeric($code)) {
            $code = (int) $code;

            foreach (self::$currencies as $alpha_code => $data) {
                if (isset($data['numeric_code']) and $data['numeric_code'] === $code) {
                    return $data;
                }
            }
        }

        return null;
    }
}
