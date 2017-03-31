<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2017 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar\Data;

use Cheddar\Cheddar;
use Cheddar\Currencies;
use Cheddar\Exceptions\CheddarException;

class Payment
{
    public $uuid;
    public $service;
    public $variable_symbol;
    public $constant_symbol;

    public $amount = 0.00;
    public $periodicity = 0;
    public $periodicity_no = 1;

    private $api_endpoint;

    public function __construct($data = [], $options = [])
    {
        $this->api_endpoint = Cheddar::PRODUCTION_URL;

        $this->uuid = $data['uuid'];
        $this->service = $data['service']['handle'];
        $this->variable_symbol = $data['variable_symbol'];
        $this->constant_symbol = $data['constant_symbol'];

        $this->amount = (float) $data['amount'];
        $this->currency = Currencies::get($data['currency']);
        $this->status = $data['status']['status'];

        if (isset($data['periodicity'])) {
            $this->periodicity = (int) $data['periodicity'];
        }

        if (isset($data['periodicity_no'])) {
            $this->periodicity_no = (int) $data['periodicity_no'];
        }

        if (!empty($data['charge_on'])) {
            try {
                $this->charge_on = new \Datetime($data['charge_on']);
            } catch (Exception $e) {
                $this->charge_on = null;
            }
        }

        if (!empty($data['card_expire_on'])) {
            try {
                $this->card_expire_on = new \Datetime($data['card_expire_on']);
            } catch (Exception $e) {
                $this->card_expire_on = null;
            }
        }

        if (!empty($data['card_no'])) {
            $this->card_no = $data['card_no'];
        }

        if (!empty($data['transaction_identifier'])) {
            $this->transaction_identifier = $data['transaction_identifier'];
        }

        if (!empty($options) AND isset($options['api_endpoint']) AND is_string($options['api_endpoint']))
        {
            $this->api_endpoint = $options['api_endpoint'];
        }
    }

    public function redirectUrl()
    {
        if (empty($this->uuid)) {
            throw new CheddarException(
                'Further processing is not possible due to incomplete payment '
                . 'object (payment UUID is missing)'
            );
        }

        if ($this->status !== 'none') {
            throw new CheddarException(sprintf(
                'Further processing is not possible due to unprocessable '
                . 'status %s (The only valid payment status for further '
                . 'processing is \'none\')',
                empty($this->status) ? '' : $this->status
            ));
        }

        return sprintf('%s/r/%s', $this->api_endpoint, $this->uuid);
    }
}
