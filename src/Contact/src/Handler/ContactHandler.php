<?php

declare(strict_types=1);

namespace Contact\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ContactHandler implements RequestHandlerInterface
{
    private TemplateRendererInterface $template;

    public function __construct(
        TemplateRendererInterface $template
    ) {
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = [];
        return new HtmlResponse(
            $this->template->render(
                $request->getAttribute('template'),
                $data
            )
        );
    }
}
