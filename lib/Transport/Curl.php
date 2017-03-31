<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2017 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar\Transport;

use Cheddar\Cheddar;
use Cheddar\Exceptions\ApiException;

class Curl
{
    const CUSTOM_HEADER_REGEX = '/^X-(?<name>[a-zA-Z0-9-]+): ?(?<value>.+)?/i';

    private $http_code = 0;
    private $headers = [];
    private $content = null;
    private $raw_content = null;

    private $command = '';

    public function __construct($method, $url, $data = null, $custom_headers = [])
    {
        if (!function_exists('curl_init')) {
            throw new ApiException('Curl is required');
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_USERAGENT => 'cheddar-php-' . Cheddar::VERSION
        ];

        $ch = curl_init();

        switch ($method) {
            case 'POST':
                $options[ CURLOPT_CUSTOMREQUEST ] = 'POST';
                $options[ CURLOPT_POSTFIELDS ] = $data;
                break;

            case 'PUT':
                $options[ CURLOPT_CUSTOMREQUEST ] = 'PUT';
                $options[ CURLOPT_POSTFIELDS ] = $data;
                break;

            case 'DELETE':
                $options[ CURLOPT_CUSTOMREQUEST ] = 'DELETE';
                $data = '';
                break;

            case 'GET':
                $data = '';
                break;
        }

        if ($data) {
            $custom_headers[] = 'Content-Length: ' . strlen($data);
        }

        // Discard 'Expect: 100-continue' behavior forcing cURL to wait
        // for two seconds if the server does not understand it.
        $options[ CURLOPT_HTTPHEADER ] = array_merge($custom_headers, [
            'Expect:',
            'Content-Type: application/json'
        ]);

        $this->command = 'curl -X '.$method;

        foreach ($options[ CURLOPT_HTTPHEADER ] as $header) {
            $this->command .= " --header '" . $header . "'";
        }

        if ($data) {
            $this->command .= " --data '" . $data . "'";
        }

        $this->command .= ' ' . $url;

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        // With dual stacked DNS responses, it's possible for a server to
        // have IPv6 enabled but not have IPv6 connectivity.  If this is
        // the case, curl will try IPv4 first and if that fails, then it will
        // fall back to IPv6 and the error EHOSTUNREACH is returned by the
        // operating system.
        if ($result === false and empty($this->curl_opts[ CURLOPT_IPRESOLVE ])) {
            $regex = '/Failed to connect to ([^:].*): Network is unreachable/';

            if (preg_match($regex, curl_error($ch), $matches)) {
                if (strlen(@inet_pton($matches[ 1 ])) === 16) {
                    $this->curl_opts[ CURLOPT_IPRESOLVE ] = CURL_IPRESOLVE_V4;
                    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    $result = curl_exec($ch);
                }
            }
        }

        $this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($result === false) {
            $exception_data = [
                'code' => curl_errno($ch),
                'message' => curl_error($ch)
            ];

            curl_close($ch);

            throw new ApiException('curl', $exception_data);
        } else {
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = explode("\r\n", substr($result, 0, $header_size));

            $this->raw_content = substr($result, $header_size);
            $this->content = json_decode($this->raw_content, true);

            foreach ($header as $line) {
                if (preg_match(self::CUSTOM_HEADER_REGEX, $line, $match)) {
                    $this->headers[ $match['name'] ] = is_numeric($match['value'])
                        ? (int) $match['value']
                        : $match['value'];
                }
            }

            curl_close($ch);
        }

        return $this->content;
    }

    public function content()
    {
        return $this->content;
    }

    public function headers()
    {
        return $this->headers;
    }

    public function httpStatusCode()
    {
        return !empty($this->http_code)
            ? $this->http_code
            : null;
    }
}
