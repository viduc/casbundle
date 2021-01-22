<?php


namespace Viduc\CasBundle\Controller;


interface PersonaRolesInterfaceController
{
    public function recupererLesRoles(array $hierarchy) : array;

    public function ajouterUnRole(String $role) : void;
}