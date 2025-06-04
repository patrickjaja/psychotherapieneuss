<?php
namespace App\Controller;

class CostsController extends BaseController
{
    public function index()
    {
        return $this->render('pages/costs.html.twig', [
            'current_page' => 'costs'
        ]);
    }
}