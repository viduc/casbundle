<?php

namespace Viduc\CasBundle\Controller;

interface PersonaPhotoInterfaceController
{
    /** --------------------> CREATION <--------------------**/

    /** --------------------> LECTURE <--------------------**/
    public function recupererLaListeDesPhotos();

    /** --------------------> AJOUT <--------------------*
     * @param $file
     * @param $username
     */
    public function enregistrerPhoto($file, $username);
    /** --------------------> MODIFICATION <--------------------**/












}
