/**
 * Created by SITRAKA on 21/07/2016.
 */
$(document).ready(function(){
    analytiques = new Array();

    Dropzone.autoDiscover = false;
    myDropzone = new Dropzone('#my-awesome-dropzone', {
        maxFilesize: 200,
        parallelUploads: 10000,
        uploadMultiple: true,
        dictDefaultMessage: "Déposer les images ici ou cliquer",
        addRemoveLinks: true,
        dictRemoveFile: "Enlever",
        thumbnailWidth: 20,
        thumbnailHeight: 20,
        autoProcessQueue: false,
        async: true,
        successmultiple: function (file, response) {
            show_info("Envoi images","Les images sont envoyées avec succès.");
            close_modal();
            //myDropzone.removeAllFiles();
            /*$('#btn-cancel-image-param').addClass('hidden');
            $('#image-dropzone-area').addClass('hidden');
            $('#btn-valider-image-param').removeClass('hidden');*/
        }
    });
    set_height_dropzone();
});

/**
 * change dossier
 */
$(document).on('change','#dossier',function(){
    charger_analytique();
});

/**
 * click sur envoyer
 */
$(document).on('click','#js_upload_click',function(){
    if(!$('.dz-preview').length > 0)
    {
        show_info("PAS D'IMAGE","AJOUTER LES IMAGES", 'warning');
        return;
    }
    show_filtre();
});

/**
 * click sur supprimer images
 */
$(document).on('click','#js_remove_all',function(){
    vider_liste();
});

/**
 * change urgent
 */
$(document).on('change','#js_is_urgent',function(){
    change_urgent();
});

/**
 * change analytique
 */
$(document).on('change','#js_is_analytique',function(){
    change_analytique();
});

$(document).on('change','#js_is_analytique_param',function(){
    change_analytique_param();
});

/**
 * annuler
 */
$(document).on('click','#js_annuler_envoi',function(){
    close_modal();
});

/**
 * Post images to server
 */
$(document).on('click','#js_valider_envoi',function(){
    post_image();
});

/**
 * Function Post Image
 */
function post_image()
{
    if(!parametres_is_valid()) return;
    myDropzone.processQueue();
}

/**
 * validation parametres
 *
 * @returns {boolean}
 */
function parametres_is_valid()
{
    dossier_sel = $('#dossier option:selected').text().trim();
    exercice_sel = parseInt($('#exercice').val().trim());
    commentaire_urgent = '';
    analytique_sel = $('#js_zero_boost').val();

    //dossier
    if(dossier_sel == '')
    {
        show_info('Notice','Choisir le dossier','error');
        return false;
    }
    dossier_sel = $('#dossier').val();
    //exercice
    if(exercice_sel == 0)
    {
        show_info('Notice','Choisir l\'exercice','error');
        return false;
    }
    //urgent
    if($('#js_is_urgent').is(':checked'))
    {
        commentaire_urgent = $('#js_commentaire').val().trim();
        if(commentaire_urgent == '')
        {
            show_info('Notice','COMMENTAIRE obligatoire pour un envoi urgent','error');
            return false;
        }
    }
    //analytique
    if($('#js_is_analytique').is(':checked'))
    {
        analytique_sel = $('#js_analytique').val().trim();
        if($('#js_analytique option:selected').text().trim() == '')
        {
            show_info('Notice','Choisir l\'analytique','error');
            return false;
        }
    }

    $('#js_dossier_upl').val(dossier_sel);
    $('#js_exercice_upl').val(exercice_sel);
    $('#js_comment_upl').val(commentaire_urgent);
    $('#js_analytique_upl').val(analytique_sel);

    return true;
}

/**
 * vider liste
 */
function vider_liste()
{
    myDropzone.removeAllFiles();
}

/**
 * change urgent
 */
function change_urgent()
{
    if($('#js_is_urgent').is(':checked'))
    {
        $('#js_form_urgent').removeClass('hidden');
    }
    else
    {
        $('#js_form_urgent').addClass('hidden');
    }
}

/**
 * change analytique
 */
function change_analytique()
{
    if($('#js_is_analytique').is(':checked'))
    {
        $('#js_form_analytique').removeClass('hidden');
    }
    else
    {
        $('#js_form_analytique').addClass('hidden');
    }
}

/**
 * afficher filtre envoie
 */
function show_filtre()
{
    var lien = Routing.generate('image_envoi_filtre');
    $.ajax({
        data: {  },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            var titre = '<i class="fa fa-cogs fa-2x" aria-hidden="true"></i>&nbsp;<span>Options d\'envoi</span>',
                animated = 'bounceInRight';
            show_modal(data,titre,animated);
            charger_site();
        }
    });
}

/**
 * charger analytiques
 */
function charger_analytique()
{
    $('#js_analytique_conteneur').empty();
    if($('#dossier option:selected').text().trim() == '') return;

    lien = Routing.generate('code_analytiques');
    $.ajax({
        data: { dossier:$('#dossier').val() },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#js_analytique_conteneur').html(data);
        }
    });
}

/**
 * gerer height dropzone
 */
function set_height_dropzone()
{
    $('#my-awesome-dropzone').height($(window).height() * 0.74);
}


function change_analytique_param()
{

}