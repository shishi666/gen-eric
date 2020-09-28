<?php

declare(strict_types=1);

namespace App\Plates\Extensions;

interface NavigationInterface
{
    public const ITEM = 'item';

    public const DROPDOWN = 'dropdown';

    public const TITLE_MENU_PATTERN = /** @lang text */
        '<a href="%1$s"><i class="%2$s"></i>%3$s</a>';

    public const LANG_ITEM_MENU_PATTERN = /** @lang text */
        '<a class="lang-item" href="%s">
            <span class="flag-icon flag-icon-%s"></span>
        </a>';

    public const ICON_PATTERN = /** @lang text */
        '<i class="%1$s mx-1"></i>';

    public const ITEM_MENU_PATTERN = /** @lang text */
        '<li id="%1$s" class="nav-item px-2">%2$s</li>';

    public const ITEM_PATTERN = /** @lang text */
        '<a href="%1$s" class="nav-link d-block %2$s" %3$s>%4$s%5$s</a>';

    // @codingStandardsIgnoreStart
    public const DROPDOWN_MENU_PATTERN  = /** @lang text */
        '<li class="nav-item dropdown %2$s px-3">
            <a class="nav-link dropdown-toggle" href="#" id="%1$s" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">%3$s</a>
            <div class="dropdown-menu bg-success" aria-labelledby="navbarDropdown">
                %4$s
            </div>
        </li>';
    // @codingStandardsIgnoreEnd

    public const DROPDOWN_ITEM_PATTERN = /** @lang text */
        '<a class="dropdown-item %1$s" href="%2$s">%3$s%4$s</a>';
}
