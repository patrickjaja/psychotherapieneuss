<?php
namespace App\Controller;

class PsychotherapyController extends BaseController
{
    public function index()
    {
        return $this->render('pages/psychotherapy.html.twig', [
            'current_page' => 'psychotherapy'
        ]);
    }
}