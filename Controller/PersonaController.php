<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class PersonaController extends AbstractController
{
    private $kernel;
    private $filesystem;

    public function __construct(
        KernelInterface $kernel
    ) {
        $this->kernel = $kernel;
        $this->filesystem = new Filesystem();
        $this->creerLeFichierPersonaSiInexistant();
    }

    public function index()
    {
        return $this->render('@Cas/persona/index.html.twig', [
            'personas' => $this->recupererLesPersonas(),
        ]);
    }

    /**
     * Créer le fichier json des personas si il n'existe pas
     * @test testCreerLeFichierPersonaSiInexistant()
     */
    public function creerLeFichierPersonaSiInexistant()
    {
        if (!$this->filesystem->exists(
            $this->kernel->getProjectDir()
            .'/public/file/personas.json')
        ) {
            $this->filesystem->copy(
                $this->kernel->getProjectDir()
                    .'/public/bundles/cas/personas/personas_base.json',
                $this->kernel->getProjectDir()
                    .'/public/file/personas.json'
            );
        }
    }

    /**
     * Récupère la liste des personas
     * @return array - la liste d'objet json de persona
     * @test testRecupererLesPersonas()
     */
    public function recupererLesPersonas(): array
    {
        $liste = [];
        $listePersonasJson = file_get_contents(
            $this->kernel->getProjectDir()
            .'/public/file/personas.json'
        );
        $liste = json_decode($listePersonasJson, false);
        if (!isset($liste->personas)) {
            return [];
        }

        return $liste->personas;
    }
}
