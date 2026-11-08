<?php

declare(strict_types=1);

namespace Metarchy\Home;

/**
 * One of the functions a government normally supplies. The list is fixed, so two
 * providers of the same service sit beside each other in the market.
 */
final class Service
{
    /** @var list<string> */
    public const ALL = [
        'civil-registration',
        'market',
        'currency',
        'defence',
        'law-enforcement',
        'courts',
        'education',
        'transport',
        'utilities',
        'sanitation',
        'public-spaces',
        'health',
        'social-security',
    ];

    public function __construct(public readonly string $handle)
    {
        if (!in_array($handle, self::ALL, true)) {
            throw new \RuntimeException('unknown service: ' . $handle);
        }
    }

    public function label(): string
    {
        return str_replace('-', ' ', $this->handle);
    }
}
