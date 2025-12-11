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
}