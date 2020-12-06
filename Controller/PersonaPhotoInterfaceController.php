<?php

namespace Viduc\CasBundle\Controller;

use Symfony\Component\HttpFoundation\File\File;

interface PersonaPhotoInterfaceController
{
    /** --------------------> CREATION <--------------------**/

    /** --------------------> LECTURE <--------------------**/
    public function recupererLaListeDesPhotos();

    /** --------------------> AJOUT <--------------------*
     * @param $file
     * @param $username
     * @param $urlPhoto
     */
    public function enregistrerPhoto(
        String $username,
        String $urlPhoto,
        File $file
    );
    /** --------------------> MODIFICATION <--------------------**/












}
