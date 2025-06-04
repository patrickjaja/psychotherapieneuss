<?php
namespace App\Controller;

class PracticeController extends BaseController
{
    public function index()
    {
        return $this->render('pages/practice.html.twig', [
            'current_page' => 'practice'
        ]);
    }
}