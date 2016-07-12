<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2016 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar;

final class Cheddar
{
    const VERSION = '0.4.0';

    const SERVICE_SPOROPAY = 'sporopay';
    const SERVICE_EPLATBY = 'eplatby';
    const SERVICE_ECARD = 'ecard';
    const SERVICE_TATRAPAY = 'tatrapay';
    const SERVICE_CARDPAY = 'cardpay';
    const SERVICE_COMFORTPAY = 'comfortpay';
    const SERVICE_PAYPAL = 'paypal';
    const SERVICE_GPWEBPAY = 'gpwebpay';
    const SERVICE_ITERMINAL = 'iterminal';

    public static $api_endpoint = 'https://cheddar.backbone.sk';

    private $api_key;
    private $api_secret;

    public function __construct($config)
    {
        if (!empty($config['key'])) {
            $this->apiKey($config['key']);
        }

        if (!empty($config['secret'])) {
            $this->apiSecret($config['secret']);
        }
    }

    public function apiKey($key = null)
    {
        if ($key === null) {
            return $this->api_key;
        }

        $this->api_key = $key;

        return $this;
    }

    public function apiSecret($secret = null)
    {
        if ($secret === null) {
            return $this->api_secret;
        }

        $this->api_secret = $secret;

        return $this;
    }

    public function payment()
    {
        return new Api\Payment($this);
    }

    public function message()
    {
        return new Api\Message($this);
    }
}
