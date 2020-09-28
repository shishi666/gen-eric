<?php

declare(strict_types=1);

namespace App\Listener;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class LoggingErrorListener
{
    /** @var string */
    private const LOG_STRING = '{status} [{method}] {uri}: {error}';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(
        Throwable $error,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): void {
        $this->logger->error(self::LOG_STRING, [
            'status' => $response->getStatusCode(),
            'method' => $request->getMethod(),
            'uri'    => (string) $request->getUri(),
            'error'  => $error->getMessage(),
        ]);
    }
}
