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

class PersonaController extends AbstractController
{
    private $session;
    protected $personaManipulation;
    protected $personaPhoto;

    public function __construct(
        SessionInterface $session,
        KernelInterface $kernel
    ) {
        $this->personaManipulation = new PersonaManipulationController($kernel);
        $this->personaPhoto = new PersonaPhotoController($kernel);
        $this->session = $session;
    }

    public function index()
    {
        return $this->render('@Cas/persona/index.html.twig', [
            'personas' => $this->personaManipulation->recupererLesPersonas(),
        ]);
    }

    /**
     * Ajoute un persona à la liste présente
     * @param Request $request
     * @return Response
     */
    public function ajouterUnPersona(Request $request)
    {
        $persona = new Persona();
        $form = $this->createForm(PersonaType::class, $persona);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $this->personaPhoto->enregistrerPhoto(
                $form->get('photo')->getData(),
                $form->getData()->getUsername(),
                $form->getData()->getUrlPhoto()
            );
            $this->personaManipulation->ajouterUnPersonaAuFichierJson(
                $form->getData(),
                $photo
            );
            return $this->render('@Cas/persona/index.html.twig', [
                'personas' => $this->personaManipulation->recupererLesPersonas(),
            ]);
        }

        return $this->render(
            '@Cas/persona/ajouter.html.twig',
            array(
                'form' => $form->createView(),
                'photos' => $this->personaPhoto->recupererLaListeDesPhotos(),
                'photoPersona' => "/bundles/cas/images/personas/anonyme.png"
            )
        );
    }

    /**
     * permet de se connecter avec un persona
     * @param $id
     * @return RedirectResponse
     * @test testSeConnecter()
     */
    public function seConnecter($id)
    {
        try {
            $persona = $this->personaManipulation->recupererUnPersona($id);
            $this->session->set('enTantQue.seConnecter', $persona->getUsername());
            /* @codeCoverageIgnoreStart */
            return $this->redirect('persona');
            /* @codeCoverageIgnoreEnd */
        } catch (Exception $e) {
            /* @codeCoverageIgnoreStart */
            $this->addFlash(
                'error',
                'Impossible de se connecter avec ce persona'
            );
            /* @codeCoverageIgnoreStart */
        }
    }

    /**
     * Restaure la session d'origine
     * @return RedirectResponse
     * @test testRestaurerEnTantQue()
     */
    public function restaurerEnTantQue()
    {
        $this->session->set('enTantQue.restaurer', true);
        /* @codeCoverageIgnoreStart */
        return $this->redirect('persona');
        /* @codeCoverageIgnoreEnd */
    }




}
