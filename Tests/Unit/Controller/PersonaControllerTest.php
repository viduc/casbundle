<?php

namespace Viduc\CasBundle\Tests\Unit\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Kernel;
use Viduc\CasBundle\Controller\PersonaController;
use PHPUnit\Framework\TestCase;
use Viduc\CasBundle\Controller\PersonaManipulationInterfaceController;
use Viduc\CasBundle\Tests\Unit\Persona\DonnesDeTest;

class PersonaControllerTest extends TestCase
{
    protected $persona;
    protected $kernel;
    protected $session;
    protected $ressource;
    protected $personaManipulation;
    protected $donneesDeTest;

    final protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->kernel = $this->createMock(Kernel::class);
        $dir = __DIR__;
        $this->ressource = str_replace("Controller", 'Ressources', $dir);
        $this->kernel->method('getProjectDir')->willReturn($this->ressource);
        $this->persona = new PersonaController($this->session, $this->kernel);
        $this->personaManipulation = $this->createMock(
            PersonaManipulationInterfaceController::class
        );
        $this->persona->setPersonManipulation($this->personaManipulation);
        $this->donneesDeTest = new DonnesDeTest();
    }

    final public function testSeConnecter() : void
    {
        $this->personaManipulation->method('recupererUnPersona')->willReturn(
            $this->donneesDeTest->genererUnPersona()
        );
        $this->persona->seConnecter(1);
        self::assertSame(
            'username1',
            $this->session->get('enTantQue.seConnecter')
        );
    }

    final public function testRestaurerEnTantQue() : void
    {
        $this->persona->restaurerEnTantQue();
        self::assertTrue(
            $this->session->get('enTantQue.restaurer')
        );
    }
}