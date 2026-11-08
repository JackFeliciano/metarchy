# Metarchy

Metarchy runs a market where private companies offer the services a government normally supplies. A member takes those services and keeps living where they are.

## What a market holds

- **service**: one of thirteen functions, from civil registration and currency to courts, education, transport and social security.
- **provider**: a private company, or the network itself.
- **listing**: one provider offering one service, at a price in cents and on a delivery term.
- **member**: a person who joined the society.
- **order**: one member taking one listing on one day.

## What the market refuses

A listing for an unknown provider or an unknown service. A listing with no price. A member who joins twice. An order for a listing that is not bookable. A second order from the same member on the same listing.

## What it answers

What one member took, what one provider earned, the cheapest listing for a service, and which services still have no provider.

## Running it

    php showroom/index.php

The demonstration runs on PHP 8 with no dependencies. Two providers of the same service sit side by side, so a member compares them on price and on delivery term.

## Tests

    php tests/run.php

## Licence

Mozilla Public License 2.0. See LICENSE.
