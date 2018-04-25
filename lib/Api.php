<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2018 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar;

use Cheddar\Cheddar;
use Cheddar\Exceptions\ApiException;
use Cheddar\Transport\Curl;

abstract class Api
{
    protected $client;

    public function __construct(Cheddar $cheddar)
    {
        $this->client = $cheddar;
    }

    protected function request($method, $url, $expected_http_code = 200, $data = '')
    {
        $data = !empty($data)
            ? json_encode($data, JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES)
            : '';

        $custom_headers = [
            'X-Key: ' . $this->client->apiKey(),
            'X-Signature: ' . $this->sign($url, $data)
        ];

        $response = new Curl(
            $method,
            $this->client->apiEndpoint() . $url,
            $data,
            $custom_headers
        );

        $data = $response->content();

        if ($response->httpStatusCode() !== $expected_http_code) {
            throw new ApiException(
                isset($data['error']) ? $data['error'] : 'Unknown error',
                $data,
                $response->httpStatusCode()
            );
        }

        return $response;
    }

    protected function payerIP()
    {
        $addresses = [];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            foreach (array_reverse(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $x_f) {
                $x_f = trim($x_f);

                if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $x_f)) {
                    $addresses[] = $x_f;
                }
            }
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $addresses[] = $_SERVER['REMOTE_ADDR'];
        }

        if (isset($_SERVER['HTTP_PROXY_USER'])) {
            $addresses[] = $_SERVER['HTTP_PROXY_USER'];
        }

        ksort($addresses);

        foreach ($addresses as $address) {
            if (isset($address))
                return $address;
        }
    }

    protected function sign($url, $data = '')
    {
        $signature_base = $url;

        if (!empty($data)) {
            $signature_base .= sprintf(';%s', $data);
        }

        return hash_hmac(
            'sha1',
            $signature_base,
            $this->client->apiSecret()
        );
    }
}
