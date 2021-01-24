<?php declare(strict_types=1);
/******************************************************************************/
/*                                  CASBUNDLE                                 */
/*     Auteur: Tristan Fleury - https://github.com/viduc - viduc@mail.fr      */
/*                              Licence: Apache-2.0                           */
/******************************************************************************/

namespace Viduc\CasBundle\Controller;

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
     * @return void
     * @test testEnregistrerLaListeDesPersonasDansLeFichierJson()
     */
    public function enregistrerLaListeDesPersonasDansLeFichierJson(
        array $liste
    ) : void;

    /** --------------------> LECTURE <--------------------**/
    /**
     * Lit le fichier des personas
     * @test testLireLeFicherDesPersonas()
     * @return false|string
     * @test testLireLeFicherDesPersonas()
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
     * @param string $id
     * @return Persona - objet Persona
     * @test testRecupererUnPersona()
     * @throws PersonaException
     */
    public function recupererUnPersona(string $id) : Persona;

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
     * @test testModifierUnPersonaAuFichierJson()
     */
    public function modifierUnPersonaAuFichierJson(Persona $persona) : void;

    /** --------------------> SUPPRESSION <--------------------**/
    /**
     * Supprime un persona dans le fichier json
     * @param Persona $persona
     * @test testSupprimerUnPersonaDuFichierJson
     */
    public function supprimerUnPersonaDuFichierJson(Persona $persona) : void;










}
