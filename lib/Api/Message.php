<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2020 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar\Api;

use Cheddar\Exceptions\MessageIntegrityException;

class Message extends \Cheddar\Api
{
    public function validate($uuid, $signature)
    {
        if ($this->sign($uuid) === $signature) {
            return true;
        }

        throw new MessageIntegrityException(sprintf(
            'Signature %s for message %s using account %s is incorrect',
            $signature,
            $uuid,
            $this->client->apiKey()
        ));
    }
}
