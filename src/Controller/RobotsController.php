<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class RobotsController extends BaseController
{
    public function robots(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "\n";
        $content .= "Sitemap: https://www.psychotherapieneuss.de/sitemap.xml\n";

        return new Response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8'
        ]);
    }
}
