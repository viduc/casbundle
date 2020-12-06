<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

define("USERS", 'enTantQue.users'); // @codeCoverageIgnore

class EnTantQueController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @codeCoverageIgnore
     */
    final public function connecterEnTantQue(Request $request) : RedirectResponse
    {
        $form = $this->createFormBuilder()
             ->add(
                 'users',
                 ChoiceType::class,
                 ['choices'  => $this->genererLeTableauDesUtilisateurs()]
             )
             ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->getData()['users'];
            $this->session->set('enTantQue.seConnecter', $username);
            return $this->redirect('cas');
        }

        return $this->render(
            '@Cas/enTantQue.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * génère le tableau qui servira au select du en tant que
     * @return array
     * @test testGenererLeTableauPourLeChoixDesUtilisateurs()
     */
    final public function genererLeTableauDesUtilisateurs() : array
    {
        $choices = [];
        foreach ($this->recupererLeTableauDesUtilisateursEnSession() as $user) {
            $username = $user->getUsername();
            $choices[$username] = $username;
        }

        return $choices;
    }

    /**
     * Récupère le tableau des utilisateurs en session qui a été généré pour la
     * fonctionnalité enTantQue
     * @return array
     * @test testRecupererLeTableauDesUtilisateursEnSession()
     */
    final public function recupererLeTableauDesUtilisateursEnSession() : array
    {
        if ($this->session->has(USERS)
            && is_array($this->session->get(USERS))) {
            return array_filter(
                $this->session->get(USERS),
                static function ($objet) {
                    return $objet instanceof UserInterface;
                }
            );
        }

        return [];
    }

    /**
     * Restaure la session d'origine
     * @return RedirectResponse
     * @test testRestaurerEnTantQue()
     */
    final public function restaurerEnTantQue() : RedirectResponse
    {
        $this->session->set('enTantQue.restaurer', true);

        /* @codeCoverageIgnoreStart */
        return $this->redirect('cas');
        /* @codeCoverageIgnoreEnd */
    }
}
