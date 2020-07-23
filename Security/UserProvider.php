<?php
namespace Viduc\CasBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Viduc\CasBundle\Exception\eTqNonAutoriseException;
use Viduc\CasBundle\Exception\eTqUtilisateurNonTrouveException;
use Symfony\Component\Security\Core\Security;

define("SECONNECTER", 'enTantQue.seConnecter');
define("SECONNECTER_USEROBJECT", 'enTantQue.seConnecterUserObject');
define("SECONNECTER_REFERENT", 'enTantQue.seConnecterReferent');

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

    public function getSeConnecter()
    {
        return SECONNECTER;
    }

    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @param $username
     * @return UserInterface
     * @test testLoadUserByUsername()
     */
    public function loadUserByUsername($username)
    {
        if ($this->session->has('enTantQue.restaurer')) {
            $this->restaurerUtilisateurReferent();
        }
        if ($this->session->has(SECONNECTER)) {
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
     * @param UserInterface $user
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof CasUser) {
            throw new UnsupportedUserException(
                sprintf('Invalid user class "%s".', get_class($user))
            );
        }

        return $user;
    }

    /**
     * Tells Symfony to use this provider for this User class.
     * @param $class
     * @return bool
     */
    public function supportsClass($class) : bool
    {
        return CasUser::class === get_class($class);
    }


    /**
     * Méthode pour se connecter en tant qu'autre utilisateur
     * @return mixed|UserInterface|CasUser
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
     * @return bool| eTqNonAutoriseException
     * @test testEstAutoriseAseConnecterEnTantQue
     */
    public function estAutoriseAseConnecterEnTantQue()
    {
        if ($this->security->getUser() !== null &&
            in_array('ROLE_ENTANTQUE', $this->security->getUser()->getRoles())) {
            return true;
        }
        $message = 'Vous n\'êtes pas autorisé à utiliser cette fonctionnalité';
        throw new eTqNonAutoriseException($message);
    }

    /**
     * Renvoie l'objet utilisateur ciblé
     * @return mixed|CasUser|eTqUtilisateurNonTrouveException
     * @test testChargerUtilisateurCible()
     * @test testChargerUtilisateurCibleException
     */
    public function chargerUtilisateurCible()
    {
        if (!$this->session->has(SECONNECTER) ||
            $this->session->get(SECONNECTER) === null) {
            $message = 'Le login de l\'utilisateur à s\'approprier n\'est';
            $message .= ' pas présent en session';
            throw new eTqUtilisateurNonTrouveException($message);
        }
        $user = $this->chargerUtilisateurParSonLogin(
            $this->session->get(SECONNECTER)
        );
        if ($this->session->has(SECONNECTER_USEROBJECT) &&
            $this->session->get(SECONNECTER_USEROBJECT) !== null) {
            $user = $this->session->get(SECONNECTER_USEROBJECT);
        }
        if (!$user instanceof UserInterface) {
            $message = 'L\'utilisateur cible n\'a pas pu être chargé';
            $message .= ' correctement';
            throw new eTqUtilisateurNonTrouveException($message);
        }
        $this->session->set(SECONNECTER_USEROBJECT, $user);

        return $user;
    }

    /**
     * Charge un utilisateur via son login
     * Méthode générique, doit être surchargée par le provider de l'application
     * Doit renvoyer un objet de type UserInterface
     * @param $username
     * @return UserInterface|string
     * @test testChargerUtilisateurParSonLogin()
     */
    public function chargerUtilisateurParSonLogin($username)
    {
        if ($username === 'testphpunit') {
            $message = 'L\'utilisateur n\'a pas été trouvé';
            throw new eTqUtilisateurNonTrouveException($message);
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
    public function enregistrerLutilisateurReferent($username) : void
    {
        if (!$this->session->has(SECONNECTER_REFERENT) ||
            $this->session->get(SECONNECTER_REFERENT) === null) {
            if ($username === null || $username === '') {
                $message = 'Vous n\'êtes pas autorisé à utiliser cette';
                $message .= ' fonctionnalité';
                throw new eTqNonAutoriseException($message);
            }
            $this->session->set(SECONNECTER_REFERENT, $username);
        }
    }

    /**
     * Vide la session des variables enTantQue
     * @test testRestaurerUtilisateurReferent()
     */
    public function restaurerUtilisateurReferent() : void
    {
        $this->session->remove(SECONNECTER);
        $this->session->remove(SECONNECTER_REFERENT);
        $this->session->remove(SECONNECTER_USEROBJECT);
        $this->session->remove('enTantQue.restaurer');
    }
}
