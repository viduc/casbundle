<?php

namespace Viduc\CasBundle\Tests\Unit\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Kernel;
use Viduc\CasBundle\Controller\PersonaController;
use PHPUnit\Framework\TestCase;

class PersonaControllerTest extends TestCase
{
    protected $persona;
    protected $kernel;
    protected $session;

    protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->kernel = $this->createMock(Kernel::class);
        $this->persona = new PersonaController($this->kernel, $this->session);

    }

    public function testSeConnecter()
    {
        $this->persona->creerLeFichierPersonaSiInexistant();
        $this->persona->seConnecter(1);
        self::assertSame(
            $this->session->get('enTantQue.seConnecter'), 'username1'
        );
    }

    public function testRestaurerEnTantQue()
    {
        $this->persona->restaurerEnTantQue();
        self::assertTrue(
            $this->session->get('enTantQue.restaurer')
        );
    }
}