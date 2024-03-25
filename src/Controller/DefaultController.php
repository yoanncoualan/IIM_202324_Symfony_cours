<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'prenom' => 'Yoann',
        ]);
    }

    #[Route('/bonjour/{prenom}', name: 'app_bonjour')]
    public function bonjour(string $prenom): Response
    {
        return $this->render('default/bonjour.html.twig', [
            'prenom' => $prenom
        ]);
    }
}
