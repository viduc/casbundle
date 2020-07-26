<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PersonaController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function index()
    {
        return $this->render('@Cas/persona/index.html.twig', [
            'controller_name' => 'PersonaController',
        ]);
    }
}
