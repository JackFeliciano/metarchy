<?php

declare(strict_types=1);

namespace Metarchy\Home;

/** One answer from one seat. */
final class Vote
{
    public const ANSWERS = ['yes', 'no', 'abstain'];

    public function __construct(
        public readonly string $seat,
        public readonly string $answer,
    ) {
        if (!in_array($answer, self::ANSWERS, true)) {
            throw new \RuntimeException('a vote answers yes, no or abstain');
        }
    }
}
