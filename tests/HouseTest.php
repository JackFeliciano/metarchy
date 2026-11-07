<?php

declare(strict_types=1);

use Metarchy\Home\House;
use Metarchy\Home\Motion;
use Metarchy\Home\Seat;
use Metarchy\Home\Vote;
use Metarchy\Tests\Assert;

function mta_house(int $seats = 3): House
{
    $house = new House('Assembly');
    for ($i = 1; $i <= $seats; $i++) {
        $house->seat(new Seat('seat-' . $i, 'Holder ' . $i));
    }

    return $house;
}

return [
    ['a seat carries a handle and a holder', static function (): void {
        Assert::throws(static fn () => new Seat(' ', 'Ana Reis'), 'carries a handle');
        Assert::throws(static fn () => new Seat('seat-1', ' '), 'carries a holder');
    }],
    ['a house keeps its seats once', static function (): void {
        $house = new House('Assembly');
        $house->seat(new Seat('seat-1', 'Ana Reis'));
        Assert::throws(static fn () => $house->seat(new Seat('seat-1', 'Bright Field')), 'already holds that seat');
        Assert::same(1, $house->seats());
    }],
    ['a house with an even seat count opens no motion', static function (): void {
        Assert::throws(static fn () => mta_house(2)->open('A title', '2026-10-01', 7), 'odd number of seats');
    }],
    ['a closed motion writes a record that does not change', static function (): void {
        $house = mta_house(3);
        $motion = $house->open('Hold the index on one page', '2026-10-01', 7);
        $motion->cast(new Vote('seat-1', 'yes'));
        $motion->cast(new Vote('seat-2', 'yes'));
        // Two of three seats fall short of the quorum of two, so the window decides.
        Assert::throws(static fn () => $house->close($motion, '2026-10-02'), 'still open');
        $motion->cast(new Vote('seat-3', 'no'));
        $record = $house->close($motion, '2026-10-02');
        Assert::same('carried', $record['result']);
        Assert::same(2, $record['tally']['yes']);
        Assert::same(1, count($house->records()));
    }],
    ['a house closes no motion from another house', static function (): void {
        $mine = mta_house(3);
        $theirs = mta_house(3);
        $motion = $theirs->open('A title', '2026-10-01', 1);
        Assert::throws(static fn () => $mine->close($motion, '2026-11-01'), 'belongs to another house');
    }],
    ['the sample holds five seats, three motions and two records', static function (): void {
        $house = (new Metarchy\Home\Sample())->house();
        Assert::same(5, $house->seats());
        Assert::same(3, count($house->motions()));
        Assert::same(2, count($house->records()));
        Assert::same('carried', $house->records()[0]['result']);
        Assert::same('lost', $house->records()[1]['result']);
    }],
];
