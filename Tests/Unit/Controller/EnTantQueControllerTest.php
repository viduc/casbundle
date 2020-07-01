<?php

namespace Viduc\CasBundle\Tests\Unit\Controller;

use Viduc\CasBundle\Controller\EnTantQueController;
use Viduc\CasBundle\Security\CasUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class EnTantQueControllerTest extends TestCase
{
    protected $session;
    protected $enTantQue;

    protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->enTantQue = new EnTantQueController($this->session);
    }

    public function creerUser($username,$roles)
    {
        $user = new CasUser();
        $user->setUsername($username);
        $user->setRoles($roles);

        return $user;
    }

    public function testGenererLeTableauDesUtilisateurs()
    {
        $this->assertCount(
            0,
            $this->enTantQue->genererLeTableauDesUtilisateurs()
        );
        $this->session->set('enTantQue.users', null);
        $this->assertCount(
            0,
            $this->enTantQue->genererLeTableauDesUtilisateurs()
        );
        $this->session->set(
            'enTantQue.users',
            [$this->creerUser('test', ['ROLE_USER'])]
        );
        $this->assertCount(
            1,
            $this->enTantQue->genererLeTableauDesUtilisateurs()
        );
    }
}
