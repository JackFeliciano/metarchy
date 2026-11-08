<?php

declare(strict_types=1);

namespace Metarchy\Home;

/**
 * The market. It holds providers, the listings they offer, the members who joined
 * and the orders those members placed.
 */
final class Market
{
    /** @var array<string, Provider> */
    private array $providers = [];

    /** @var array<string, Listing> */
    private array $listings = [];

    /** @var array<string, Member> */
    private array $members = [];

    /** @var list<Order> */
    private array $orders = [];

    public function addProvider(Provider $provider): void
    {
        if (isset($this->providers[$provider->handle])) {
            throw new \RuntimeException('the market already holds that provider');
        }
        $this->providers[$provider->handle] = $provider;
    }

    public function join(Member $member): void
    {
        if (isset($this->members[$member->handle])) {
            throw new \RuntimeException('that member has already joined');
        }
        $this->members[$member->handle] = $member;
    }

    public function list(Listing $listing): void
    {
        if (!isset($this->providers[$listing->provider])) {
            throw new \RuntimeException('a listing names a known provider');
        }
        if (isset($this->listings[$listing->handle])) {
            throw new \RuntimeException('the market already holds that listing');
        }
        $this->listings[$listing->handle] = $listing;
    }

    public function take(string $member, string $listing, string $day): Order
    {
        if (!isset($this->members[$member])) {
            throw new \RuntimeException('only a member takes a service');
        }
        if (!isset($this->listings[$listing])) {
            throw new \RuntimeException('the market holds no such listing');
        }
        $offer = $this->listings[$listing];
        if (!$offer->bookable) {
            throw new \RuntimeException('that listing is not bookable');
        }
        foreach ($this->orders as $order) {
            if ($order->member === $member && $order->listing === $listing) {
                throw new \RuntimeException('that member has already taken this listing');
            }
        }
        $order = new Order($member, $listing, $offer->provider, $offer->service->handle, $offer->cents, $day);
        $this->orders[] = $order;

        return $order;
    }

    /** @return list<Listing> */
    public function listings(): array
    {
        return array_values($this->listings);
    }

    /** @return list<Listing> */
    public function listingsOf(string $service): array
    {
        $out = [];
        foreach ($this->listings as $listing) {
            if ($listing->service->handle === $service) {
                $out[] = $listing;
            }
        }

        return $out;
    }

    /** @return list<Order> */
    public function orders(): array
    {
        return $this->orders;
    }

    /** @return list<Order> */
    public function ordersOf(string $member): array
    {
        $out = [];
        foreach ($this->orders as $order) {
            if ($order->member === $member) {
                $out[] = $order;
            }
        }

        return $out;
    }

    /** What one provider took in. */
    public function earned(string $provider): int
    {
        $sum = 0;
        foreach ($this->orders as $order) {
            if ($order->provider === $provider) {
                $sum += $order->cents;
            }
        }

        return $sum;
    }

    /** A listing by handle, so a caller can read what a member took. */
    public function listing(string $handle): ?Listing
    {
        return $this->listings[$handle] ?? null;
    }

    /** The cheapest listing for a service, or null when nobody offers it. */
    public function cheapest(string $service): ?Listing
    {
        $best = null;
        foreach ($this->listingsOf($service) as $listing) {
            if (!$listing->bookable) {
                continue;
            }
            if ($best === null || $listing->cents < $best->cents) {
                $best = $listing;
            }
        }

        return $best;
    }

    /** @return list<string> */
    public function uncovered(): array
    {
        $out = [];
        foreach (Service::ALL as $handle) {
            if ($this->cheapest($handle) === null) {
                $out[] = $handle;
            }
        }

        return $out;
    }

    public function provider(string $handle): Provider
    {
        if (!isset($this->providers[$handle])) {
            throw new \RuntimeException('the market holds no such provider');
        }

        return $this->providers[$handle];
    }
}
