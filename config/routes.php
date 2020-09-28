<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    // App routes
    (new App\ConfigProvider())->registerRoutes($app);

    // Contact
    (new Contact\ConfigProvider())->registerRoutes($app);
};
