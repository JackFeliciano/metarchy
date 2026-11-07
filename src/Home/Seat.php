<?php

declare(strict_types=1);

namespace Metarchy\Home;

/** One voting position. A seat answers a motion once and only once. */
final class Seat
{
    public function __construct(
        public readonly string $handle,
        public readonly string $holder,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('a seat carries a handle');
        }
        if (trim($holder) === '') {
            throw new \RuntimeException('a seat carries a holder');
        }
    }
}
