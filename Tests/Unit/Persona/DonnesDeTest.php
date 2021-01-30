<?php declare(strict_types=1);
/******************************************************************************/
/*                                  CASBUNDLE                                 */
/*     Auteur: Tristan Fleury - https://github.com/viduc - viduc@mail.fr      */
/*                              Licence: Apache-2.0                           */
/******************************************************************************/

namespace Viduc\CasBundle\Tests\Unit\Persona;

use Viduc\CasBundle\Entity\Persona;

class DonnesDeTest
{
    /**
     * Génère un persona avec un id = 1
     * @return Persona
     */
    public function genererUnPersona()
    {
        $persona = new Persona();
        $persona->setId(1);
        $persona->setUsername('username1');
        $persona->setNom('le nom');
        $persona->setPrenom('le prenom');
        $persona->setAge(32);
        $persona->setLieu('le lieu');
        $persona->setAisanceNumerique(5);
        $persona->setExpertiseDomaine(2);
        $persona->setFrequenceUsage(3);
        $persona->setMetier('le métier');
        $persona->setCitation('la citation');
        $persona->setHistoire('le lieu');
        $persona->setButs('les buts');
        $persona->setPersonnalite('personalité');
        $persona->setUrlPhoto("l'url de la photo");
        $persona->setRoles(['roles']);
        $persona->setIsActive(true);

        return $persona;
    }
}