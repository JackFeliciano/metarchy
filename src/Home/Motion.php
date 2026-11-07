<?php

declare(strict_types=1);

namespace Metarchy\Home;

/**
 * A motion collects the answers of a house. It closes when every seat has answered
 * or when its window runs out, and a seat answers once.
 */
final class Motion
{
    /** @var array<string, Vote> */
    private array $votes = [];

    public function __construct(
        public readonly string $title,
        public readonly string $house,
        public readonly string $opened,
        public readonly int $daysOpen,
        public readonly int $seats,
    ) {
        if (trim($title) === '') {
            throw new \RuntimeException('a motion carries a title');
        }
        if (trim($house) === '') {
            throw new \RuntimeException('a motion names its house');
        }
        if ($daysOpen < 1) {
            throw new \RuntimeException('a motion stays open at least one day');
        }
        if ($seats < 1 || $seats % 2 === 0) {
            throw new \RuntimeException('a house holds an odd number of seats');
        }
    }

    public function cast(Vote $vote): void
    {
        if (isset($this->votes[$vote->seat])) {
            throw new \RuntimeException('that seat has already answered');
        }
        if (count($this->votes) >= $this->seats) {
            throw new \RuntimeException('every seat has answered');
        }
        $this->votes[$vote->seat] = $vote;
    }

    public function closedOn(string $day): bool
    {
        return count($this->votes) === $this->seats || $day > $this->closes();
    }

    public function closes(): string
    {
        return (new \DateTimeImmutable($this->opened))->modify('+' . $this->daysOpen . ' days')->format('Y-m-d');
    }

    /** @return array{yes: int, no: int, abstain: int} */
    public function tally(): array
    {
        $out = ['yes' => 0, 'no' => 0, 'abstain' => 0];
        foreach ($this->votes as $vote) {
            $out[$vote->answer]++;
        }

        return $out;
    }

    public function answered(): int
    {
        return count($this->votes);
    }

    /** A motion carries when yes outnumbers no and the answers reach the quorum. */
    public function result(): string
    {
        $tally = $this->tally();
        if ($this->answered() < $this->quorum()) {
            return 'lost';
        }

        return $tally['yes'] > $tally['no'] ? 'carried' : 'lost';
    }

    public function quorum(): int
    {
        // Whole seats only, so 5 seats need 4, 7 need 5 and 3 need 2.
        return intdiv($this->seats * 2 + 2, 3);
    }
}
