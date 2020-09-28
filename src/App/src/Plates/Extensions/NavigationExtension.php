<?php

declare(strict_types=1);

namespace App\Plates\Extensions;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use League\Plates\Template\Template;
use Mezzio\Router\RouteResult;

use function array_filter;
use function array_intersect_assoc;
use function array_map;
use function array_merge;
use function count;
use function implode;
use function preg_match;
use function sprintf;
use function uasort;

class NavigationExtension implements ExtensionInterface, NavigationInterface
{
    public Template $template;

    private array $menuConfig;

    public function __construct(
        array $menuConfig
    ) {
        $this->menuConfig = array_filter($menuConfig, function ($item) {
            return $item['visible'];
        });

        $this->menuConfig = array_map(function ($item) {
            switch ($item['type']) {
                case NavigationInterface::ITEM:
                    return $item;
                case NavigationInterface::DROPDOWN:
                    $item['items'] = array_filter($item['items'], function ($subItem) {
                        return $subItem['visible'];
                    });
                    uasort($item['items'], function ($config, $nextConfig) {
                        return $config['position'] <=> $nextConfig['position'];
                    });
                    return $item;
            }
        }, $this->menuConfig);

        uasort($this->menuConfig, function ($config, $nextConfig) {
            return $config['position'] <=> $nextConfig['position'];
        });
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('renderMenu', [$this, 'renderMenu']);
        $engine->registerFunction('renderTitleMenu', [$this, 'renderTitleMenu']);
    }

    public function renderMenu(): string
    {
        $menu = [];
        foreach ($this->menuConfig as $nameItem => $itemConfig) {
            $menu[] = $this->getHtmlMenu($nameItem, $itemConfig);
        }
        return implode('', $menu);
    }

    private function getHtmlMenu(string $nameItem, array $item): string
    {
        $html = '';
        switch ($item['type']) {
            case self::ITEM:
                $html = sprintf(
                    self::ITEM_MENU_PATTERN,
                    $nameItem,
                    $this->getHtmlItem($item)
                );
                break;
            case self::DROPDOWN:
                $html = sprintf(
                    self::DROPDOWN_MENU_PATTERN,
                    $nameItem,
                    $this->hasActiveItem($item['items'])
                        ? 'active'
                        : '',
                    $item['title'],
                    $this->getDropdownItems($item['items'])
                );
                break;
        }
        return $html;
    }

    private function getHtmlItem(array $item): string
    {
        $html = '';
        switch ($item['type']) {
            case self::ITEM:
                $html = sprintf(
                    self::ITEM_PATTERN,
                    preg_match('/^(http[s]?:\\/\\/)/', $item['route-or-url'])
                        ? $item['route-or-url']
                        : $this->template->url(
                            $item['route-or-url'],
                            $this->getMatchedRouteParams($item['route-params']),
                            $item['query-params']
                        ),
                    $this->getActiveClass($item),
                    preg_match('/^(http[s]?:\\/\\/)/', $item['route-or-url'])
                        ? 'target="_blank"'
                        : '',
                    $this->getIconHtml($item),
                    $item['title']
                );
        }
        return $html;
    }

    private function getActiveClass(array $item): string
    {
        if ($this->template->route()->getMatchedRouteName() === $item['route-or-url']) {
            $nbParams = (int) count(array_intersect_assoc(
                $this->template->route()->getMatchedParams(),
                $item['route-params']
            ));

            if ($nbParams === (int) count($item['route-params'])) {
                return 'active';
            }
        }
        return '';
    }

    private function getIconHtml(array $item): string
    {
        return sprintf(self::ICON_PATTERN, $item['icon-class']) ?? '';
    }

    private function hasActiveItem(array $dropdownItems): bool
    {
        foreach ($dropdownItems as $item) {
            if (! preg_match('/^(http[s]?:\\/\\/)/', $item['route-or-url'])) {
                if ($this->template->route()->getMatchedRouteName() === $item['route-or-url']) {
                    $nbParams = (int) count(array_intersect_assoc(
                        $this->template->route()->getMatchedParams(),
                        $item['route-params']
                    ));

                    if ($nbParams === (int) count($item['route-params'])) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private function getDropdownItems(array $dropdownItems): string
    {
        $dropdownItemsHtml = [];
        foreach ($dropdownItems as $item) {
            if ($item['visible']) {
                $dropdownItemsHtml[] = sprintf(
                    self::DROPDOWN_ITEM_PATTERN,
                    $this->getActiveClass($item),
                    preg_match('/^(http[s]?:\\/\\/)/', $item['route-or-url'])
                        ? $item['route-or-url']
                        : $this->template->url(
                            $item['route-or-url'],
                            $this->getMatchedRouteParams($item['route-params']),
                            $item['query-params']
                        ),
                    $this->getIconHtml($item),
                    $item['title']
                );
            }
        }
        return implode('', $dropdownItemsHtml);
    }

    private function getMatchedRouteParams(array $routeParams = []): array
    {
        /** @var RouteResult $routeResult */
        $routeResult = $this->template->route();
        return array_merge($routeResult->getMatchedParams(), $routeParams);
    }

    public function renderTitleMenu(): string
    {
        return sprintf(
            self::TITLE_MENU_PATTERN,
            $this->template->url('home', $this->getMatchedRouteParams()),
            'fas fa-home mr-2',
            'Menu'
        );
    }
}
