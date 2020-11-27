<?php

namespace Viduc\CasBundle\Controller;

use Exception;
use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Viduc\CasBundle\Entity\Persona;
use Viduc\CasBundle\Form\PersonaType;

class PersonaManipulationController extends AbstractController implements PersonaManipulationInterfaceController
{
    private $kernel;
    private $filesystem;
    private $serializer;
    protected $personaPhoto;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->filesystem = new Filesystem();
        $this->creerLeFichierPersonaSiInexistant();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->personaPhoto = new PersonaPhotoController($kernel);
    }

    /** --------------------> CREATION <--------------------**/
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
     * Enregsitre la liste des personas dans le fichier json
     * @param array $liste
     */
    public function enregistrerLaListeDesPersonasDansLeFichierJson(array $liste)
    {
        $liste = $this->serializer->serialize($liste, 'json');
        $liste = '{"personas":' . $liste . '}';
        $file = $this->kernel->getProjectDir().'/public/file/personas.json';
        if (file_exists($file)) {
            unlink($this->kernel->getProjectDir().'/public/file/personas.json');
        }
        file_put_contents(
            $this->kernel->getProjectDir().'/public/file/personas.json',
            $liste
        );
    }

    /** --------------------> LECTURE <--------------------**/
    /**
     * Lit le fichier des personas
     * @test testLireLeFicherDesPersonas()
     * @return false|string
     */
    public function lireLeFicherDesPersonas()
    {
        return file_get_contents(
            $this->kernel->getProjectDir()
            .'/public/file/personas.json'
        );
    }

    /**
     * Récupère la liste des personas
     * @return array - la liste d'objets de persona
     * @test testRecupererLesPersonas()
     */
    public function recupererLesPersonas(): array
    {
        $personas = [];
        $listePersonasJson = $this->lireLeFicherDesPersonas();
        $liste = json_decode($listePersonasJson, false);
        if (isset($liste->personas)) {
            foreach ($liste->personas as $persona) {
                $personas[] = $this->serializer->deserialize(
                    $this->serializer->serialize($persona, 'json'),
                    Persona::class,
                    'json'
                );
            }
        }

        return $personas;
    }

    /**
     * Récupère un persona par son id
     * @param $id
     * @return Persona - objet Persona
     * @test testRecupererUnPersona()
     * @throws Exception
     */
    public function recupererUnPersona($id)
    {
        $personas = $this->recupererLesPersonas();
        foreach ($personas as $persona) {
            if ($persona->getId() === (int)$id) {
                return $persona;
            }
        }
        throw new Exception('Aucun persona trouvé');
    }

    /**
     * récupère un id non utilisé dans le fichier des personas
     * @return int
     * @test testGenererIdPersona()
     */
    public function genererIdPersona()
    {
        $personas = $this->recupererLesPersonas();
        $ids = [];
        foreach ($personas as $persona) {
            $ids[] = $persona->getId();
        }
        $i = 1;
        while (true) {
            if (!in_array($i, $ids, true)) {
                return $i;
            }
            $i++;
        }
    }

    /** --------------------> AJOUT <--------------------**/
    /**
     * Ajoute un persona dans le fichier json
     * @param $persona
     * @test testAjouterUnPersonaAuFichierJson()
     */
    public function ajouterUnPersonaAuFichierJson($persona)
    {
        $persona->setId($this->genererIdPersona());
        $persona->setButs('');//TODO à revoir
        $persona->setPersonnalite('');//TODO à revoir
        $liste = $this->recupererLesPersonas();
        $liste[] = $persona;
        $this->enregistrerLaListeDesPersonasDansLeFichierJson($liste);
    }

    /** --------------------> MODIFICATION <--------------------**/
    /**
     * Modifie un persona dans le fichier json
     * @param Persona $persona
     */
    public function modifierUnPersonaAuFichierJson($persona)
    {
        $liste = [];
        foreach ($this->recupererLesPersonas() as $element ) {
            if ($element->getId() !== $persona->getId()) {
                $liste[] = $element;
            } else {
                $liste[] = $persona;
            }
        }
        $this->enregistrerLaListeDesPersonasDansLeFichierJson($liste);
    }

    /** --------------------> SUPPRESSION <--------------------**/
    /**
     * Supprime un persona dans le fichier json
     * @param Persona $persona
     */
    public function supprimerUnPersonaDuFichierJson(Persona $persona)
    {
        $liste = [];
        foreach ($this->recupererLesPersonas() as $element ) {
            if ($element->getId() !== $persona->getId()) {
                $liste[] = $element;
            }
        }
        $this->enregistrerLaListeDesPersonasDansLeFichierJson($liste);
    }

}
