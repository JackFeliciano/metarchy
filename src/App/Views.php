<?php

declare(strict_types=1);

namespace Metarchy\App;

use Metarchy\Home\Listing;
use Metarchy\Home\Sample;
use Metarchy\Home\Service;

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

    public function front(Sample $market): string
    {
        $TITLE = 'Metarchy, a market for an alternative society';
        $LEDE = 'Metarchy lets people build an alternative society within the one they already live in, with access to essential services through a shared marketplace.';
        $WHAT = 'Metarchy brings together private companies and network services to provide the essential functions of a government, from civil registration and currency to education, healthcare, transport and public safety. Members can find, choose and pay for services in one place, creating an alternative society without leaving their existing community.';

        $m = $market->market();
        $out = $this->header($TITLE);
        $out .= '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h($WHAT) . '</p>' . "\n";
        $out .= '<p class="stage">Services: ' . count(Service::ALL) . '. Listings: ' . count($m->listings())
            . '. Orders: ' . count($m->orders()) . '. Without a provider: ' . count($m->uncovered()) . '.</p>' . "\n";

        $out .= '<h2>The market</h2>' . "\n";
        $out .= '<div class="listings">' . "\n";
        $listings = $m->listings();
        usort($listings, static fn (\Metarchy\Home\Listing $a, \Metarchy\Home\Listing $b): int => [$a->service->handle, $a->cents] <=> [$b->service->handle, $b->cents]);
        foreach ($listings as $listing) {
            $out .= $this->listing($m->provider($listing->provider), $listing);
        }
        $out .= '</div>' . "\n";

        $out .= '<h2>Services without a provider</h2>' . "\n";
        if ($m->uncovered() === []) {
            $out .= '<p class="where">Every service carries at least one listing.</p>' . "\n";
        } else {
            $out .= '<p class="where">' . $this->h(implode(', ', array_map(static fn (string $s): string => str_replace('-', ' ', $s), $m->uncovered()))) . '.</p>' . "\n";
        }

        $out .= '<h2>What the market does</h2>' . "\n";
        $out .= '<ol class="steps">' . "\n";
        foreach ([
            ['Explore the marketplace', 'find the services available from private companies and the network.'],
            ['Choose the service you need', 'take it through the marketplace.'],
            ['Pay for your chosen service', 'and use it while continuing to live in your existing society.'],
        ] as [$step, $line]) {
            $out .= '<li><strong>' . $this->h($step) . '</strong> ' . $this->h($line) . '</li>' . "\n";
        }
        $out .= '</ol>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    private function listing(\Metarchy\Home\Provider $provider, Listing $listing): string
    {
        $out = '<article class="listing">' . "\n";
        $out .= '<h3>' . $this->h(str_replace('-', ' ', $listing->service->handle)) . '</h3>' . "\n";
        $out .= '<p class="where">' . $this->h($provider->name) . ($provider->isNetwork() ? ' (the network)' : '') . '</p>' . "\n";
        $out .= '<p class="price">' . $this->money($listing->cents) . '</p>' . "\n";
        $out .= '<p class="terms">Delivery in ' . $listing->days . ' day' . ($listing->days === 1 ? '' : 's')
            . ($listing->bookable ? '.' : '. Not bookable.') . '</p>' . "\n";
        $out .= '</article>' . "\n";

        return $out;
    }

    private function money(int $cents): string
    {
        return $cents === 0 ? 'No charge' : number_format($cents / 100, 2, '.', ',');
    }

    public function missing(): string
    {
        return $this->header('Not found') . "<h1>Not found</h1>\n<p>That path does not exist here.</p>\n" . $this->footer();
    }
}
