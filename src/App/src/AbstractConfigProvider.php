<?php

declare(strict_types=1);

namespace App;

use Mezzio\Template\TemplateRendererInterface;

class AbstractConfigProvider
{
    protected function getSimplePageConfig(): array
    {
        return [
            TemplateRendererInterface::class,
        ];
    }
}
