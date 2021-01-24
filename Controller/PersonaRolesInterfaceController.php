<?php declare(strict_types=1);
/******************************************************************************/
/*                                  CASBUNDLE                                 */
/*     Auteur: Tristan Fleury - https://github.com/viduc - viduc@mail.fr      */
/*                              Licence: Apache-2.0                           */
/******************************************************************************/

namespace Viduc\CasBundle\Controller;


interface PersonaRolesInterfaceController
{
    /**
     * Récupère les roles des paramètres
     * @param array $hierarchy
     * @return array
     * @test testeRecupererLesRoles()
     */
    public function recupererLesRoles(array $hierarchy) : array;

    /**
     * Ajoute un role au tableau de roles
     * @param String $role
     * @test testAjouterUnRole()
     */
    public function ajouterUnRole(String $role) : void;
}