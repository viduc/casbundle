jQuery(document).ready(function() {
    $("#cas_bundle_persona_type_urlPhoto").val($("#photoPersona").attr("src"));
    $(document).on("click", "#upload", function() {
        $("#cas_bundle_persona_type_photo").click();
    });
    $(document).on("click", "[id^=selectPhoto]", function() {
        selectionnerPhotoCorousel(this.id);
    });
    $(document).on("click", "#ajouterBut", function() {
        ajouterUnBut();
    });
    $(document).on("click", "#viderBut", function() {
        $("#cas_bundle_persona_type_buts").val('');
    });
    $(document).on("click", "#ajouterPersonalite", function() {
        ajouterUnePersonalite();
    });
    $(document).on("click", "#viderPersonalite", function() {
        $("#cas_bundle_persona_type_personnalite").val('');
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

/**
 * Ajout un but à la liste des buts dans le input suvit d'un ;
 */
function ajouterUnBut()
{
    let but = $("#ajoutBut").val();
    if (but !== "") {
        let buts = $("#cas_bundle_persona_type_buts").val();
        if (buts !== "") {
            buts += (";")
        }
        buts += but
        $("#cas_bundle_persona_type_buts").val(buts);
        $("#ajoutBut").val('');
    }
}

/**
 * Ajout une personalité à la liste des personalités dans le input suvit d'un ;
 */
function ajouterUnePersonalite()
{
    let personalite = $("#ajoutPersonalite").val();
    if (personalite !== "") {
        let personalites = $("#cas_bundle_persona_type_personnalite").val();
        if (personalites !== "") {
            personalites += (";")
        }
        personalites += personalite
        $("#cas_bundle_persona_type_personnalite").val(personalites);
        $("#ajoutPersonalite").val('');
    }
}