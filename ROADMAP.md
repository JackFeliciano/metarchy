# Metarchy roadmap

Metarchy records what a house decided, in which seat and on which day. Version 1
holds one house in one process, and the items below are what a second house and a
second reader need.

## Open in the model

- Seat weights are fixed at one and a weighted seat is out of scope. A house that
  needs them waits for a new draft of SPEC.md.
- The quorum is two thirds, rounded up. A house cannot set its own quorum.
- A seat may hold more than one house and the model does not stop it.
- A seat that resigns keeps its place until the house fills it, and no motion can
  close on a vacant seat.

## Owed to a reader

- The page shows the open motions and the records, and it holds no filters. A
  reader cannot ask for one house, one seat or one month.
- A record names the tally and the result, and it keeps no list of who answered
  what. A reader cannot audit a single seat.
- Nothing signs a record, so a reader cannot tell a record this house wrote from
  one copied in from elsewhere.

## Owed to a house

- The window is counted in whole days from the opening day. No time zone is named,
  so two houses can disagree about the day a motion closes.
- A motion cannot be withdrawn once it opens.
- A house holds its seats in memory, and a restart forgets them.

## Checks owed

- A motion that closes on the day its window runs out.
- A house with one seat and a house with nine.
- A record written twice for the same motion.
