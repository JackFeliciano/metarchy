<?php

declare(strict_types=1);

namespace Metarchy\Tests;

use Metarchy\Home\Sample;

return [
    'the sample market holds nine listings' => static function (): void {
        Assert::count(9, (new Sample())->market()->listings());
    },

    'the sample market holds six orders' => static function (): void {
        Assert::count(6, (new Sample())->market()->orders());
    },

    'six services carry no provider' => static function (): void {
        $uncovered = (new Sample())->market()->uncovered();
        Assert::same('defence', $uncovered[0]);
        Assert::same('law-enforcement', $uncovered[1]);
        Assert::same('courts', $uncovered[2]);
        Assert::same('education', $uncovered[3]);
        Assert::same('utilities', $uncovered[4]);
        Assert::same('sanitation', $uncovered[5]);
        Assert::count(6, $uncovered);
    },

    'marlowe took the most in the sample' => static function (): void {
        $m = (new Sample())->market();
        Assert::same(12000, $m->earned('marlowe'));
        Assert::same(8300, $m->earned('arven'));
        Assert::same(7400, $m->earned('kestrel'));
        Assert::same(4200, $m->earned('northlin'));
        Assert::same(900, $m->earned('the-network'));
    },

    'the network lists a service at no charge' => static function (): void {
        $m = (new Sample())->market();
        Assert::same(0, $m->cheapest('currency')?->cents);
    },

    'a member holds its own orders' => static function (): void {
        $m = (new Sample())->market();
        Assert::count(2, $m->ordersOf('ana'));
        Assert::count(0, $m->ordersOf('pia'));
    },

    'health carries two providers' => static function (): void {
        Assert::count(2, (new Sample())->market()->listingsOf('health'));
    },

    'a network provider knows it is the network' => static function (): void {
        Assert::true((new Sample())->market()->provider('the-network')->isNetwork());
        Assert::false((new Sample())->market()->provider('marlowe')->isNetwork());
    },
];
