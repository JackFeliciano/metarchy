<?php

declare(strict_types=1);

namespace Metarchy\App;

use Metarchy\Home\Motion;
use Metarchy\Home\Sample;

/**
 * The HTML. Every string reaching the page goes through h(), so a title with an
 * ampersand cannot break the markup.
 */
final class Views
{
    public function __construct(private readonly string $base = '/')
    {
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function header(string $title): string
    {
        $base = $this->h($this->base);
        $t = $this->h($title);

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$t}</title>
<base href="{$base}">
<link rel="stylesheet" href="assets/site.css">
</head>
<body>
<main>
HTML;
    }

    public function footer(): string
    {
        return "</main>\n</body>\n</html>\n";
    }

    public function front(Sample $sample): string
    {
        $house = $sample->house();
        $TITLE = 'Metarchy, a house that records its decisions';
        $LEDE = 'Metarchy is the governance layer for the InFolk projects, where houses decide motions through recorded votes.';
        $WHAT = 'A house holds an odd number of seats, and each seat can open a motion. The motion names its house, opening day and voting window in days. Each seat can answer once with yes, no or abstain. A motion closes when every seat has answered or its window expires. At least two thirds of the seats, rounded up, must answer. The motion carries when yes votes outnumber no votes and the answers reach quorum. Abstentions count towards quorum but neither side.';

        $out = $this->header($TITLE);
        $out .= '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h($WHAT) . '</p>' . "\n";
        $out .= '<p class="stage">' . $this->h($house->name) . '. Seats: ' . $house->seats()
            . '. Quorum: ' . (new Motion('x', $house->name, '2026-01-01', 1, $house->seats()))->quorum()
            . ' of ' . $house->seats() . '. Motions: ' . count($house->motions())
            . '. Records: ' . count($house->records()) . '.</p>' . "\n";

        $out .= '<h2>Open motions</h2>' . "\n";
        $out .= '<div class="motions">' . "\n";
        foreach ($house->motions() as $motion) {
            $out .= $this->motion($motion);
        }
        $out .= '</div>' . "\n";

        $out .= '<h2>Records</h2>' . "\n";
        if ($house->records() === []) {
            $out .= '<p class="where">The house has closed no motion.</p>' . "\n";
        } else {
            $out .= '<table class="records"><thead><tr><th>Motion</th><th>Opened</th><th>Closed</th><th>Yes</th><th>No</th><th>Abstain</th><th>Result</th></tr></thead><tbody>' . "\n";
            foreach ($house->records() as $record) {
                $out .= '<tr><td>' . $this->h((string) $record['title']) . '</td><td>' . $this->h((string) $record['opened'])
                    . '</td><td>' . $this->h((string) $record['closed']) . '</td><td class="num">' . $record['tally']['yes']
                    . '</td><td class="num">' . $record['tally']['no'] . '</td><td class="num">' . $record['tally']['abstain']
                    . '</td><td>' . $this->h((string) $record['result']) . '</td></tr>' . "\n";
            }
            $out .= '</tbody></table>' . "\n";
        }

        $out .= '<h2>What the house does</h2>' . "\n";
        $out .= '<ol class="steps">' . "\n";
        foreach ([
            ['Seats a house', 'The house holds an odd number of seats.'],
            ['Opens a motion', 'A seat opens a motion with its house, opening day and voting window.'],
            ['Casts a vote', 'Each seat answers once with yes, no or abstain.'],
            ['Closes the motion', 'The motion closes when every seat answers or its voting window expires.'],
            ['Writes the record', 'The system records the outcome permanently when the motion closes.'],
        ] as [$step, $line]) {
            $out .= '<li><strong>' . $this->h($step) . '</strong> ' . $this->h($line) . '</li>' . "\n";
        }
        $out .= '</ol>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    private function motion(Motion $motion): string
    {
        $tally = $motion->tally();
        $out = '<article class="motion">' . "\n";
        $out .= '<h3>' . $this->h($motion->title) . '</h3>' . "\n";
        $out .= '<p class="where">Opened ' . $this->h($motion->opened) . ', closes ' . $this->h($motion->closes())
            . '. Answered ' . $motion->answered() . ' of ' . $motion->seats . '. Quorum ' . $motion->quorum() . '.</p>' . "\n";
        $out .= '<p class="where">Yes ' . $tally['yes'] . ', no ' . $tally['no'] . ', abstain ' . $tally['abstain'] . '.</p>' . "\n";
        $out .= '</article>' . "\n";

        return $out;
    }

    public function missing(): string
    {
        return $this->header('Not found') . "<h1>Not found</h1>\n<p>That path does not exist here.</p>\n" . $this->footer();
    }
}
