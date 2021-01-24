<?php declare(strict_types=1);
/******************************************************************************/
/*                                  CASBUNDLE                                 */
/*     Auteur: Tristan Fleury - https://github.com/viduc - viduc@mail.fr      */
/*                              Licence: Apache-2.0                           */
/******************************************************************************/

namespace Viduc\CasBundle\Tests\Unit\Controller;

use Viduc\CasBundle\Controller\EnTantQueController;
use Viduc\CasBundle\Security\CasUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

define('TABROLES', ['ROLE_USER']);
define('ETQ_USER', 'enTantQue.users');

class EnTantQueControllerTest extends TestCase
{
    protected $session;
    protected $enTantQue;

    final protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->enTantQue = new EnTantQueController($this->session);
    }

    /**
     * @param String $username
     * @param array $roles
     */
    final public function creerUser(String $username, Array $roles) : CasUser
    {
        $user = new CasUser();
        $user->setUsername($username);
        $user->setRoles($roles);

        return $user;
    }

    final public function testGenererLeTableauDesUtilisateurs() : void
    {
        self::assertCount(
            0,
            $this->enTantQue->genererLeTableauDesUtilisateurs()
        );
        $this->session->set(
            ETQ_USER,
            [$this->creerUser('test', TABROLES)]
        );
        self::assertCount(
            1,
            $this->enTantQue->genererLeTableauDesUtilisateurs()
        );
    }

    final public function testRecupererLeTableauDesUtilisateursEnSession() : void
    {
        self::assertEmpty(
            $this->enTantQue->recupererLeTableauDesUtilisateursEnSession()
        );
        $this->session->set(
            ETQ_USER,
            [$this->creerUser('test', TABROLES)]
        );
        self::assertIsArray(
            $this->enTantQue->recupererLeTableauDesUtilisateursEnSession()
        );
        self::assertCount(
            1,
            $this->enTantQue->recupererLeTableauDesUtilisateursEnSession()
        );
        $this->session->set(
            ETQ_USER,
            [$this->creerUser('test', TABROLES), 'test']
        );
        self::assertIsArray(
            $this->enTantQue->recupererLeTableauDesUtilisateursEnSession()
        );
        self::assertCount(
            1,
            $this->enTantQue->recupererLeTableauDesUtilisateursEnSession()
        );
    }

    final public function testRestaurerEnTantQue() : void
    {
        $this->enTantQue->restaurerEnTantQue();
        self::assertTrue(
            $this->session->get('enTantQue.restaurer')
        );
    }
}
