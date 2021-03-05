<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2021 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar\Exceptions;

class CheddarException extends \RuntimeException
{
    protected $message;

    public $http_body;
    public $http_status;

    public function __construct($message, $http_body = null, $http_status = null)
    {
        parent::__construct($message);

        $this->http_body = $http_body;
        $this->http_status = $http_status;
    }
}
