<?php

declare(strict_types=1);

namespace Metarchy\Home;

/** A private company, or the network itself, that offers services in the market. */
final class Provider
{
    public function __construct(
        public readonly string $handle,
        public readonly string $name,
        public readonly string $kind,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('a provider carries a handle');
        }
        if (trim($name) === '') {
            throw new \RuntimeException('a provider carries a name');
        }
        if (!in_array($kind, ['company', 'network'], true)) {
            throw new \RuntimeException('a provider is a company or the network');
        }
    }

    public function isNetwork(): bool
    {
        return $this->kind === 'network';
    }
}
