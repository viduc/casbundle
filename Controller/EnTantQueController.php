<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EnTantQueController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function connecterEnTantQue(Request $request)
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
    public function genererLeTableauDesUtilisateurs()
    {
        $choices = [];
        if ($this->session->has('enTantQue.users')
            && is_array($this->session->get('enTantQue.users'))) {
            foreach ($this->session->get('enTantQue.users') as $user) {
                if ($user instanceof UserInterface) {
                    $username = $user->getUsername();
                    $choices[$username] = $username;
                }
            }
        }

        return $choices;
    }

    public function restaurerEnTantQue()
    {
        $this->session->set('enTantQue.restaurer', true);

        return $this->redirect('cas');
    }
}
