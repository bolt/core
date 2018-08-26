<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/admin/{name}")
     */
     public function index($name = "Gekke Henkie") {
        return $this->render('index.html.twig', [
            'name' => $name,
        ]);
     }
}