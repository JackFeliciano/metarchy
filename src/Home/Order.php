<?php

declare(strict_types=1);

namespace Metarchy\Home;

/** One member taking one listing on one day. The order stays once written. */
final class Order
{
    public function __construct(
        public readonly string $member,
        public readonly string $listing,
        public readonly string $provider,
        public readonly string $service,
        public readonly int $cents,
        public readonly string $day,
    ) {
        if (trim($day) === '') {
            throw new \RuntimeException('an order carries a day');
        }
    }
}
