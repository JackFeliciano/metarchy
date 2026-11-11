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
        $LEDE = 'Metarchy creates alternative societies inside an existing one, following panarchy, with private companies and network services supplying the functions government normally provides.';
        $WHAT = 'The interstitial model lets a member meet their needs through a shared marketplace without relocating or leaving the communities they belong to. A provider lists a function with a price, and the member chooses among providers while staying where they are.';

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

        $out .= '<h2>What members took</h2>' . "\n";
        $out .= '<table class="orders"><thead><tr><th>Member</th><th>Service</th><th>Provider</th><th>Day</th><th>Paid</th></tr></thead><tbody>' . "\n";
        foreach ($m->orders() as $order) {
            $out .= '<tr><td>' . $this->h($order->member) . '</td><td>' . $this->h(str_replace('-', ' ', $order->service))
                . '</td><td>' . $this->h($m->provider($order->provider)->name) . '</td><td>' . $this->h($order->day)
                . '</td><td class="num">' . $this->money($order->cents) . '</td></tr>' . "\n";
        }
        $out .= '</tbody></table>' . "\n";

        $out .= '<h2>Services without a provider</h2>' . "\n";
        if ($m->uncovered() === []) {
            $out .= '<p class="where">Every service carries at least one listing.</p>' . "\n";
        } else {
            $out .= '<p class="where">' . $this->h(implode(', ', array_map(static fn (string $s): string => str_replace('-', ' ', $s), $m->uncovered()))) . '.</p>' . "\n";
        }

        $out .= '<h2>What the market does</h2>' . "\n";
        $out .= '<ol class="steps">' . "\n";
        foreach ([
            ['Open the service', 'Private companies and the network itself list services in the functions government usually supplies.'],
            ['Choose the provider', 'The member finds the service in the shared marketplace and picks the provider that suits their need.'],
            ['Pay through the market', 'The member pays through the marketplace, and the society grows without anyone moving.'],
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
