<?php

declare(strict_types=1);

namespace Metarchy\Home;

/**
 * A small market. Four companies and the network offer eight services, five members
 * joined, and six orders were taken. Currency, courts, education, utilities and
 * sanitation carry one provider each, so a member has no rival to compare.
 */
final class Sample
{
    private Market $market;

    public function __construct()
    {
        $this->market = new Market();
        foreach ([
            ['northlin', 'Northlin Registry', 'company'],
            ['arven', 'Arven Transit', 'company'],
            ['marlowe', 'Marlowe Health', 'company'],
            ['kestrel', 'Kestrel Assurance', 'company'],
            ['the-network', 'The network', 'network'],
        ] as [$handle, $name, $kind]) {
            $this->market->addProvider(new Provider($handle, $name, $kind));
        }

        foreach ([['ana', 'Ana Reis'], ['bright', 'Bright Field'], ['mira', 'Mira Costa'], ['odo', 'Odo Fenn'], ['pia', 'Pia Marsh']] as [$handle, $name]) {
            $this->market->join(new Member($handle, $name));
        }

        // The network wallet is the one listing at no charge, so the market shows a
        // free service beside the paid ones.
        foreach ([
            ['northlin-registry', 'northlin', 'civil-registration', 4200, 3, true],
            ['arven-passes', 'arven', 'transport', 1800, 1, true],
            ['arven-freight', 'arven', 'market', 6500, 2, true],
            ['marlowe-clinic', 'marlowe', 'health', 12000, 1, true],
            ['marlowe-visits', 'marlowe', 'health', 9500, 5, true],
            ['kestrel-cover', 'kestrel', 'social-security', 7400, 7, true],
            ['network-wallet', 'the-network', 'currency', 0, 0, true, true],
            ['network-streets', 'the-network', 'public-spaces', 900, 1, true],
        ] as $row) {
            $this->market->list(new Listing($row[0], $row[1], new Service($row[2]), $row[3], $row[4], $row[5] ?? true, $row[6] ?? false));
        }

        $this->market->list(new Listing('kestrel-cover-annual', 'kestrel', new Service('defence'), 24000, 30, false));

        foreach ([
            ['ana', 'northlin-registry', '2026-11-02'],
            ['ana', 'network-streets', '2026-11-02'],
            ['bright', 'marlowe-clinic', '2026-11-03'],
            ['mira', 'arven-passes', '2026-11-04'],
            ['mira', 'kestrel-cover', '2026-11-05'],
            ['odo', 'arven-freight', '2026-11-06'],
        ] as [$member, $listing, $day]) {
            $this->market->take($member, $listing, $day);
        }
    }

    public function market(): Market
    {
        return $this->market;
    }
}
