<?php declare(strict_types=1);
/******************************************************************************/
/*                                  CASBUNDLE                                 */
/*     Auteur: Tristan Fleury - https://github.com/viduc - viduc@mail.fr      */
/*                              Licence: Apache-2.0                           */
/******************************************************************************/

namespace Viduc\CasBundle\Tests\Unit\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Viduc\CasBundle\Exception\eTqNonAutoriseException;
use Viduc\CasBundle\Exception\eTqUtilisateurNonTrouveException;
use Viduc\CasBundle\Security\CasUser;
use Viduc\CasBundle\Security\UserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

define('SE_CONNECTER', 'enTantQue.seConnecter');
define('RESTAURER', 'enTantQue.restaurer');
define('GET_USER', 'getUser');
define('ROLE_ENTANTQUE', 'ROLE_ENTANTQUE');
define('ROLE_USER','ROLE_USER');
class UserProviderTest extends TestCase
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

    public function testLoadUserByUsername()
    {
        $this->session->set(RESTAURER, true);
        self::assertInstanceOf(
            UserInterface::class,
            $this->provider->loadUserByUsername('test')
        );
        $this->session->set(SE_CONNECTER, 'test');
        $this->security->method(GET_USER)->willReturn(
            $this->creerUser('test',[ROLE_ENTANTQUE])
        );
        self::assertInstanceOf(
            UserInterface::class,
            $this->provider->loadUserByUsername('test')
        );
    }

    public function testRefreshUser()
    {
        $user = $this->creerUser('test', [ROLE_USER]);
        self::assertInstanceOf(
            UserInterface::class,
            $this->provider->refreshUser($user)
        );
        try {
            $user = new User('test', 'test');
            $this->provider->refreshUser($user);
        } catch (UnsupportedUserException $exception) {
            self::assertInstanceOf(
                UnsupportedUserException::class,
                $exception
            );
        }
    }

    public function testSupportsClass()
    {
        self::assertFalse($this->provider->supportsClass('test'));
        self::assertTrue($this->provider->supportsClass(
            'Viduc\CasBundle\Security\CasUser'
        ));

    }

    public function testConnecterEnTantQue()
    {
        $this->security->method(GET_USER)->willReturn(
            $this->creerUser('test',[ROLE_ENTANTQUE])
        );

        /* test si la variable de session 'enTantQue.seConnecter' est existante
         et non null, utilisateur autorisé à se connecter */
        $this->session->set(SE_CONNECTER, 'test');
        self::assertInstanceOf(
            UserInterface::class,
            $this->provider->connecterEnTantQue()
        );

    }

    public function testEstAutoriseAseConnecterEnTantQue()
    {
        $this->security->method(GET_USER)->will(
            self::onConsecutiveCalls(
                $this->creerUser('test',[ROLE_ENTANTQUE]),
                $this->creerUser('test',[ROLE_ENTANTQUE]),
                $this->creerUser('test',[ROLE_USER]),
                $this->creerUser('test',[ROLE_USER])
            )
        );
        /* test avec le rôle autorisé */
        self::assertTrue(
            $this->provider->estAutoriseAseConnecterEnTantQue()
        );
        /* test sans le role autorisé */
        try {
            $this->provider->estAutoriseAseConnecterEnTantQue();
        } catch (eTqNonAutoriseException $exception) {
            self::assertInstanceOf(
                eTqNonAutoriseException::class,
                $exception
            );
        }
    }

    public function testChargerUtilisateurCible()
    {
        /* test avec login présent et object user non présent, login connu */
        $this->session->set(SE_CONNECTER, 'test');
        self::assertInstanceOf(
            CasUser::class,
            $this->provider->chargerUtilisateurCible()
        );

        /* test avec login présent et object user présent et valide */
        $this->session->set(SE_CONNECTER, 'test');
        $user = new CasUser();
        $user->setUsername('test');
        $this->session->set('enTantQue.seConnecterUserObject', $user);
        self::assertInstanceOf(
            UserInterface::class,
            $this->provider->chargerUtilisateurCible()
        );
    }

    public function testChargerUtilisateurCibleException()
    {
        /* test avec le login non présent dans la session */
        try {
            $this->provider->chargerUtilisateurCible();
        } catch (eTqUtilisateurNonTrouveException $exception) {
            self::assertInstanceOf(
                eTqUtilisateurNonTrouveException::class,
                $exception
            );
            $message = 'Le login de l\'utilisateur à s\'approprier n\'est';
            $message .= ' pas présent en session';
            self::assertEquals(
                $message,
                $exception->getMessage()
            );
        }

        /* test avec login présent et object user non présent, login non connu */
        $this->session->set(SE_CONNECTER, 'testphpunit');
        try {
            $this->provider->chargerUtilisateurCible();
        } catch (eTqUtilisateurNonTrouveException $exception) {
            self::assertInstanceOf(
                eTqUtilisateurNonTrouveException::class,
                $exception
            );
            $message = 'L\'utilisateur n\'a pas été trouvé';
            self::assertEquals(
                $message,
                $exception->getMessage()
            );
        }

        /* test avec login présent et object user non présent, login connu
        * mais méthode chargerUtilisateurParSonLogin ne retourne pas un objet
        * UgaUser
        */
        $this->session->set(SE_CONNECTER, 'testphpunitnonobjectuser');
        try {
            $this->provider->chargerUtilisateurCible();
        } catch (eTqUtilisateurNonTrouveException $exception) {
            self::assertInstanceOf(
                eTqUtilisateurNonTrouveException::class,
                $exception
            );
            $message = 'L\'utilisateur cible n\'a pas pu être chargé correctement';
            self::assertEquals(
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
        } catch (eTqUtilisateurNonTrouveException $exception) {
            self::assertInstanceOf(
                eTqUtilisateurNonTrouveException::class,
                $exception
            );
            $message = 'L\'utilisateur n\'a pas été trouvé';
            self::assertEquals(
                $message,
                $exception->getMessage()
            );
        }

        /* test avec renvoi d'un string au lieu d'un User
        */
        self::assertNotInstanceOf(
            CasUser::class,
            $this->provider->chargerUtilisateurParSonLogin(
                'testphpunitnonobjectuser'
            )
        );

        /* test avec un user retourné */
        self::assertInstanceOf(
            CasUser::class,
            $this->provider->chargerUtilisateurParSonLogin('test')
        );
    }

    public function testEnregistrerLutilisateurReferent()
    {
        try {
            self::assertNull($this->provider->enregistrerLutilisateurReferent(''));
        } catch (eTqNonAutoriseException $exception) {
            self::assertInstanceOf(
                eTqNonAutoriseException::class,
                $exception
            );
            $message = 'Vous n\'êtes pas autorisé à utiliser cette fonctionnalité';
            self::assertEquals(
                $message,
                $exception->getMessage()
            );
        }
        self::assertNull($this->provider->enregistrerLutilisateurReferent('test'));
    }

    public function testRestaurerUtilisateurReferent()
    {
        self::assertNull($this->provider->restaurerUtilisateurReferent());
        self::assertFalse(
            $this->session->has(SE_CONNECTER)
        );
        self::assertFalse(
            $this->session->has('enTantQue.seConnecterReferent')
        );
        self::assertFalse(
            $this->session->has('enTantQue.seConnecterUserObject')
        );
        self::assertFalse(
            $this->session->has(RESTAURER)
        );
    }
}
