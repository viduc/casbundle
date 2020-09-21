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
        $dir = __DIR__;
        $this->ressource = str_replace("Controller", 'Ressources', $dir);
        //$this->ressource = '../Ressources';
        $this->kernel->method('getProjectDir')->willReturn($this->ressource);
        $this->persona = new PersonaController($this->kernel);
        if (file_exists(
            $this->ressource.'/public/file/personas.json'
        )) {
            unlink($this->ressource.'/public/file/personas.json');
        }
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
            $this->ressource.'/public/file/personas.json',
            'wb'
        );

        $this->assertSame(
            count($this->persona->recupererLesPersonas()), 0
        );
        unlink($this->ressource.'/public/file/personas.json');
        $this->persona->creerLeFichierPersonaSiInexistant();
        $this->assertTrue(
            count($this->persona->recupererLesPersonas()) >= 2
        );
    }
}
