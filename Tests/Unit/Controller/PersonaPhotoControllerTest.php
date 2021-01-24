<?php declare(strict_types=1);
/******************************************************************************/
/*                                  CASBUNDLE                                 */
/*     Auteur: Tristan Fleury - https://github.com/viduc - viduc@mail.fr      */
/*                              Licence: Apache-2.0                           */
/******************************************************************************/

namespace Viduc\CasBundle\Tests\Unit\Controller;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Kernel;
use PHPUnit\Framework\TestCase;
use Viduc\CasBundle\Controller\PersonaPhotoController;
use Symfony\Component\Filesystem\Filesystem;

define('PHOTO_TEST', '/tmp/photo-test.jpeg');

class PersonaPhotoControllerTest extends TestCase
{
    protected $personaPhoto;
    protected $kernel;
    private $ressource;
    protected $filesystem;

    final protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->kernel = $this->createMock(Kernel::class);
        $dir = __DIR__;
        $this->ressource = str_replace("Controller", 'Ressources', $dir);
        $this->kernel->method('getProjectDir')->willReturn($this->ressource);
        $this->personaPhoto = new PersonaPhotoController($this->kernel);
    }

    /** --------------------> CREATION <--------------------**/


    /** --------------------> LECTURE <--------------------**/
    final public function testRecupererLaListeDesPhotos() : void
    {
        self::assertTrue(
            count($this->personaPhoto->recupererLaListeDesPhotos()) >= 2
        );
    }

    /** --------------------> AJOUT <--------------------**/
    final public function testEnregistrerPhoto() : void
    {
        if (!file_exists(
            $this->ressource . PHOTO_TEST
        ) && file_exists(
            $this->ressource.'/tmp/phpunit.jpeg'
        )) {
            $this->filesystem->copy(
                $this->ressource.'/tmp/phpunit.jpeg',
                $this->ressource . PHOTO_TEST
            );
        }
        if (file_exists(
            $this->ressource.'/public/images/personas/test.jpeg'
        )) {
            unlink($this->ressource.'/public/images/personas/test.jpeg');
        }

        $photo = new File(
            $this->kernel->getProjectDir() . PHOTO_TEST
        );
        self::assertEquals(
            '/images/personas/test.jpeg',
            $this->personaPhoto->enregistrerPhoto('test', '', $photo)
        );
        self::assertEquals(
            '/images/personas/nc.jpeg',
            $this->personaPhoto->enregistrerPhoto('test', '', null)
        );
        self::assertEquals(
            'test',
            $this->personaPhoto->enregistrerPhoto('test', 'test', null)
        );
    }

    /** --------------------> MODIFICATION <--------------------**/

    /** --------------------> Méthodes utiles au test <--------------------**/

}