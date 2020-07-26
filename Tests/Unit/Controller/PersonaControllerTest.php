<?php

namespace Viduc\CasBundle\Tests\Unit\Controller;

use Viduc\CasBundle\Controller\PersonaController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class PersonaControllerTest extends TestCase
{
    protected $session;
    protected $persona;

    protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->persona = new PersonaController($this->session);
    }

}
