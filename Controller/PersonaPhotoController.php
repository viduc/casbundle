<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

class PersonaPhotoController extends AbstractController implements PersonaPhotoInterfaceController
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /** --------------------> CREATION <--------------------**/
    /**


    /** --------------------> LECTURE <--------------------**/
    /**
     * récupère la listes des photos enregistrées dans le dossier personas
     * @return array testRecupererLaListeDesPhotos()
     * @test
     */
    public function recupererLaListeDesPhotos()
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
     * @param $file
     * @param $username
     * @param $urlPhoto
     * @return String - le path du fichier, '', si aucun
     * @codeCoverageIgnore
     */
    public function enregistrerPhoto($file, $username, $urlPhoto)
    {
        $retour = '/images/personas/nc.jpeg';
        if ($file && file_exists($file)) {var_dump('vvvvv');
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
