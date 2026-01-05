<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

abstract class BaseController
{
    protected Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    protected function render(string $template, array $parameters = []): Response
    {
        $content = $this->twig->render($template, $parameters);
        $response = new Response($content);

        // Set proper cache headers for SEO
        // Allow Google and browsers to cache pages for 1 hour
        $response->setPublic();
        $response->setMaxAge(3600); // 1 hour
        $response->headers->addCacheControlDirective('must-revalidate');

        return $response;
    }
}