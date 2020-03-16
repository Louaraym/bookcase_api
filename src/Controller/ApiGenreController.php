<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiGenreController extends AbstractController
{
    /**
     * @Route("/api/genre", name="api_genre")
     */
    public function index()
    {
        return $this->render('api_genre/index.html.twig', [
            'controller_name' => 'ApiGenreController',
        ]);
    }
}
