<?php

declare(strict_types=1);

namespace App;

use App\Delegator\LoggingErrorListenerDelegator;
use App\Factory\LoggerFactory;
use App\Handler\AboutMeHandler;
use App\Handler\CareHandler;
use App\Handler\GalleryHandler;
use App\Handler\HomePageHandler;
use App\Plates\Extensions\BasePathExtension;
use App\Plates\Extensions\NavigationExtension;
use App\Plates\Extensions\NavigationInterface;
use App\Plates\PlatesEngineDelegator;
use Blast\BaseUrl\BasePathHelper;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stratigility\Middleware\ErrorHandler;
use League\Plates\Engine;
use Mezzio\Application;
use Mezzio\Plates\PlatesEngineFactory;
use Mezzio\Template\TemplateRendererInterface;
use Phly\ConfigFactory\ConfigFactory;
use Psr\Log\LoggerInterface;

/**
 * The configuration provider for the App module
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
                HomePageHandler::class => ConfigAbstractFactory::class,
                CareHandler::class     => ConfigAbstractFactory::class,
                GalleryHandler::class  => ConfigAbstractFactory::class,
                AboutMeHandler::class  => ConfigAbstractFactory::class,

                // Plates
                Engine::class              => PlatesEngineFactory::class,
                NavigationExtension::class => ConfigAbstractFactory::class,
                BasePathExtension::class   => ConfigAbstractFactory::class,

                // Logger
                LoggerInterface::class => LoggerFactory::class,

                // Config
                'config-menu' => ConfigFactory::class,
                'config-care' => ConfigFactory::class,
            ],
            'delegators' => [
                Engine::class       => [
                    PlatesEngineDelegator::class,
                ],
                ErrorHandler::class => [
                    LoggingErrorListenerDelegator::class,
                ],
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
                'app'      => [__DIR__ . '/../templates/app'],
                'error'    => [__DIR__ . '/../templates/error'],
                'layout'   => [__DIR__ . '/../templates/layout'],
                'partials' => [__DIR__ . '/../templates/partials'],
            ],
        ];
    }

    public function registerRoutes(Application $app): void
    {
        $app->get(
            '/',
            [HomePageHandler::class],
            'home'
        )->setOptions(
            [
                'defaults' => [
                    'template' => 'app::home-page',
                ],
            ]
        );

        $app->get(
            '/care/{type:all|reiki|massage|huile}',
            [CareHandler::class],
            'care'
        )->setOptions(
            [
                'defaults' => [
                    'template' => 'app::care',
                ],
            ]
        );

        $app->get(
            '/gallery',
            [GalleryHandler::class],
            'gallery'
        )->setOptions(
            [
                'defaults' => [
                    'template' => 'app::gallery',
                ],
            ]
        );

        $app->get(
            '/about-me',
            [GalleryHandler::class],
            'about-me'
        )->setOptions(
            [
                'defaults' => [
                    'template' => 'app::about-me',
                ],
            ]
        );
    }

    public function getMenuConfig(): array
    {
        return [
            'care'     => [
                'title'      => 'Soins proposés',
                'visible'    => true,
                'icon-class' => '',
                'type'       => NavigationInterface::DROPDOWN,
                'position'   => 1,
                'items'      => [
                    [
                        'title'        => 'Tous les soins',
                        'visible'      => true,
                        'icon-class'   => '',
                        'route-or-url' => 'care',
                        'route-params' => ['type' => 'all'],
                        'query-params' => [],
                        'type'         => NavigationInterface::ITEM,
                        'position'     => 1,
                    ],
                    [
                        'title'        => 'Les soins Reiki',
                        'visible'      => true,
                        'icon-class'   => '',
                        'route-or-url' => 'care',
                        'route-params' => ['type' => 'reiki'],
                        'query-params' => [],
                        'type'         => NavigationInterface::ITEM,
                        'position'     => 2,
                    ],
                    [
                        'title'        => 'Les massages',
                        'visible'      => true,
                        'icon-class'   => '',
                        'route-or-url' => 'care',
                        'route-params' => ['type' => 'massage'],
                        'query-params' => [],
                        'type'         => NavigationInterface::ITEM,
                        'position'     => 3,
                    ],
                    [
                        'title'        => 'Les préparation de flacons',
                        'visible'      => true,
                        'icon-class'   => '',
                        'route-or-url' => 'care',
                        'route-params' => ['type' => 'huile'],
                        'query-params' => [],
                        'type'         => NavigationInterface::ITEM,
                        'position'     => 4,
                    ],
                ],
            ],
            'rdv'      => [
                'title'        => 'Prendre rendez-vous',
                'visible'      => true,
                'icon-class'   => '',
                'route-or-url' => 'https://www.facebook.com/generic57',
                'route-params' => [],
                'query-params' => [],
                'type'         => NavigationInterface::ITEM,
                'position'     => 2,
            ],
            'gallery'  => [
                'title'        => 'Gallery photos',
                'visible'      => true,
                'icon-class'   => '',
                'route-or-url' => 'gallery',
                'route-params' => [],
                'query-params' => [],
                'type'         => NavigationInterface::ITEM,
                'position'     => 4,
            ],
            'about-me' => [
                'title'        => 'A propos de moi',
                'visible'      => true,
                'icon-class'   => '',
                'route-or-url' => 'about-me',
                'route-params' => [],
                'query-params' => [],
                'type'         => NavigationInterface::ITEM,
                'position'     => 5,
            ],
        ];
    }

    public function getConfigAbstractFactory(): array
    {
        return [
            // Handler
            HomePageHandler::class => $this->getSimplePageConfig(),
            CareHandler::class     => [
                'config-care',
                TemplateRendererInterface::class,
            ],
            GalleryHandler::class  => $this->getSimplePageConfig(),
            AboutMeHandler::class  => $this->getSimplePageConfig(),

            // Plates
            NavigationExtension::class => [
                'config-menu',
            ],
            BasePathExtension::class   => [
                BasePathHelper::class,
            ],
        ];
    }
}
