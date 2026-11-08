<?php

declare(strict_types=1);

namespace Metarchy\Home;

/** A person who joined the society and takes services from the market. */
final class Member
{
    public function __construct(
        public readonly string $handle,
        public readonly string $name,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('a member carries a handle');
        }
        if (trim($name) === '') {
            throw new \RuntimeException('a member carries a name');
        }
    }
}
