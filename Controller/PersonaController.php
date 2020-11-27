<?php

namespace Viduc\CasBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormView;
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
     * @throws Exception
     */
    public function ajouterUnPersona(Request $request)
    {
        $form = $this->creerLeFormulairePersona($request);
        return $this->render(
            '@Cas/persona/ajouter.html.twig',
            array(
                'form' => $form,
                'photos' => $this->personaPhoto->recupererLaListeDesPhotos(),
                'photoPersona' => "/bundles/cas/images/personas/anonyme.png"
            )
        );
    }

    /**
     * Modifie un persone
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws Exception
     */
    public function modifierUnPersona(request $request, int $id)
    {
        $form = $this->creerLeFormulairePersona($request, $id);
        return $this->render(
            '@Cas/persona/ajouter.html.twig',
            array(
                'form' => $form,
                'photos' => $this->personaPhoto->recupererLaListeDesPhotos(),
                'photoPersona' => $form->vars['value']->getUrlPhoto()
            )
        );
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return FormView | Response
     * @throws Exception
     */
    public function creerLeFormulairePersona(
        Request $request,
        int $id = null
    ): FormView {
        $persona = new Persona();
        if ($id) {
            $persona = $this->personaManipulation->recupererUnPersona($id);
        }
        $form = $this->createForm(PersonaType::class, $persona);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $this->personaPhoto->enregistrerPhoto(
                $form->get('photo')->getData(),
                $form->getData()->getUsername(),
                $form->getData()->getUrlPhoto()
            );
            $persona = $form->getData();
            $persona->setUrlPhoto($photo);
            if ($id) {
                $this->personaManipulation->modifierUnPersonaAuFichierJson(
                    $persona
                );
            } else {
                $this->personaManipulation->ajouterUnPersonaAuFichierJson(
                    $persona
                );
            }
            return $this->render('@Cas/persona/index.html.twig', [
                'personas' => $this->personaManipulation->recupererLesPersonas(),
            ]);
        }

        return $form->createView();
    }

    /**
     * permet de se connecter avec un persona
     * @param $id
     * @return RedirectResponse
     * @test testSeConnecter()
     */
    public function seConnecter($id): ?RedirectResponse
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
    public function restaurerEnTantQue(): RedirectResponse
    {
        $this->session->set('enTantQue.restaurer', true);
        /* @codeCoverageIgnoreStart */
        return $this->redirect('persona');
        /* @codeCoverageIgnoreEnd */
    }




}
