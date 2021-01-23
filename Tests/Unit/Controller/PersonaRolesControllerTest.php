<?php

namespace Viduc\CasBundle\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use Viduc\CasBundle\Controller\PersonaRolesController;

class PersonaRolesControllerTest extends TestCase
{
    protected $personaRoles;

    final protected function setUp(): void
    {
        $this->personaRoles = new PersonaRolesController();
    }

    final public function testeRecupererLesRoles(): void
    {
        $role[] = 'ROLE_USER';
        $hierarchy['ROLE_ADMIN'] = $role;
        $roles = ["ROLE_ADMIN" => "ROLE_ADMIN", "ROLE_USER" => "ROLE_USER"];
        $this->assertEquals(
            $roles,
            $this->personaRoles->recupererLesRoles($hierarchy)
        );
    }

    final public function testAjouterUnRole() : void
    {
        $this->personaRoles->ajouterUnRole('test');
        $this->assertContains('test', $this->personaRoles->getRoles());
    }
}