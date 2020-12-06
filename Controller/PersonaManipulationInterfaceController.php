<?php

namespace Viduc\CasBundle\Controller;

use Exception;
use Viduc\CasBundle\Entity\Persona;
use Viduc\CasBundle\Exception\PersonaException;

interface PersonaManipulationInterfaceController
{
    /** --------------------> CREATION <--------------------**/
    /**
     * Créer le fichier json des personas si il n'existe pas
     * @test testCreerLeFichierPersonaSiInexistant()
     */
    public function creerLeFichierPersonaSiInexistant() : void;

    /**
     * Enregsitre la liste des personas dans le fichier json
     * @param array $liste
     */
    public function enregistrerLaListeDesPersonasDansLeFichierJson(
        array $liste
    ) : void;

    /** --------------------> LECTURE <--------------------**/
    /**
     * Lit le fichier des personas
     * @test testLireLeFicherDesPersonas()
     * @return false|string
     */
    public function lireLeFicherDesPersonas() : string;

    /**
     * Récupère la liste des personas
     * @return array - la liste d'objets de persona
     * @test testRecupererLesPersonas()
     */
    public function recupererLesPersonas(): array;

    /**
     * Récupère un persona par son id
     * @param $id
     * @return Persona - objet Persona
     * @test testRecupererUnPersona()
     * @throws PersonaException
     */
    public function recupererUnPersona($id) : Persona;

    /**
     * récupère un id non utilisé dans le fichier des personas
     * @return int
     * @test testGenererIdPersona()
     */
    public function genererIdPersona() : int;
    /** --------------------> AJOUT <--------------------**/

    /**
     * Ajoute un persona dans le fichier json
     * @param $persona
     * @test testAjouterUnPersonaAuFichierJson()
     */
    public function ajouterUnPersonaAuFichierJson(Persona $persona) : void;

    /** --------------------> MODIFICATION <--------------------**/
    /**
     * Modifie un persona dans le fichier json
     * @param Persona $persona
     */
    public function modifierUnPersonaAuFichierJson(Persona $persona) : void;

    /** --------------------> SUPPRESSION <--------------------**/
    /**
     * Supprime un persona dans le fichier json
     * @param Persona $persona
     */
    public function supprimerUnPersonaDuFichierJson(Persona $persona) : void;










}
