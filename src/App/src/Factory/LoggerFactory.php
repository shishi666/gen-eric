<?php

declare(strict_types=1);

namespace App\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use const PHP_EOL;

class LoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $config       = $container->get('config');
        $loggerConfig = $config['logger'];
        $formatter    = new LineFormatter('[%datetime%] %channel%.%level_name%: %message%' . PHP_EOL);

        $handler = new StreamHandler($loggerConfig['file_path'], Logger::INFO);
        $handler->setFormatter($formatter);

        $logger = new Logger('gen-eric', [$handler]);

        $logger->pushProcessor(new PsrLogMessageProcessor());

        return $logger;
    }
}
