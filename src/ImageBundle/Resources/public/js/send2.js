/**
 * Created by SITRAKA on 31/05/2017.
 */
$(document).on('click','#js_upload_click',function(){
    upload_files();
});

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

    $('#div-input-image .file-caption-main .fileinput-upload').click();
    //myDropzone.processQueue();
}

/**
 * test if parametres upload are valid
 * @returns {boolean}
 */
function upload_is_valid()
{
    if(!$('.kv-preview-thumb').length > 0)
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

    var urgents = [];
    var urgent_checked = false,
        urgent_no_checked = false;
    $('.kv-preview-thumb .js_is_urgent').each(function(){
        if ($(this).closest('.kv-preview-thumb').find('.file-actions').find('.glyphicon-ok-sign').length <= 0)
        {
            if($(this).is(':checked'))
            {
                urgents.push(1);
                urgent_checked = true;
            }
            else
            {
                urgents.push(0);
                urgent_no_checked = true;
            }
        }
    });

    var status_checked = 0;
    if(urgent_checked && urgent_no_checked) status_checked = 3;
    else if(urgent_checked && !urgent_no_checked) status_checked = 2;
    else if(!urgent_checked && urgent_no_checked) status_checked = 1;
    $('#js_urgent_stat_upl').val(status_checked);

    //code analytiques
    var analytiques_sels = [];
    if($('#js_is_analytique').is(':checked'))
    {
        $('.kv-preview-thumb .js_select_analytique').each(function(){
            if ($(this).closest('.kv-preview-thumb').find('.file-actions').find('.glyphicon-ok-sign').length <= 0)
            {
                analytiques_sels.push(parseInt($(this).val()));
            }
        });
    }
    
    var commentaires_sels = [];
    if ($('#js_commentaires').hasClass('btn-primary'))
    {
        $('.kv-preview-thumb .js_select_comment').each(function(){
            if ($(this).closest('.kv-preview-thumb').find('.file-actions').find('.glyphicon-ok-sign').length <= 0)
            {
                commentaires_sels.push(parseInt($(this).val()));
            }
        });
    }

    //mode de paiement
    var mps = [];
    $('.kv-preview-thumb .js_select_mp').each(function(){
        if ($(this).closest('.kv-preview-thumb').find('.file-actions').find('.glyphicon-ok-sign').length <= 0)
        {
            mps.push($(this).val());
        }
    });

    $('#js_dossier_upl').val($('#dossier').val());
    $('#js_exercice_upl').val($('#exercice').val());
    $('#js_comment_urgent_upl').val((is_urgent) ? $('#js_message_urgent').val().trim() : '');
    $('#js_urgents_upl').val(JSON.stringify(urgents));
    $('#js_analytiques_upl').val(JSON.stringify(analytiques_sels));
    $('#js_mps_upl').val(JSON.stringify(mps));
    $('#js_commentraires_upl').val(JSON.stringify(commentaires_sels));

    mode_paiements_upls = mps;
    urgents_upls = urgents;
    analytiques_upls = analytiques_sels;

    return true;
}
