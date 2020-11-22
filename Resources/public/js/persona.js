jQuery(document).ready(function() {
    $("#cas_bundle_persona_type_urlPhoto").val($("#photoPersona").attr("src"));
    $(document).on("click", "#upload", function() {
        $("#cas_bundle_persona_type_photo").click();
    });
    $(document).on("click", "[id^=selectPhoto]", function() {
        selectionnerPhotoCorousel(this.id);
    });
});

/**
 * Sélectionne une photo
 * Modifie la prévisualisation
 * Modifie l'input urlphoto
 * @param idPhoto
 */
function selectionnerPhotoCorousel(idPhoto)
{
    let pathPhoto = idPhoto.replace('selectPhoto_', '');
    $("#photoPersona").attr("src", pathPhoto);
    $("#cas_bundle_persona_type_urlPhoto").val(pathPhoto);
    $('#modalPhoto').modal('hide');
}