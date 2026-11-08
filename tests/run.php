<?php

declare(strict_types=1);

require __DIR__ . '/../autoload.php';

use Metarchy\Tests\Runner;

$runner = new Runner();
foreach (glob(__DIR__ . '/*Test.php') ?: [] as $file) {
    /** @var array<string, \Closure> $tests */
    $tests = require $file;
    foreach ($tests as $name => $test) {
        $runner->check(basename($file) . ': ' . $name, $test);
    }
}

exit($runner->summary());
