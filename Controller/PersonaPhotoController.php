<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface;

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
    public function recupererLaListeDesPhotos()
    {

    }
    /** --------------------> AJOUT <--------------------**/
    /**
     * Enregsitrer la photo dans le dossier personas
     * @param $file
     * @param $username
     * @return String - le nom du fichier, '', si aucun
     * @codeCoverageIgnore
     */
    public function enregistrerPhoto($file, $username)
    {
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
        }

        return $retour;
    }
    /** --------------------> MODIFICATION <--------------------**/












}
