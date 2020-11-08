<?php

namespace Viduc\CasBundle\Tests\Unit\Controller;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Kernel;
use PHPUnit\Framework\TestCase;
use Viduc\CasBundle\Controller\PersonaPhotoController;
use Symfony\Component\Filesystem\Filesystem;

class PersonaPhotoControllerTest extends TestCase
{
    protected $personaPhoto;
    protected $kernel;
    private $ressource;
    protected $filesystem;

    protected function setUp(): void
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


    /** --------------------> AJOUT <--------------------**/
    public function testEnregistrerPhoto()
    {
        if (!file_exists(
            $this->ressource.'/tmp/photo-test.jpeg'
        ) && file_exists(
            $this->ressource.'/tmp/phpunit.jpeg'
        )) {
            $this->filesystem->copy(
                $this->ressource.'/tmp/phpunit.jpeg',
                $this->ressource.'/tmp/photo-test.jpeg'
            );
        }
        if (file_exists(
            $this->ressource.'/public/images/personas/test.jpeg'
        )) {
            unlink($this->ressource.'/public/images/personas/test.jpeg');
        }

        $photo = new File(
            $this->kernel->getProjectDir() . '/tmp/photo-test.jpeg'
        );
        self::assertEquals(
            "/images/personas/test.jpeg",
            $this->personaPhoto->enregistrerPhoto($photo, 'test')
        );
        self::assertEquals(
            "/images/personas/nc.jpeg",
            $this->personaPhoto->enregistrerPhoto(null, 'test')
        );
    }

    /** --------------------> MODIFICATION <--------------------**/

    /** --------------------> Méthodes utiles au test <--------------------**/

}