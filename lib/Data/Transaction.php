<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2018 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar\Data;

use Cheddar\Cheddar;
use Cheddar\Currencies;
use Cheddar\Exceptions\CheddarException;
use Exception;

class Transaction
{
    public $uuid;
    public $type;
    public $bank_identifier;
    public $payment;

    public $variable_symbol;
    public $specific_symbol;
    public $constant_symbol;

    public $amount = 0.00;
    public $currency;

    public $description = '';
    public $notes = '';

    public $booked_at = null;
    public $created_at = null;

    private $api_endpoint;

    public function __construct($data = [], $options = [])
    {
        $this->api_endpoint = Cheddar::PRODUCTION_URL;

        $this->uuid = $data['uuid'];
        $this->type = $data['type'];
        $this->payment_uuid = $data['payment'];
        $this->bank_identifier = $data['bank_identifier'];

        if (!empty($data['variable_symbol'])) {
            $this->variable_symbol = $data['variable_symbol'];
        }

        if (!empty($data['constant_symbol'])) {
            $this->constant_symbol = $data['constant_symbol'];
        }

        if (!empty($data['specific_symbol'])) {
            $this->specific_symbol = $data['specific_symbol'];
        }

        $this->amount = (float) $data['amount'];
        $this->currency = Currencies::get($data['currency']);

        if (!empty($data['notes'])) {
            $this->notes = $data['notes'];
        }

        if (!empty($data['description'])) {
            $this->description = $data['description'];
        }

        if (!empty($data['booked_at'])) {
            try {
                $this->booked_at = new \Datetime($data['booked_at']);
            } catch (Exception $e) {}
        }

        if (!empty($data['created_at'])) {
            try {
                $this->created_at = new \Datetime($data['created_at']);
            } catch (Exception $e) {}
        }

        if (!empty($options) AND isset($options['api_endpoint']) AND is_string($options['api_endpoint']))
        {
            $this->api_endpoint = $options['api_endpoint'];
        }
    }
}
