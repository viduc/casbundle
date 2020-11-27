<?php

namespace Viduc\CasBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Viduc\CasBundle\Entity\Persona;
use Viduc\CasBundle\Form\PersonaType;

interface PersonaManipulationInterfaceController
{
    /** --------------------> CREATION <--------------------**/
    /**
     * Créer le fichier json des personas si il n'existe pas
     * @test testCreerLeFichierPersonaSiInexistant()
     */
    public function creerLeFichierPersonaSiInexistant();

    /**
     * Enregsitre la liste des personas dans le fichier json
     * @param array $liste
     */
    public function enregistrerLaListeDesPersonasDansLeFichierJson(array $liste);

    /** --------------------> LECTURE <--------------------**/
    public function lireLeFicherDesPersonas();

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
     * @throws Exception
     */
    public function recupererUnPersona($id);

    public function genererIdPersona();
    /** --------------------> AJOUT <--------------------**/

    /**
     * Ajoute un persona dans le fichier json
     * @param $persona
     */
    public function ajouterUnPersonaAuFichierJson($persona);

    /** --------------------> MODIFICATION <--------------------**/
    /**
     * Modifie un persona dans le fichier json
     * @param Persona $persona
     */
    public function modifierUnPersonaAuFichierJson(Persona $persona);

    /** --------------------> SUPPRESSION <--------------------**/
    /**
     * Supprime un persona dans le fichier json
     * @param Persona $persona
     */
    public function supprimerUnPersonaDuFichierJson(Persona $persona);










}
