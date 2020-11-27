<?php

namespace Viduc\CasBundle\Tests\Unit\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Kernel;
use Viduc\CasBundle\Controller\PersonaController;
use PHPUnit\Framework\TestCase;
use Viduc\CasBundle\Controller\PersonaManipulationController;
use Viduc\CasBundle\Entity\Persona;
use Exception;

class PersonaManipulationControllerTest extends TestCase
{
    protected $personamanipulation;
    protected $kernel;
    protected $session;
    private $ressource;

    protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->kernel = $this->createMock(Kernel::class);
        $dir = __DIR__;
        $this->ressource = str_replace("Controller", 'Ressources', $dir);
        $this->kernel->method('getProjectDir')->willReturn($this->ressource);
        $this->personamanipulation = new PersonaManipulationController(
            $this->kernel,
            $this->session
        );
        if (file_exists(
            $this->ressource.'/public/file/personas.json'
        )) {
            unlink($this->ressource.'/public/file/personas.json');
        }
    }

    /** --------------------> CREATION <--------------------**/
    public function testCreerLeFichierPersonaSiInexistant()
    {
        self::assertNull(
            $this->personamanipulation->creerLeFichierPersonaSiInexistant()
        );
    }

    public function testEnregistrerLaListeDesPersonasDansLeFichierJson()
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        $liste[] = $this->genererUnPersona();
        $this->personamanipulation->enregistrerLaListeDesPersonasDansLeFichierJson(
            $liste
        );
        self::assertCount(
            1,
            $this->personamanipulation->recupererLesPersonas()
        );
    }

    /** --------------------> LECTURE <--------------------**/
    public function testLireLeFicherDesPersonas()
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertIsString(
            $this->personamanipulation->lireLeFicherDesPersonas()
        );
    }

    public function testRecupererLesPersonas()
    {
        fopen(
            $this->ressource.'/public/file/personas.json',
            'wb'
        );

        self::assertCount(
            0,
            $this->personamanipulation->recupererLesPersonas()
        );
        unlink($this->ressource.'/public/file/personas.json');
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertTrue(
            count($this->personamanipulation->recupererLesPersonas()) >= 2
        );
    }

    public function testRecupererUnPersona()
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        $persona = $this->genererUnPersona();
        self::assertEquals(
            $this->personamanipulation->recupererUnPersona(1),
            $persona
        );

        try {
            $this->personamanipulation->recupererUnPersona(111);
        } catch (Exception $exception) {
            $message = 'Aucun persona trouvé';
            self::assertEquals($exception->getMessage(), $message);
        }
    }

    public function testGenererIdPersona()
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertEquals(
            3,
            $this->personamanipulation->genererIdPersona()
        );
    }

    /** --------------------> AJOUT <--------------------**/
    public function testAjouterUnPersonaAuFichierJson()
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertCount(
            2,
            $this->personamanipulation->recupererLesPersonas()
        );
        $this->personamanipulation->ajouterUnPersonaAuFichierJson(
            $this->genererUnPersona()
        );
        self::assertCount(
            3,
            $this->personamanipulation->recupererLesPersonas()
        );
    }

    /** --------------------> MODIFICATION <--------------------**/
    public function testModifierUnPersonaAuFichierJson()
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertCount(
            2,
            $this->personamanipulation->recupererLesPersonas()
        );
        $this->personamanipulation->modifierUnPersonaAuFichierJson(
            $this->genererUnPersona()
        );
        self::assertCount(
            2,
            $this->personamanipulation->recupererLesPersonas()
        );
    }

    /** --------------------> SUPPRESSION <--------------------**/
    public function testSupprimerUnPersonaDuFichierJson()
    {
        $this->personamanipulation->creerLeFichierPersonaSiInexistant();
        self::assertCount(
            2,
            $this->personamanipulation->recupererLesPersonas()
        );
        $this->personamanipulation->supprimerUnPersonaDuFichierJson(
            $this->genererUnPersona()
        );
        self::assertCount(
            1,
            $this->personamanipulation->recupererLesPersonas()
        );

    }

    /** --------------------> Méthodes utiles au test <--------------------**/
    private function genererUnPersona()
    {
        $persona = new persona();
        $persona->setId(1);
        $persona->setUsername('username1');
        $persona->setNom('le nom');
        $persona->setPrenom('le prenom');
        $persona->setAge(32);
        $persona->setLieu('le lieu');
        $persona->setAisanceNumerique(5);
        $persona->setExpertiseDomaine(2);
        $persona->setFrequenceUsage(3);
        $persona->setMetier('le métier');
        $persona->setCitation('la citation');
        $persona->setHistoire('le lieu');
        $persona->setButs('les buts');
        $persona->setPersonnalite('personalité');
        $persona->setUrlPhoto("l'url de la photo");
        $persona->setRoles('roles');
        $persona->setIsActive(true);

        return $persona;
    }
}