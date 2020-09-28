<?php

declare(strict_types=1);

namespace App\Plates\Extensions;

use Blast\BaseUrl\BasePathHelper;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class BasePathExtension implements ExtensionInterface
{
    private BasePathHelper $basePathHelper;

    public function __construct(BasePathHelper $basePathHelper)
    {
        $this->basePathHelper = $basePathHelper;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('basePath', [$this, 'basePath']);
    }

    public function basePath(string $assetUrl = ''): string
    {
        $helper = $this->basePathHelper;
        return $helper($assetUrl);
    }
}
