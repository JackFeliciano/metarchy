# Metarchy

Metarchy is the governance layer over the InFolk projects. Each project keeps its
own books. Metarchy keeps the record of who decided what, in which seat, on which
day, and what the decision changed.

## Words

- **house**: one body that governs, held in this document as an assembly.
- **seat**: one voting position inside a house. A seat carries a name and a weight
  of one. A house holds an odd number of seats so a tie cannot happen.
- **motion**: one proposal put to a house. It carries a title, the house it went
  to, the day it opened, and the number of days it stays open.
- **vote**: one seat answering a motion with `yes`, `no` or `abstain`.
- **record**: one closed motion together with its result and the day it closed.

## What a house does

A house opens a motion, and the seats answer. A motion closes when every seat has
answered or when its window runs out. A motion carries when the yes votes outnumber
the no votes and the seats that answered reach the quorum. A seat that abstains
counts towards the quorum and towards neither side.

A house refuses a motion that names no house, a motion whose window has run out,
and a second vote from the same seat on the same motion.

## What a record holds

A record names the motion, the house, the day it opened, the day it closed, the
tally of yes, no and abstain, and the result. A record never changes once written.
A correction is a new motion.

## The seat weights

Every seat carries weight one in version 1. Weighted seats are out of scope, and a
motion that needs them is a new draft of this document.

## Open in version 1

- What the quorum is. The demo runs a quorum of two thirds of the seats.
- Whether a seat may hold more than one house. The demo says yes.
- What happens when a house has no open motion and a seat resigns. The demo keeps
  the seat until the house fills it.
