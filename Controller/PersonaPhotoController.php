<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

/* @codeCoverageIgnoreStart */
define("PERSONA_DOSSIER", '/public/images/personas');
/* @codeCoverageIgnoreEnd */
class PersonaPhotoController extends AbstractController implements PersonaPhotoInterfaceController
{
    private $kernel;
    private $filesystem;

    public function __construct(KernelInterface $kernel)
    {
        $this->filesystem = new Filesystem();
        $this->kernel = $kernel;
        $this->creerLeDossierDesPersonaSiInexistant();
    }

    /** --------------------> CREATION <--------------------**/
    /**
    /**
     * Créer le dossier personas si il n'existe pas
     * @codeCoverageIgnore
     */
    final public function creerLeDossierDesPersonaSiInexistant() : void
    {
        if (!$this->filesystem->exists(
            $this->kernel->getProjectDir() . PERSONA_DOSSIER)
        ) {
            $this->filesystem->mkdir(
                $this->kernel->getProjectDir() . PERSONA_DOSSIER
            );
        }
    }

    /** --------------------> LECTURE <--------------------**/
    /**
     * récupère la listes des photos enregistrées dans le dossier personas
     * @return array
     * @test testRecupererLaListeDesPhotos()
     */
    final public function recupererLaListeDesPhotos() : Array
    {
        $finder = new Finder();
        $dossierHomme = '/public/bundles/cas/images/personas/hommes';
        $dossierFemme = '/public/bundles/cas/images/personas/femmes';
        $dossierUpload = '/public/images/personas';
        $finder->in(
            [
                $this->kernel->getProjectDir() . $dossierHomme,
                $this->kernel->getProjectDir() . $dossierFemme,
                $this->kernel->getProjectDir() . $dossierUpload,
            ]
        )->files();
        $photos = [];
        foreach ($finder as $file) {
            $image = str_replace(
                $this->kernel->getProjectDir() . '/public',
                '',
                $file->getPathname()
            );
            $photos[$file->getFilename()] = $image;
        }

        return $photos;
    }
    /** --------------------> AJOUT <--------------------**/
    /**
     * Enregistre la photo dans le dossier personas
     * Si un fichier est présent il sera enregistrer puis son url renvoyée
     * Sinon si une url existe elle sera renvoyée
     * Sinon l'url par défaut sera renvoyée
     * @param String $username
     * @param String $urlPhoto
     * @param File|null $file
     * @return String - le path du fichier, '', si aucun
     * @codeCoverageIgnore
     */
    final public function enregistrerPhoto(
        String $username,
        String $urlPhoto,
        File $file = null
    ) :String {
        $retour = '/images/personas/nc.jpeg';
        if ($file && file_exists($file)) {
            $dossier = '/public/images/personas';
            $name = $username . '.' .$file->guessExtension();
            $file->move(
                $this->kernel->getProjectDir() . $dossier,
                $name
            );
            $dossier = '/images/personas';
            $retour = $dossier . '/' . $name;
        } elseif ($urlPhoto !== '') {
            $retour = $urlPhoto;
        }

        return $retour;
    }
    /** --------------------> MODIFICATION <--------------------**/
}
