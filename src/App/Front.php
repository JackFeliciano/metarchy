<?php

declare(strict_types=1);

namespace Metarchy\App;

use Metarchy\Home\Sample;

/**
 * The front controller. Routes, then renders. The health route answers with counts
 * taken from the sample, so a probe can tell a working process from a live one.
 */
final class Front
{
    public function __construct(private readonly string $base)
    {
    }

    public function run(): void
    {
        $router = new Router($this->base);
        $views = new Views($this->base);
        $sample = new Sample();

        $router->get('/', static fn (): array => [200, [
            'html' => $views->front($sample),
        ]]);
        $router->get('/health', static fn (): array => [200, ['json' => [
            'ok' => true,
            'services' => count(\Metarchy\Home\Service::ALL),
            'listings' => count($sample->market()->listings()),
            'orders' => count($sample->market()->orders()),
            'uncovered' => count($sample->market()->uncovered()),
        ]]]);

        [$status, $body] = $router->handle(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
        );

        http_response_code($status);
        if (isset($body['json'])) {
            header('Content-Type: application/json');
            echo json_encode($body['json'], JSON_UNESCAPED_SLASHES), "\n";

            return;
        }
        header('Content-Type: text/html; charset=utf-8');
        if (isset($body['html'])) {
            echo $body['html'];

            return;
        }
        echo $views->missing();
    }
}
