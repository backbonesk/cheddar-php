<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2016 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar\Api;

class Payment extends \Cheddar\Api
{
    public function details($uuid)
    {
        $response = $this->request(
            'GET',
            '/api/v1/payments/'.$uuid,
            200
        );

        return new \Cheddar\Data\Payment($response->content());
    }

    public function create($service, $metadata)
    {
        $response = $this->request(
            'POST',
            '/api/v1/payments/',
            201,
            [
                'service'     => $service,
                'metadata'     => $metadata
            ]
        );

        return new \Cheddar\Data\Payment($response->content());
    }

    public function update($uuid, $metadata)
    {
        $response = $this->request(
            'PUT',
            '/api/v1/payments/' . $uuid,
            200,
            [ 'refund' => $metadata ]
        );

        return new \Cheddar\Data\Payment($response->content());
    }

    public function refund($uuid, $refund)
    {
        $response = $this->request(
            'POST',
            '/api/v1/payments/' . $uuid . '/refund',
            200,
            [ 'refund' => $refund ]
        );

        return new \Cheddar\Data\Payment($response->content());
    }
}
