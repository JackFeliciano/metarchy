<?php

declare(strict_types=1);

namespace Metarchy\Home;

/** A house holds its seats, its motions and the records of what it closed. */
final class House
{
    /** @var array<string, Seat> */
    private array $seats = [];

    /** @var list<Motion> */
    private array $motions = [];

    /** @var list<array<string, mixed>> */
    private array $records = [];

    public function __construct(public readonly string $name)
    {
        if (trim($name) === '') {
            throw new \RuntimeException('a house carries a name');
        }
    }

    public function seat(Seat $seat): void
    {
        if (isset($this->seats[$seat->handle])) {
            throw new \RuntimeException('the house already holds that seat');
        }
        $this->seats[$seat->handle] = $seat;
    }

    public function seats(): int
    {
        return count($this->seats);
    }

    /** A house votes with an odd number of seats, so add a seat in pairs. */
    public function seatsAreOdd(): bool
    {
        return $this->seats() % 2 === 1;
    }

    public function open(string $title, string $day, int $daysOpen): Motion
    {
        if (!$this->seatsAreOdd()) {
            throw new \RuntimeException('a house votes with an odd number of seats');
        }
        $motion = new Motion($title, $this->name, $day, $daysOpen, $this->seats());
        $this->motions[] = $motion;

        return $motion;
    }

    /** @return list<Motion> */
    public function motions(): array
    {
        return $this->motions;
    }

    /** @return list<array<string, mixed>> */
    public function records(): array
    {
        return $this->records;
    }

    public function close(Motion $motion, string $day): array
    {
        if (array_search($motion, $this->motions, true) === false) {
            throw new \RuntimeException('that motion belongs to another house');
        }
        if (!$motion->closedOn($day)) {
            throw new \RuntimeException('that motion is still open');
        }
        $record = [
            'title' => $motion->title,
            'house' => $motion->house,
            'opened' => $motion->opened,
            'closed' => $day,
            'tally' => $motion->tally(),
            'result' => $motion->result(),
        ];
        $this->records[] = $record;

        return $record;
    }
}
