<?php

declare(strict_types=1);

namespace App\Plates;

use League\Plates\Engine;
use Psr\Container\ContainerInterface;

class PlatesEngineDelegator
{
    public function __invoke(ContainerInterface $container, string $name, callable $factory): Engine
    {
        /** @var Engine $engine */
        $engine = $factory();

        $engine->loadExtension($container->get(Extensions\NavigationExtension::class));

        $engine->loadExtension($container->get(Extensions\BasePathExtension::class));

        $this->addTitreVariable($engine);

        return $engine;
    }

    private function addTitreVariable(Engine $engine)
    {
        $engine->addData(['title' => 'Gen\'eric']);
    }
}
