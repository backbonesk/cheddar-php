<?php

/*
 * This file is part of Cheddar.
 *
 * (c) 2017 BACKBONE, s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cheddar;

class PaymentStatuses
{
    const CANCELLED = 'cancelled';
    const COMPLETED = 'completed';
    const DENIED = 'denied';
    const EXPIRED = 'expired';
    const IN_PROGRESS = 'in_progress';
    const NONE = 'none';
    const PENDING = 'pending';
    const TIMEOUT = 'timeout';
}
