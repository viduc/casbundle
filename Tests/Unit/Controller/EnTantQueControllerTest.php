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
        self::assertCount(
            0,
            $this->enTantQue->genererLeTableauDesUtilisateurs()
        );
        $this->session->set(
            'enTantQue.users',
            [$this->creerUser('test', ['ROLE_USER'])]
        );
        self::assertCount(
            1,
            $this->enTantQue->genererLeTableauDesUtilisateurs()
        );
    }

    public function testRecupererLeTableauDesUtilisateursEnSession()
    {
        self::assertEmpty(
            $this->enTantQue->recupererLeTableauDesUtilisateursEnSession()
        );
        $this->session->set(
            'enTantQue.users',
            [$this->creerUser('test', ['ROLE_USER'])]
        );
        self::assertIsArray(
            $this->enTantQue->recupererLeTableauDesUtilisateursEnSession()
        );
        self::assertCount(
            1,
            $this->enTantQue->recupererLeTableauDesUtilisateursEnSession()
        );
        $this->session->set(
            'enTantQue.users',
            [$this->creerUser('test', ['ROLE_USER']), 'test']
        );
        self::assertIsArray(
            $this->enTantQue->recupererLeTableauDesUtilisateursEnSession()
        );
        self::assertCount(
            1,
            $this->enTantQue->recupererLeTableauDesUtilisateursEnSession()
        );
    }
}
