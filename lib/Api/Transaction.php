<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2018 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar\Api;

use \Cheddar\Cheddar;
use \Cheddar\Exceptions\ApiException;

class Transaction extends \Cheddar\Api
{
    private $options = [];

    public function __construct(Cheddar $cheddar)
    {
        parent::__construct($cheddar);

        $this->options = [
            'api_endpoint' => $this->client->apiEndpoint()
        ];
    }

    public function all($options = [])
    {
        $url = '/api/v1/transactions/';

        if (!empty($options)) {
            $url .= '?' . http_build_query($options);
        }

        $response = $this->request('GET', $url, 200);

        $data = $response->content();

        if (!isset($data['metadata']) OR !isset($data['transactions']))
            throw new ApiException(
                'Response not formatted properly as there are no `metadata` or ' .
                '`transactions` keys',
                $data,
                $response->httpStatusCode()
            );

        return [
            $data['metadata'],
            array_map(function($transaction) {
                return new \Cheddar\Data\Transaction($transaction, $this->options);
            }, (array) $data['transactions'])
        ];
    }

    public function details($uuid)
    {
        $response = $this->request(
            'GET',
            '/api/v1/transactions/'.$uuid,
            200
        );

        return new \Cheddar\Data\Transaction($response->content(), $this->options);
    }

    public function update($uuid, $metadata)
    {
        $response = $this->request(
            'PUT',
            '/api/v1/transactions/' . $uuid,
            200,
            [ 'metadata' => $metadata ]
        );

        return new \Cheddar\Data\Payment($response->content(), $this->options);
    }
}
