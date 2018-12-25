<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2019 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar\Api;

use \Cheddar\Cheddar;

class Payment extends \Cheddar\Api
{
    private $options = [];

    public function __construct(Cheddar $cheddar)
    {
        parent::__construct($cheddar);

        $this->options = [
            'api_endpoint' => $this->client->apiEndpoint()
        ];
    }

    public function details($uuid)
    {
        $response = $this->request(
            'GET',
            '/api/v1/payments/'.$uuid,
            200
        );

        return new \Cheddar\Data\Payment($response->content(), $this->options);
    }

    public function create($service, $metadata)
    {
        if (!array_key_exists('payer_ip_address', $metadata)) {
            $metadata['payer_ip_address'] = $this->payerIP();
        }

        $response = $this->request(
            'POST',
            '/api/v1/payments/',
            201,
            [
                'service'     => $service,
                'metadata'    => $metadata
            ]
        );

        return new \Cheddar\Data\Payment($response->content(), $this->options);
    }

    public function update($uuid, $metadata)
    {
        $response = $this->request(
            'PUT',
            '/api/v1/payments/' . $uuid,
            200,
            [ 'metadata' => $metadata ]
        );

        return new \Cheddar\Data\Payment($response->content(), $this->options);
    }

    public function refund($uuid, $refund)
    {
        $response = $this->request(
            'POST',
            '/api/v1/payments/' . $uuid . '/refund',
            200,
            [ 'refund' => $refund ]
        );

        return new \Cheddar\Data\Payment($response->content(), $this->options);
    }
}
