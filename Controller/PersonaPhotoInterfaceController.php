<?php declare(strict_types=1);
/******************************************************************************/
/*                                  CASBUNDLE                                 */
/*     Auteur: Tristan Fleury - https://github.com/viduc - viduc@mail.fr      */
/*                              Licence: Apache-2.0                           */
/******************************************************************************/

namespace Viduc\CasBundle\Controller;

use Symfony\Component\HttpFoundation\File\File;

interface PersonaPhotoInterfaceController
{
    /** --------------------> CREATION <--------------------**/
    /**
     * Créer le dossier personas si il n'existe pas
     * @codeCoverageIgnore
     */
    public function creerLeDossierDesPersonaSiInexistant() : void;
    /** --------------------> LECTURE <--------------------**/
    /**
     * récupère la listes des photos enregistrées dans le dossier personas
     * @return array
     * @test testRecupererLaListeDesPhotos()
     */
    public function recupererLaListeDesPhotos() : array;

    /** --------------------> AJOUT <--------------------*
    /**
     * Enregistre la photo dans le dossier personas
     * Si un fichier est présent il sera enregistrer puis son url renvoyée
     * Sinon si une url existe elle sera renvoyée
     * Sinon l'url par défaut sera renvoyée
     * @param String $username
     * @param String $urlPhoto
     * @param File|null $file
     * @return String - le path du fichier, '', si aucun
     * @test testEnregistrerPhoto()
     */
    public function enregistrerPhoto(
        String $username,
        String $urlPhoto,
        File $file
    ) : string;
    /** --------------------> MODIFICATION <--------------------**/












}
