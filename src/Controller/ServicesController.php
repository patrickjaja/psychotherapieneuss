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
}