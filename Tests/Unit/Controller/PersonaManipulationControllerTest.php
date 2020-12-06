<?php

namespace Viduc\CasBundle\Tests\Unit\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Kernel;
use PHPUnit\Framework\TestCase;
use Viduc\CasBundle\Controller\PersonaManipulationController;
use Viduc\CasBundle\Exception\PersonaException;
use Viduc\CasBundle\Tests\Unit\Persona\DonnesDeTest;

class PersonaManipulationControllerTest extends TestCase
{
    protected $personamanipulation;
    protected $kernel;
    protected $session;
    protected $ressource;
    protected $donneesDeTest;

    final protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->kernel = $this->createMock(Kernel::class);
        $dir = __DIR__;
        $this->ressource = str_replace("Controller", 'Ressources', $dir);
        $this->kernel->method('getProjectDir')->willReturn($this->ressource);
        $this->personamanipulation = new PersonaManipulationController(
            $this->kernel
        );
        if (file_exists(
            $this->ressource . PERSONA_JSON
        )) {
            unlink($this->ressource . PERSONA_JSON);
        }
        $this->donneesDeTest = new DonnesDeTest();
    }

    /** --------------------> CREATION <--------------------**/
    final public function testCreerLeFichierPersonaSiInexistant() : void
    {
        self::assertNull(
            $this->personamanipulation->creerLeFichierPersonaSiInexistant()
        );
    }

    final public function testEnregistrerLaListeDesPersonasDansLeFichierJson() : void
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        $liste[] = $this->donneesDeTest->genererUnPersona();
        $this->personamanipulation->enregistrerLaListeDesPersonasDansLeFichierJson(
            $liste
        );
        self::assertCount(
            1,
            $this->personamanipulation->recupererLesPersonas()
        );
    }

    /** --------------------> LECTURE <--------------------**/
    final public function testLireLeFicherDesPersonas() : void
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertIsString(
            $this->personamanipulation->lireLeFicherDesPersonas()
        );
    }

    final public function testRecupererLesPersonas() : void
    {
        fopen(
            $this->ressource . PERSONA_JSON,
            'wb'
        );

        self::assertCount(
            0,
            $this->personamanipulation->recupererLesPersonas()
        );
        unlink($this->ressource . PERSONA_JSON);
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertTrue(
            count($this->personamanipulation->recupererLesPersonas()) >= 2
        );
    }

    final public function testRecupererUnPersona() : void
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        $persona = $this->donneesDeTest->genererUnPersona();
        self::assertEquals(
            $this->personamanipulation->recupererUnPersona(1),
            $persona
        );

        try {
            $this->personamanipulation->recupererUnPersona(111);
        } catch (PersonaException $exception) {
            $message = 'Aucun persona trouvé';
            self::assertEquals($message, $exception->getMessage());
        }
    }

    final public function testGenererIdPersona() : void
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertEquals(
            3,
            $this->personamanipulation->genererIdPersona()
        );
    }

    /** --------------------> AJOUT <--------------------**/
    final public function testAjouterUnPersonaAuFichierJson() : void
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertCount(
            2,
            $this->personamanipulation->recupererLesPersonas()
        );
        $this->personamanipulation->ajouterUnPersonaAuFichierJson(
            $this->donneesDeTest->genererUnPersona()
        );
        self::assertCount(
            3,
            $this->personamanipulation->recupererLesPersonas()
        );
    }

    /** --------------------> MODIFICATION <--------------------**/
    final public function testModifierUnPersonaAuFichierJson() : void
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertCount(
            2,
            $this->personamanipulation->recupererLesPersonas()
        );
        $this->personamanipulation->modifierUnPersonaAuFichierJson(
            $this->donneesDeTest->genererUnPersona()
        );
        self::assertCount(
            2,
            $this->personamanipulation->recupererLesPersonas()
        );
    }

    /** --------------------> SUPPRESSION <--------------------**/
    final public function testSupprimerUnPersonaDuFichierJson() : void
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertCount(
            2,
            $this->personamanipulation->recupererLesPersonas()
        );
        $this->personamanipulation->supprimerUnPersonaDuFichierJson(
            $this->donneesDeTest->genererUnPersona()
        );
        self::assertCount(
            1,
            $this->personamanipulation->recupererLesPersonas()
        );

    }
}