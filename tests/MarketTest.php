<?php

declare(strict_types=1);

namespace Metarchy\Tests;

use Metarchy\Home\Listing;
use Metarchy\Home\Market;
use Metarchy\Home\Member;
use Metarchy\Home\Provider;
use Metarchy\Home\Service;

return [
    'thirteen services are named' => static function (): void {
        Assert::count(13, Service::ALL);
    },

    'a service outside the list is refused' => static function (): void {
        Assert::throws(static fn (): Service => new Service('police'), 'unknown service');
    },

    'a provider is a company or the network' => static function (): void {
        Assert::throws(static fn (): Provider => new Provider('x', 'X', 'charity'), 'a company or the network');
    },

    'a market refuses a listing from an unknown provider' => static function (): void {
        $m = new Market();
        Assert::throws(
            static fn (): null => $m->list(new Listing('l1', 'ghost', new Service('health'), 100, 1)) ?? null,
            'known provider',
        );
    },

    'a listing carries a price' => static function (): void {
        Assert::throws(static fn (): Listing => new Listing('l1', 'p', new Service('health'), 0, 1), 'carries a price');
    },

    'a listing may carry no charge when it says so' => static function (): void {
        $offer = new Listing('l1', 'p', new Service('currency'), 0, 0, true, true);
        Assert::same(0, $offer->cents);
    },

    'a listing carries no negative price' => static function (): void {
        Assert::throws(static fn (): Listing => new Listing('l1', 'p', new Service('health'), -5, 1), 'no negative price');
    },

    'a listing carries a delivery term' => static function (): void {
        Assert::throws(static fn (): Listing => new Listing('l1', 'p', new Service('health'), 100, -1), 'delivery term');
    },

    'a member joins once' => static function (): void {
        $m = new Market();
        $m->join(new Member('ana', 'Ana Reis'));
        Assert::throws(static fn (): null => $m->join(new Member('ana', 'Ana Reis')) ?? null, 'already joined');
    },

    'a non member cannot take a service' => static function (): void {
        $m = new Market();
        $m->addProvider(new Provider('p', 'P', 'company'));
        $m->list(new Listing('l1', 'p', new Service('health'), 100, 1));
        Assert::throws(static fn (): \Metarchy\Home\Order => $m->take('nobody', 'l1', '2026-11-01'), 'only a member');
    },

    'the market refuses a listing that is not bookable' => static function (): void {
        $m = new Market();
        $m->addProvider(new Provider('p', 'P', 'company'));
        $m->join(new Member('ana', 'Ana Reis'));
        $m->list(new Listing('l1', 'p', new Service('health'), 100, 1, false));
        Assert::throws(static fn (): \Metarchy\Home\Order => $m->take('ana', 'l1', '2026-11-01'), 'not bookable');
    },

    'a member takes one listing once' => static function (): void {
        $m = new Market();
        $m->addProvider(new Provider('p', 'P', 'company'));
        $m->join(new Member('ana', 'Ana Reis'));
        $m->list(new Listing('l1', 'p', new Service('health'), 100, 1));
        $m->take('ana', 'l1', '2026-11-01');
        Assert::throws(static fn (): \Metarchy\Home\Order => $m->take('ana', 'l1', '2026-11-02'), 'already taken');
    },

    'an order carries the provider, the service and the amount' => static function (): void {
        $m = new Market();
        $m->addProvider(new Provider('p', 'P', 'company'));
        $m->join(new Member('ana', 'Ana Reis'));
        $m->list(new Listing('l1', 'p', new Service('health'), 1200, 1));
        $order = $m->take('ana', 'l1', '2026-11-01');
        Assert::same('p', $order->provider);
        Assert::same('health', $order->service);
        Assert::same(1200, $order->cents);
    },

    'a provider earns what its orders carried' => static function (): void {
        $m = new Market();
        $m->addProvider(new Provider('p', 'P', 'company'));
        $m->join(new Member('ana', 'Ana Reis'));
        $m->join(new Member('odo', 'Odo Fenn'));
        $m->list(new Listing('l1', 'p', new Service('health'), 1200, 1));
        $m->list(new Listing('l2', 'p', new Service('transport'), 800, 1));
        $m->take('ana', 'l1', '2026-11-01');
        $m->take('odo', 'l2', '2026-11-01');
        Assert::same(2000, $m->earned('p'));
    },

    'the cheapest listing wins a service' => static function (): void {
        $m = new Market();
        $m->addProvider(new Provider('p', 'P', 'company'));
        $m->addProvider(new Provider('q', 'Q', 'company'));
        $m->list(new Listing('l1', 'p', new Service('health'), 1200, 1));
        $m->list(new Listing('l2', 'q', new Service('health'), 900, 3));
        Assert::same('l2', $m->cheapest('health')?->handle);
    },

    'a service nobody offers is uncovered' => static function (): void {
        $m = new Market();
        $m->addProvider(new Provider('p', 'P', 'company'));
        $m->list(new Listing('l1', 'p', new Service('health'), 1200, 1));
        Assert::count(12, $m->uncovered());
    },
];
