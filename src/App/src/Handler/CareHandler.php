<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_filter;
use function in_array;

class CareHandler implements RequestHandlerInterface
{
    private array $careConfig;

    private TemplateRendererInterface $template;

    public function __construct(
        array $careConfig,
        TemplateRendererInterface $template
    ) {
        $this->careConfig = $careConfig;
        $this->template   = $template;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data         = [];
        $data['care'] = array_filter($this->careConfig, function ($item) use ($request) {
            if ($request->getAttribute('type') === 'all') {
                return true;
            }
            if (in_array($request->getAttribute('type'), $item['tags'])) {
                return true;
            }
        });

        return new HtmlResponse(
            $this->template->render(
                $request->getAttribute('template'),
                $data
            )
        );
    }
}
