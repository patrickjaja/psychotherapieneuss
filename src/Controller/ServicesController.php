<?php
namespace App\Controller;

class ServicesController extends BaseController
{
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

    public function diagnostik()
    {
        return $this->render('pages/services/diagnostik.html.twig', [
            'current_page' => 'diagnostik'
        ]);
    }

    public function diagnostikBdi()
    {
        return $this->render('pages/services/diagnostik/bdi-ii.html.twig', [
            'current_page' => 'diagnostik'
        ]);
    }

    public function diagnostikScid()
    {
        return $this->render('pages/services/diagnostik/scid-5-spq.html.twig', [
            'current_page' => 'diagnostik'
        ]);
    }

    public function diagnostikScl()
    {
        return $this->render('pages/services/diagnostik/scl-90.html.twig', [
            'current_page' => 'diagnostik'
        ]);
    }

    public function diagnostikCtq()
    {
        return $this->render('pages/services/diagnostik/ctq.html.twig', [
            'current_page' => 'diagnostik'
        ]);
    }
}