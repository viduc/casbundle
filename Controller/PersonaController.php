<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PersonaController extends AbstractController
{
    public function index()
    {
        return $this->render('@Cas/persona/index.html.twig', [
            'controller_name' => 'PersonaController',
        ]);
    }
}
