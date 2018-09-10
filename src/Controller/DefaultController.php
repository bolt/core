<?php

namespace Bolt\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/bolt")
     */
     public function index($name = "Gekke Henkie") {
        return $this->render('bolt/index.html.twig', [
            'name' => $name,
        ]);
     }
}