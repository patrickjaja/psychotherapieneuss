<?php
namespace App\Controller;

class HomeController extends BaseController
{
    public function index()
    {
        return $this->render('pages/home.html.twig', [
            'current_page' => 'home'
        ]);
    }
}