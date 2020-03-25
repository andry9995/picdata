/**
 * Created by SITRAKA on 08/09/2016.
 */
$(document).on('click','#js_upload_click',function(){
    show_sender();
});

$(document).on('click','#js_cancel_upload',function(){
    close_modal();
});

$(document).on('click','#js_upload_file',function(){
    upload_files();
});

/**
 * show sender form
 */
function show_sender()
{
    if(!param_sender_is_valid()) return;
    var lien = Routing.generate('img_sender');
    $.ajax({
        data: { dossier:dossier_id_sel, is_urgent:is_urgent },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data)
        {
            test_security(data);
            var titre = '<i class="fa fa-cogs fa-2x" aria-hidden="true"></i>&nbsp;<span>Param&egrave;tres d&apos;envoi</span>',
                animated = 'bounceInRight';
            show_modal(data,titre,animated);
            if(parseInt($('#js_charger_site').val()) == 1) charger_site();
        }
    });
}

/**
 * test if all parametres valid
 * @returns {boolean}
 */
function param_sender_is_valid()
{
    if(!$('.dz-preview').length > 0)
    {
        show_info('NOTICE','AJOUTER LES IMAGES','warning');
        return false;
    }

    //urgent
    is_urgent = false;
    urgents = new Array();
    var urgent_checked = false,
        urgent_no_checked = false;
    $('.dz-preview .js_is_urgent').each(function(){
        if($(this).is(':checked'))
        {
            urgents.push(1);
            is_urgent = true;
            urgent_checked = true;
        }
        else
        {
            urgents.push(0);
            urgent_no_checked = true;
        }
    });
    var status_checked = 0;
    if(urgent_checked && urgent_no_checked) status_checked = 3;
    else if(urgent_checked && !urgent_no_checked) status_checked = 2;
    else if(!urgent_checked && urgent_no_checked) status_checked = 1;
    $('#js_urgent_stat_upl').val(status_checked);


    //code analytiques
    analytiques_sels = new Array();
    if($('#js_is_analytique').is(':checked'))
    {
        $('.dz-preview .js_select_analytique').each(function(){
            analytiques_sels.push(parseInt($(this).val()));
        });
    }
    return true;
}

/**
 * launch UPLOAD
 */
function upload_files()
{
    if(!upload_is_valid()) return;

    /*lien = Routing.generate('img_sender_test');
    $.ajax({
        data: { dossier:$('#js_dossier_upl').val(), exercice:$('#js_exercice_upl').val(),
                message_urgent:$('#js_comment_urgent_upl').val(), js_urgents_upl:$('#js_urgents_upl').val(),
                analytiques:$('#js_analytiques_upl').val() },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data)
        {
            test_security(data);
            $('#image_test').html(data);
        }
    });*/
    myDropzone.processQueue();
}

/**
 * test if parametres upload are valid
 * @returns {boolean}
 */
function upload_is_valid()
{
    if(!$('.dz-preview').length > 0)
    {
        show_info('Image vide','Ajouter les fichiers','warning');
        return false;
    }
    if($('#dossier option:selected').text().trim() == '')
    {
        show_info('erreur','choisir le dossier','error');
        return false;
    }
    if(parseInt($('#exercice').val()) == 0)
    {
        show_info('erreur',"choisir l'exercice",'error');
        return false;
    }
    if(is_urgent && $('#js_message_urgent').val().trim() == '')
    {
        show_info('Notice','MESSAGE obligatoire pour les PIECES URGENTS','error');
        return false;
    }

    $('#js_dossier_upl').val($('#dossier').val());
    $('#js_exercice_upl').val($('#exercice').val());
    $('#js_comment_urgent_upl').val((is_urgent) ? $('#js_message_urgent').val().trim() : '');
    $('#js_urgents_upl').val(JSON.stringify(urgents));
    $('#js_analytiques_upl').val(JSON.stringify(analytiques_sels));

    return true;
}