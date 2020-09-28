<?php

declare(strict_types=1);

namespace Contact;

use App\AbstractConfigProvider;
use App\Plates\Extensions\NavigationInterface;
use Contact\Handler\ContactHandler;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Mezzio\Application;

/**
 * The configuration provider for the Contact module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider extends AbstractConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies'               => $this->getDependencies(),
            'templates'                  => $this->getTemplates(),
            'menu'                       => $this->getMenuConfig(),
            ConfigAbstractFactory::class => $this->getConfigAbstractFactory(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'factories'  => [
                // Handler
                ContactHandler::class => ConfigAbstractFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'contact' => [__DIR__ . '/../templates/'],
            ],
        ];
    }

    public function registerRoutes(Application $app): void
    {
        $app->get(
            '/contact',
            [ContactHandler::class],
            'contact'
        )->setOptions(
            [
                'defaults' => [
                    'template' => 'contact::contact',
                ],
            ]
        );
    }

    public function getMenuConfig(): array
    {
        return [
            'contact' => [
                'title'        => 'Me contacter',
                'visible'      => true,
                'icon-class'   => '',
                'route-or-url' => 'contact',
                'route-params' => [],
                'query-params' => [],
                'type'         => NavigationInterface::ITEM,
                'position'     => 3,
            ],
        ];
    }

    public function getConfigAbstractFactory(): array
    {
        return [
            // Handler
            ContactHandler::class => $this->getSimplePageConfig(),
        ];
    }
}
