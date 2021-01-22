<?php

namespace Viduc\CasBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Viduc\CasBundle\Entity\Persona;
use Viduc\CasBundle\Exception\PersonaException;
use Viduc\CasBundle\Form\PersonaType;

class PersonaController extends AbstractController
{
    private $session;
    protected $personaManipulation;
    protected $personaPhoto;
    protected $personaRoles;

    /**
     * PersonaController constructor.
     * @param SessionInterface $session
     * @param KernelInterface $kernel
     * @codeCoverageIgnore
     */
    public function __construct(
        SessionInterface $session,
        KernelInterface $kernel
    ) {
        $this->personaManipulation = new PersonaManipulationController($kernel);
        $this->personaPhoto = new PersonaPhotoController($kernel);
        $this->personaRoles = new PersonaRolesController();
        $this->session = $session;
    }

    /**
     * Permet de passer le controller PersonaManipulation
     * @param PersonaManipulationInterfaceController $controller
     * @codeCoverageIgnore
     */
    final public function setPersonManipulation(
        PersonaManipulationInterfaceController $controller
    ) : void {
        $this->personaManipulation = $controller;
    }

    /**
     * Vue principale
     * @return Response
     * @codeCoverageIgnore
     */
    final public function index() : Response
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
     * @codeCoverageIgnore
     */
    final public function ajouterUnPersona(Request $request) : Response
    {
        $form = $this->creerLeFormulairePersona($request);
        if ($form->isSubmitted()) {
            return $this->redirectToRoute('personaIndex');
        } else {
            return $this->render(
                '@Cas/persona/ajouter.html.twig',
                array(
                    'form' => $form->createView(),
                    'photos' => $this->personaPhoto->recupererLaListeDesPhotos(),
                    'photoPersona' => "/bundles/cas/images/personas/anonyme.png"
                )
            );
        }
    }

    /**
     * Modifie un persona
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws Exception
     * @codeCoverageIgnore
     */
    final public function modifierUnPersona(request $request, int $id) : Response
    {
        $form = $this->creerLeFormulairePersona($request, $id);
        return $this->render(
            '@Cas/persona/ajouter.html.twig',
            array(
                'form' => $form->createView(),
                'photos' => $this->personaPhoto->recupererLaListeDesPhotos(),
                'photoPersona' => $form->getData()->getUrlPhoto()
            )
        );
    }

    /**
     * Supprime un persona
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     * @codeCoverageIgnore
     */
    final public function supprimerUnPersona(int $id) : RedirectResponse
    {
        $this->personaManipulation->supprimerUnPersonaDuFichierJson(
            $this->personaManipulation->recupererUnPersona($id)
        );
        $this->addFlash('success', 'Le persona a été supprimé');

        return $this->redirectToRoute('personaIndex');
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return FormInterface
     * @throws Exception
     * @codeCoverageIgnore
     */
    final public function creerLeFormulairePersona(
        Request $request,
        int $id = null
    ) : FormInterface {
        $persona = new Persona();
        if ($id) {
            $persona = $this->personaManipulation->recupererUnPersona($id);
        }
        $form = $this->createForm(PersonaType::class, $persona, array(
            'rolesListe' => $this->personaRoles->recupererLesRoles(
                $this->getParameter('security.role_hierarchy.roles')
            )
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $this->personaPhoto->enregistrerPhoto(
                $form->getData()->getUsername(),
                $form->getData()->getUrlPhoto(),
                $form->get('photo')->getData()
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
        }

        return $form;
    }

    /**
     * permet de se connecter avec un persona
     * @param $id
     * @return RedirectResponse
     * @codeCoverageIgnore
     */
    final public function seConnecter($id): ?RedirectResponse
    {
        try {
            $persona = $this->personaManipulation->recupererUnPersona((int)$id);
            $this->session->set('enTantQue.seConnecter', $persona->getUsername());
            return $this->redirectToRoute('personaIndex');
        } catch (PersonaException $e) {
            $this->addFlash(
                'error',
                'Impossible de se connecter avec ce persona'
            );
        }
    }

    /**
     * Restaure la session d'origine
     * @return RedirectResponse
     * test testRestaurerEnTantQue()
     */
    final public function restaurerEnTantQue(): RedirectResponse
    {
        $this->session->set('enTantQue.restaurer', true);
        /* @codeCoverageIgnoreStart */
        return $this->redirectToRoute('personaIndex');
        /* @codeCoverageIgnoreEnd */
    }
}
