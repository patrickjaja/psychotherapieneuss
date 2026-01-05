<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class SitemapController extends BaseController
{
    public function sitemap(): Response
    {
        $baseUrl = 'https://www.psychotherapieneuss.de';
        $templateDir = __DIR__ . '/../../templates/pages/';

        // Define all pages with their properties
        $pages = [
            ['url' => '/', 'template' => 'home.html.twig', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => '/leistungen', 'template' => 'leistungen.html.twig', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => '/zur-person', 'template' => 'zur-person.html.twig', 'priority' => '0.9', 'changefreq' => 'yearly'],
            ['url' => '/psychotherapie', 'template' => 'psychotherapie.html.twig', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/leistungen/kognitive-verhaltenstherapie', 'template' => 'kognitive-verhaltenstherapie.html.twig', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/leistungen/coaching', 'template' => 'coaching.html.twig', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/leistungen/online-therapie', 'template' => 'online-therapie.html.twig', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/praxis', 'template' => 'praxis.html.twig', 'priority' => '0.7', 'changefreq' => 'yearly'],
            ['url' => '/kostenuebernahme', 'template' => 'kostenuebernahme.html.twig', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/kontakt', 'template' => 'kontakt.html.twig', 'priority' => '0.6', 'changefreq' => 'yearly'],
            ['url' => '/impressum', 'template' => 'impressum.html.twig', 'priority' => '0.1', 'changefreq' => 'yearly'],
            ['url' => '/datenschutz', 'template' => 'datenschutz.html.twig', 'priority' => '0.1', 'changefreq' => 'yearly'],
        ];

        // Start XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Add each page to sitemap
        foreach ($pages as $page) {
            $templatePath = $templateDir . $page['template'];
            $lastmod = file_exists($templatePath)
                ? date('Y-m-d', filemtime($templatePath))
                : date('Y-m-d');

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($baseUrl . $page['url']) . "</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>{$page['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$page['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return new Response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8'
        ]);
    }
}
