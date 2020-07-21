<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Viduc\CasBundle\Security\CasUser;

class CasController extends AbstractController
{

    public function __construct(SessionInterface $session)
    {
        $user1 = new CasUser();
        $user1->setUsername('test1');
        $user1->setRoles(['ROLE_USER']);
        $user2 = new CasUser();
        $user2->setUsername('test2');
        $user2->setRoles(['ROLE_USER']);
        $users[] = $user1;
        $users[] = $user2;
        $session->set('enTantQue.users', $users);
    }

    public function index()
    {
        return $this->render('@Cas/index.html.twig', [
            'controller_name' => 'cas',
        ]);
    }
}
