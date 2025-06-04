<?php
namespace App\Controller;

class LegalController extends BaseController
{
    public function impressum()
    {
        return $this->render('pages/impressum.html.twig', [
            'current_page' => 'impressum'
        ]);
    }

    public function datenschutz()
    {
        return $this->render('pages/datenschutz.html.twig', [
            'current_page' => 'datenschutz'
        ]);
    }
}