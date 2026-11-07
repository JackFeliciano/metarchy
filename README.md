# Metarchy

## What the project is

Metarchy is a PHP 8.3 programme that provides the governance layer over the InFolk projects. It models houses, seats, motions, votes and permanent records.

A house holds an odd number of seats. A seat can open a motion that names its house, opening day and voting window in days. Each seat answers once with yes, no or abstain.

A motion closes when every seat has answered or its voting window expires. Quorum requires answers from at least two thirds of the seats, rounded up. A motion carries when yes votes outnumber no votes and the answers reach quorum. Abstentions count towards quorum but towards neither side.

When a motion closes, the programme writes a record of its outcome. The record never changes.

The demo contains 1 house with 5 seats, 3 motions and 2 records. One record shows a motion that carried. Another shows a motion that lost on quorum. The third motion remains open.

The programme uses plain PHP 8.3. It requires no Composer and no packages.

## How to run it

Run the test suite from the repository root:

```bash
php tests/run.php
```


Start the demo with PHP's built-in server:

```bash
php -S 127.0.0.1:8106 -t showroom showroom/index.php
```


The application uses / as its default URL prefix. If a proxy passes a different prefix, set METARCHY_BASE to that value.

## What the routes answer

The demo exposes 2 routes.

`/` renders the house, its open motions and its records.

`/health` answers with the seat count, motion count and record count.

The home route shows the current governance state. Visitors can see the house structure, motions awaiting closure and records of completed motions.

The health route provides the counts for the house and its governance activity.

## How the layout is arranged

The repository separates governance logic from request handling, presentation, tests and deployment files.

`src/Home/` holds `Seat`, `Vote`, `Motion`, `House` and `Sample`.

`src/App/` holds the router, the front controller and the HTML.

`showroom/` holds the entry file and the stylesheet.

`tests/` holds the test suite.

`deploy/` holds the systemd unit and the nginx location block.

`SPEC.md` describes the governance model.

The specification states the project vocabulary, the rules of a house, what a record holds and 3 things that version 1 leaves open.

## The state of the suite

The repository includes a hand-written test runner with 16 checks. Run all checks with:

```bash
php tests/run.php
```

The runner requires no package installation or external test framework. It provides a direct way to check the programme from the command line.

## Licence

The repository uses the Mozilla Public License 2.0 (MPL 2.0). You may use, modify and distribute the code. You must publish changes to files covered by the licence.

