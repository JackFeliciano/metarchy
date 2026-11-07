<?php

declare(strict_types=1);

namespace Metarchy\Home;

/**
 * One house with five seats, three motions and two records. The sample shows a
 * motion that carried, a motion that lost on the quorum, and a motion still open.
 */
final class Sample
{
    private House $house;

    public function __construct()
    {
        $this->house = new House('Assembly of the InFolk projects');
        foreach ([['seat-1', 'Ana Reis'], ['seat-2', 'Bright Field'], ['seat-3', 'Kestrel Works'], ['seat-4', 'Marchmont'], ['seat-5', 'Paco Milano']] as [$handle, $holder]) {
            $this->house->seat(new Seat($handle, $holder));
        }

        $first = $this->house->open('Hold the project index on one page', '2026-10-01', 7);
        foreach ([['seat-1', 'yes'], ['seat-2', 'yes'], ['seat-3', 'no'], ['seat-4', 'yes'], ['seat-5', 'abstain']] as [$seat, $answer]) {
            $first->cast(new Vote($seat, $answer));
        }
        $this->house->close($first, '2026-10-05');

        $second = $this->house->open('Move the licence to a copyleft one', '2026-10-06', 7);
        foreach ([['seat-1', 'yes'], ['seat-2', 'no'], ['seat-3', 'no']] as [$seat, $answer]) {
            $second->cast(new Vote($seat, $answer));
        }
        $this->house->close($second, '2026-10-15');

        $third = $this->house->open('Open a second seat on the design board', '2026-10-20', 14);
        $third->cast(new Vote('seat-2', 'yes'));
    }

    public function house(): House
    {
        return $this->house;
    }
}
