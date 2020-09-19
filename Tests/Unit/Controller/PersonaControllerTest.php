<?php

namespace Viduc\CasBundle\Tests\Unit\Controller;

use Symfony\Component\HttpKernel\Kernel;
use Viduc\CasBundle\Controller\PersonaController;
use PHPUnit\Framework\TestCase;

class PersonaControllerTest extends TestCase
{
    //protected $session;
    protected $persona;
    protected $kernel;
    private $ressource;

    protected function setUp(): void
    {
        //$this->session = new Session(new MockArraySessionStorage());
        $this->kernel = $this->createMock(Kernel::class);
        $this->ressource = './Tests/Unit/Ressources';
        $this->kernel->method('getProjectDir')->willReturn($this->ressource);
        $this->persona = new PersonaController($this->kernel);
        unlink($this->ressource.'/public/bundles/cas/personas/personas.json');
    }

    public function testCreerLeFichierPersonaSiInexistant()
    {
        $this->assertNull(
            $this->persona->creerLeFichierPersonaSiInexistant()
        );
    }

    public function testRecupererLesPersonas()
    {
        fopen(
            $this->ressource.'/public/bundles/cas/personas/personas.json',
            'wb'
        );

        $this->assertSame(
            count($this->persona->recupererLesPersonas()), 0
        );
        unlink($this->ressource.'/public/bundles/cas/personas/personas.json');
        $this->persona->creerLeFichierPersonaSiInexistant();
        $this->assertTrue(
            count($this->persona->recupererLesPersonas()) >= 2
        );
    }
}
