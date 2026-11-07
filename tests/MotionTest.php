<?php

declare(strict_types=1);

use Metarchy\Home\Motion;
use Metarchy\Home\Vote;
use Metarchy\Tests\Assert;

function mta_motion(int $seats = 5): Motion
{
    return new Motion('Hold the index on one page', 'Assembly', '2026-10-01', 7, $seats);
}

return [
    ['a motion names a house and stays open at least a day', static function (): void {
        Assert::throws(static fn () => new Motion(' ', 'Assembly', '2026-10-01', 7, 5), 'carries a title');
        Assert::throws(static fn () => new Motion('A title', ' ', '2026-10-01', 7, 5), 'names its house');
        Assert::throws(static fn () => new Motion('A title', 'Assembly', '2026-10-01', 0, 5), 'at least one day');
    }],
    ['a house holds an odd number of seats', static function (): void {
        Assert::throws(static fn () => mta_motion(4), 'odd number of seats');
        Assert::throws(static fn () => mta_motion(0), 'odd number of seats');
    }],
    ['a vote answers yes, no or abstain', static function (): void {
        Assert::throws(static fn () => new Vote('seat-1', 'maybe'), 'yes, no or abstain');
    }],
    ['a seat answers once', static function (): void {
        $motion = mta_motion();
        $motion->cast(new Vote('seat-1', 'yes'));
        Assert::throws(static fn () => $motion->cast(new Vote('seat-1', 'no')), 'already answered');
        Assert::same(1, $motion->answered());
    }],
    ['the quorum is two thirds of the seats', static function (): void {
        Assert::same(4, mta_motion(5)->quorum());
        Assert::same(5, mta_motion(7)->quorum());
        Assert::same(2, mta_motion(3)->quorum());
    }],
    ['a motion carries when yes outnumbers no and the quorum is met', static function (): void {
        $motion = mta_motion();
        foreach ([['seat-1', 'yes'], ['seat-2', 'yes'], ['seat-3', 'no'], ['seat-4', 'yes']] as [$seat, $answer]) {
            $motion->cast(new Vote($seat, $answer));
        }
        Assert::same('carried', $motion->result());
    }],
    ['a motion below the quorum loses', static function (): void {
        $motion = mta_motion();
        $motion->cast(new Vote('seat-1', 'yes'));
        $motion->cast(new Vote('seat-2', 'yes'));
        Assert::same('lost', $motion->result());
        Assert::same(2, $motion->tally()['yes']);
    }],
    ['an abstention counts towards the quorum and neither side', static function (): void {
        $motion = mta_motion();
        foreach ([['seat-1', 'yes'], ['seat-2', 'yes'], ['seat-3', 'abstain'], ['seat-4', 'abstain']] as [$seat, $answer]) {
            $motion->cast(new Vote($seat, $answer));
        }
        Assert::same(2, $motion->tally()['abstain']);
        Assert::same('carried', $motion->result());
    }],
    ['a motion closes when every seat has answered', static function (): void {
        $motion = mta_motion(3);
        $motion->cast(new Vote('seat-1', 'yes'));
        Assert::same(false, $motion->closedOn('2026-10-02'));
        $motion->cast(new Vote('seat-2', 'no'));
        $motion->cast(new Vote('seat-3', 'yes'));
        Assert::same(true, $motion->closedOn('2026-10-02'));
    }],
    ['a motion closes when its window runs out', static function (): void {
        $motion = mta_motion();
        Assert::same('2026-10-08', $motion->closes());
        Assert::same(false, $motion->closedOn('2026-10-08'));
        Assert::same(true, $motion->closedOn('2026-10-09'));
    }],
];
