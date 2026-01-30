<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServicesController extends BaseController
{
    private function requireDiagnostikAuth(Request $request): ?Response
    {
        $user = $request->headers->get('PHP_AUTH_USER');
        $pass = $request->headers->get('PHP_AUTH_PW');

        if ($user !== 'patient' || $pass !== 'neuss2026') {
            $response = new Response('Zugang verweigert.', 401);
            $response->headers->set('WWW-Authenticate', 'Basic realm="Geschützter Bereich - Zugangsdaten erhalten Sie von Ihrer Therapeutin"');
            return $response;
        }

        return null;
    }

    public function index()
    {
        return $this->render('pages/services.html.twig', [
            'current_page' => 'services'
        ]);
    }

    public function kvt()
    {
        return $this->render('pages/services/kvt.html.twig', [
            'current_page' => 'services'
        ]);
    }

    public function coaching()
    {
        return $this->render('pages/services/coaching.html.twig', [
            'current_page' => 'services'
        ]);
    }

    public function onlineTherapie()
    {
        return $this->render('pages/services/online-therapie.html.twig', [
            'current_page' => 'services'
        ]);
    }

    public function diagnostik(Request $request)
    {
        if ($deny = $this->requireDiagnostikAuth($request)) return $deny;
        return $this->render('pages/services/diagnostik.html.twig', [
            'current_page' => 'diagnostik'
        ]);
    }

    public function diagnostikBdi(Request $request)
    {
        if ($deny = $this->requireDiagnostikAuth($request)) return $deny;
        return $this->render('pages/services/diagnostik/bdi-ii.html.twig', [
            'current_page' => 'diagnostik'
        ]);
    }

    public function diagnostikScid(Request $request)
    {
        if ($deny = $this->requireDiagnostikAuth($request)) return $deny;
        return $this->render('pages/services/diagnostik/scid-5-spq.html.twig', [
            'current_page' => 'diagnostik'
        ]);
    }

    public function diagnostikScl(Request $request)
    {
        if ($deny = $this->requireDiagnostikAuth($request)) return $deny;
        return $this->render('pages/services/diagnostik/scl-90.html.twig', [
            'current_page' => 'diagnostik'
        ]);
    }

    public function diagnostikCtq(Request $request)
    {
        if ($deny = $this->requireDiagnostikAuth($request)) return $deny;
        return $this->render('pages/services/diagnostik/ctq.html.twig', [
            'current_page' => 'diagnostik'
        ]);
    }
}