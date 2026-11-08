<?php

declare(strict_types=1);

namespace Metarchy\Home;

/** One provider offering one service, at a price and on a delivery term. */
final class Listing
{
    public function __construct(
        public readonly string $handle,
        public readonly string $provider,
        public readonly Service $service,
        public readonly int $cents,
        public readonly int $days,
        public readonly bool $bookable = true,
        public readonly bool $free = false,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('a listing carries a handle');
        }
        if ($cents < 0) {
            throw new \RuntimeException('a listing carries no negative price');
        }
        if ($cents === 0 && !$free) {
            throw new \RuntimeException('a listing carries a price');
        }
        if ($days < 0) {
            throw new \RuntimeException('a listing carries a delivery term');
        }
    }

    public function offeredBy(string $provider): bool
    {
        return $this->provider === $provider;
    }
}
