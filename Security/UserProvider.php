<?php

namespace Viduc\CasBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Viduc\CasBundle\Exception\ETQ_NonAutoriseException;
use Viduc\CasBundle\Exception\ETQ_UtilisateurNonTrouveException;
use Symfony\Component\Security\Core\Security;

class UserProvider implements UserProviderInterface
{
    private $session;
    private $security;

    public function __construct(
        SessionInterface $session,
        Security $security
    ) {
        $this->session = $session;
        $this->security = $security;
    }

    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        if ($this->session->has('enTantQue.restaurer')) {
            $this->restaurerUtilisateurReferent();
        }
        if ($this->session->has('enTantQue.seConnecter')) {
            $this->enregistrerLutilisateurReferent($username);
            return $this->connecterEnTantQue();
        }

        return $this->chargerUtilisateurParSonLogin($username);
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @return UserInterface
     * @throws \Exception
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof CasUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $user;
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass($class)
    {
        return CasUser::class === $class;
    }

    /**
     * Upgrades the encoded password of a user, typically for using a better hash algorithm.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        // TODO: when encoded passwords are in use, this method should:
        // 1. persist the new password in the user storage
        // 2. update the $user object with $user->setPassword($newEncodedPassword);
    }

    /**
     * Méthode pour se connecter en tant qu'autre utilisateur
     * @return mixed|UserInterface|UgaUser
     * @test testConnecterEnTantQue()
     */
    public function connecterEnTantQue()
    {
        $this->estAutoriseAseConnecterEnTantQue();
        return $this->chargerUtilisateurCible();
    }

    /**
     * Vérifie si l'utilisateur qui utilise la fonctionnalité enTantQue est
     * autorisé à le faire
     * @return bool| ETQ_NonAutoriseException
     * @test testEstAutoriseAseConnecterEnTantQue
     */
    public function estAutoriseAseConnecterEnTantQue()
    {
        if (in_array('ROLE_ENTANTQUE', $this->security->getUser()->getRoles())) {
            return true;
        }
        $message = 'Vous n\'êtes pas autorisé à utiliser cette fonctionnalité';
        throw new ETQ_NonAutoriseException($message);
    }

    /**
     * Renvoie l'objet utilisateur ciblé
     * @return mixed|UgaUser|ETQ_UtilisateurNonTrouveException
     * @test testChargerUtilisateurCible()
     * @test testChargerUtilisateurCibleException
     */
    public function chargerUtilisateurCible()
    {
        if (!$this->session->has('enTantQue.seConnecter') ||
            $this->session->get('enTantQue.seConnecter') === null) {
            $message = 'Le login de l\'utilisateur à s\'approprier n\'est';
            $message .= ' pas présent en session';
            throw new ETQ_UtilisateurNonTrouveException($message);
        }
        if ($this->session->has('enTantQue.seConnecterUserObject') &&
            $this->session->get('enTantQue.seConnecterUserObject') !== null) {
            $user = $this->session->get('enTantQue.seConnecterUserObject');
        } else {
            $user = $this->chargerUtilisateurParSonLogin(
                $this->session->get('enTantQue.seConnecter')
            );
        }
        if (!$user instanceof UserInterface) {
            $message = 'L\'utilisateur cible n\'a pas pu être chargé correctement';
            throw new ETQ_UtilisateurNonTrouveException($message);
        }
        $this->session->set('enTantQue.seConnecterUserObject', $user);

        return $user;
    }

    /**
     * Charge un utilisateur via son login
     * Méthode générique, doit être surchargée par le provider de l'application
     * Doit renvoyer un objet de type UserInterface
     * @param $username
     * @return UserInterface
     * @test testChargerUtilisateurParSonLogin()
     */
    public function chargerUtilisateurParSonLogin($username)
    {
        if ($username === 'testphpunit') {
            $message = 'L\'utilisateur n\'a pas été trouvé';
            throw new ETQ_UtilisateurNonTrouveException($message);
        }
        if ($username === 'testphpunitnonobjectuser') {
            return 'noobjectuser';
        }
        $user = new CasUser();
        $user->setUsername($username);
        $user->setRoles(['ROLE_USER', 'ROLE_ENTANTQUE']);

        return $user;
    }

    /**
     * Enregistre l'utilisateur référent (celui qui a le droit de se connecter
     * en tant que et qui fait l'action) en session
     * @param string $username
     */
    public function enregistrerLutilisateurReferent($username)
    {
        if (!$this->session->has('enTantQue.seConnecterReferent') ||
            $this->session->get('enTantQue.seConnecterReferent') === null) {
            if ($username === null || $username === '') {
                $message = 'Vous n\'êtes pas autorisé à utiliser cette fonctionnalité';
                throw new ETQ_NonAutoriseException($message);
            }
            $this->session->set('enTantQue.seConnecterReferent', $username);
        }
    }

    /**
     * Vide la session des variables enTantQue
     * @test testRestaurerUtilisateurReferent()
     */
    public function restaurerUtilisateurReferent()
    {
        $this->session->remove('enTantQue.seConnecter');
        $this->session->remove('enTantQue.seConnecterReferent');
        $this->session->remove('enTantQue.seConnecterUserObject');
        $this->session->remove('enTantQue.restaurer');
    }
}
