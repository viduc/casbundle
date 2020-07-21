<?php

namespace Viduc\CasBundle\Tests\Unit\Security;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Viduc\CasBundle\Exception\eTq_NonAutoriseException;
use Viduc\CasBundle\Exception\eTq_UtilisateurNonTrouveException;
use Viduc\CasBundle\Security\CasUser;
use Viduc\CasBundle\Security\UserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class UgaUserProviderTest extends TestCase
{
    protected $session;
    protected $security;
    protected $provider;

    protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->security = $this->createMock(Security::class);
        $this->provider = new UserProvider($this->session, $this->security);
    }

    public function creerUser($username,$roles)
    {
        $user = new CasUser();
        $user->setUsername($username);
        $user->setRoles($roles);

        return $user;
    }

    public function testConnecterEnTantQue()
    {
        $this->security->method('getUser')->willReturn(
            $this->creerUser('test',['ROLE_ENTANTQUE'])
        );

        /* test si la variable de session 'enTantQue.seConnecter' est existante
         et non null, utilisateur autorisé à se connecter */
        $this->session->set('enTantQue.seConnecter', 'test');
        $this->assertInstanceOf(
            UserInterface::class,
            $this->provider->connecterEnTantQue()
        );

    }

    public function testEstAutoriseAseConnecterEnTantQue()
    {
        $this->security->method('getUser')->will(
            $this->onConsecutiveCalls(
                $this->creerUser('test',['ROLE_ENTANTQUE']),
                $this->creerUser('test',['ROLE_USER'])
            )
        );
        /* test avec le rôle autorisé */
        $this->assertTrue(
            $this->provider->estAutoriseAseConnecterEnTantQue()
        );
        /* test sans le role autorisé */
        try {
            $this->provider->estAutoriseAseConnecterEnTantQue();
        } catch (eTq_NonAutoriseException $exception) {
            $this->assertInstanceOf(
                eTq_NonAutoriseException::class,
                $exception
            );
        }
    }

    public function testChargerUtilisateurCible()
    {
        /* test avec login présent et object user non présent, login connu */
        $this->session->set('enTantQue.seConnecter', 'test');
        $this->assertInstanceOf(
            CasUser::class,
            $this->provider->chargerUtilisateurCible()
        );

        /* test avec login présent et object user présent et valide */
        $this->session->set('enTantQue.seConnecter', 'test');
        $user = new CasUser();
        $user->setUsername('test');
        $this->session->set('enTantQue.seConnecterUserObject', $user);
        $this->assertInstanceOf(
            UserInterface::class,
            $this->provider->chargerUtilisateurCible()
        );
    }

    public function testChargerUtilisateurCibleException()
    {
        /* test avec le login non présent dans la session */
        try {
            $this->provider->chargerUtilisateurCible();
        } catch (eTq_UtilisateurNonTrouveException $exception) {
            $this->assertInstanceOf(
                eTq_UtilisateurNonTrouveException::class,
                $exception
            );
            $message = 'Le login de l\'utilisateur à s\'approprier n\'est';
            $message .= ' pas présent en session';
            $this->assertEquals(
                $message,
                $exception->getMessage()
            );
        }

        /* test avec login présent et object user non présent, login non connu */
        $this->session->set('enTantQue.seConnecter', 'testphpunit');
        try {
            $this->provider->chargerUtilisateurCible();
        } catch (eTq_UtilisateurNonTrouveException $exception) {
            $this->assertInstanceOf(
                eTq_UtilisateurNonTrouveException::class,
                $exception
            );
            $message = 'L\'utilisateur n\'a pas été trouvé';
            $this->assertEquals(
                $message,
                $exception->getMessage()
            );
        }

        /* test avec login présent et object user non présent, login connu
        * mais méthode chargerUtilisateurParSonLogin ne retourne pas un objet
        * UgaUser
        */
        $this->session->set('enTantQue.seConnecter', 'testphpunitnonobjectuser');
        try {
            $this->provider->chargerUtilisateurCible();
        } catch (eTq_UtilisateurNonTrouveException $exception) {
            $this->assertInstanceOf(
                eTq_UtilisateurNonTrouveException::class,
                $exception
            );
            $message = 'L\'utilisateur cible n\'a pas pu être chargé correctement';
            $this->assertEquals(
                $message,
                $exception->getMessage()
            );
        }
    }

    public function testChargerUtilisateurParSonLogin()
    {
        /* test avec login non connu */
        try {
            $this->provider->chargerUtilisateurParSonLogin('testphpunit');
        } catch (eTq_UtilisateurNonTrouveException $exception) {
            $this->assertInstanceOf(
                eTq_UtilisateurNonTrouveException::class,
                $exception
            );
            $message = 'L\'utilisateur n\'a pas été trouvé';
            $this->assertEquals(
                $message,
                $exception->getMessage()
            );
        }

        /* test avec renvoi d'un string au lieu d'un User
        */
        $this->assertNotInstanceOf(
            CasUser::class,
            $this->provider->chargerUtilisateurParSonLogin('testphpunitnonobjectuser')
        );

        /* test avec un user retourné */
        $this->assertInstanceOf(
            CasUser::class,
            $this->provider->chargerUtilisateurParSonLogin('test')
        );
    }

    public function testEnregistrerLutilisateurReferent()
    {
        try {
            $this->assertNull($this->provider->enregistrerLutilisateurReferent(''));
        } catch (eTq_NonAutoriseException $exception) {
            $this->assertInstanceOf(
                eTq_NonAutoriseException::class,
                $exception
            );
            $message = 'Vous n\'êtes pas autorisé à utiliser cette fonctionnalité';
            $this->assertEquals(
                $message,
                $exception->getMessage()
            );
        }
        $this->assertNull($this->provider->enregistrerLutilisateurReferent('test'));
    }

    public function testRestaurerUtilisateurReferent()
    {
        $this->assertNull($this->provider->restaurerUtilisateurReferent());
        $this->assertFalse(
            $this->session->has('enTantQue.seConnecter')
        );
        $this->assertFalse(
            $this->session->has('enTantQue.seConnecterReferent')
        );
        $this->assertFalse(
            $this->session->has('enTantQue.seConnecterUserObject')
        );
        $this->assertFalse(
            $this->session->has('enTantQue.restaurer')
        );
    }
}
