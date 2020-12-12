<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Viduc\CasBundle\Entity\Persona;
use Viduc\CasBundle\Exception\PersonaException;

/* @codeCoverageIgnoreStart */
define("PERSONA_JSON", '/public/file/personas.json');
/* @codeCoverageIgnoreEnd */
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
    final public function creerLeFichierPersonaSiInexistant() : void
    {
        if (!$this->filesystem->exists(
            $this->kernel->getProjectDir() . PERSONA_JSON)
        ) {
            $this->filesystem->copy(
                $this->kernel->getProjectDir()
                .'/public/bundles/cas/personas/personas_base.json',
                $this->kernel->getProjectDir() . PERSONA_JSON
            );
        }
    }

    /**
     * Enregsitre la liste des personas dans le fichier json
     * @param array $liste
     * @return void
     */
    final public function enregistrerLaListeDesPersonasDansLeFichierJson(
        array $liste
    ) : void {
        $listeSerialize = $this->serializer->serialize($liste, 'json');
        $listeFinale = '{"personas":' . $listeSerialize . '}';
        $file = $this->kernel->getProjectDir() . PERSONA_JSON;
        if (file_exists($file)) {
            unlink($this->kernel->getProjectDir() . PERSONA_JSON);
        }
        file_put_contents(
            $this->kernel->getProjectDir() . PERSONA_JSON,
            $listeFinale
        );
    }

    /** --------------------> LECTURE <--------------------**/
    /**
     * Lit le fichier des personas
     * @test testLireLeFicherDesPersonas()
     * @return false|string
     */
    final public function lireLeFicherDesPersonas() : string
    {
        return file_get_contents(
            $this->kernel->getProjectDir() . PERSONA_JSON
        );
    }

    /**
     * Récupère la liste des personas
     * @return array - la liste d'objets de persona
     * @test testRecupererLesPersonas()
     */
    final public function recupererLesPersonas(): array
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
     * @throws PersonaException
     */
    final public function recupererUnPersona($id)
    {
        $personas = $this->recupererLesPersonas();
        foreach ($personas as $persona) {
            if ($persona->getId() === (int)$id) {
                return $persona;
            }
        }
        throw new PersonaException('Aucun persona trouvé');
    }

    /**
     * récupère un id non utilisé dans le fichier des personas
     * @return int
     * @test testGenererIdPersona()
     */
    final public function genererIdPersona() : int
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
    final public function ajouterUnPersonaAuFichierJson(Persona $persona) : void
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
    final public function modifierUnPersonaAuFichierJson(Persona $persona) : void
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
    final public function supprimerUnPersonaDuFichierJson(Persona $persona): void
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
