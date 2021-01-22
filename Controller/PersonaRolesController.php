<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PersonaRolesController extends AbstractController implements PersonaRolesInterfaceController
{
    private $roles = [];

    /**
     * Récupère les roles des paramètres
     * @param array $hierarchy
     * @return array
     * @test testeRecupererLesRoles()
     */
    final public function recupererLesRoles(array $hierarchy): array
    {
        foreach ($hierarchy as $key => $roles) {
            $this->ajouterUnRole($key);
            foreach ($roles as $role) {
                $this->ajouterUnRole($role);
            }
        }

        return $this->roles;
    }

    /**
     * Ajoute un role au tableau de roles
     * @param String $role
     * @test testAjouterUnRole()
     */
    final public function ajouterUnRole(String $role) : void
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[$role] = $role;
        }
    }

    /**
     * Récupère le tableau de roles
     * @return array
     * @codeCoverageIgnore
     */
    final public function getRoles() : array
    {
        return $this->roles;
    }
}
