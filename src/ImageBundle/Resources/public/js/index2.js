/**
 * Created by SITRAKA on 31/08/2016.
 */

var analytiques = [],
    dossier_id_sel = $('#js_zero_boost').val().trim(),
    class_div_analytique = 'js_div_analytique',
    class_div_urgent = 'js_div_urgent',
    class_div_mp = 'js_mp',
    class_div_comment = 'js_comment',
    urgent_id = 1,
    is_urgent = false,
    urgent_max = 3,
    myDropzone,
    mode_paiements_upls = [],
    urgents_upls = [],
    analytiques_upls = [],
    comments = [];

/**
 * EVENTS
 */
$(document).ready(function(){
    dossier_depend_exercice = true;
    var defaultMessage =
        'Sélectionner un DOSSIER, puis un EXERCICE et CLIQUER pour sélectionner des PIECES ou DEPOSER les ici.<br><br><br>' +
        'Vous pouvez aussi définir et choisir une affectation ANALYTIQUE par pièce.<br><br><br>' +
        "Il est aussi possible d'envoyer jusqu'à 3 pièces en URGENCE au chef de mission.";

    $('#js_id_input_image')
        .fileinput({
            language: 'fr',
            theme: 'fa',
            uploadAsync: false,
            showPreview: true,
            showUpload: true,
            showBrowse: false,
            showRemove: false,
            showCancel: false,
            maxFilePreviewSize: 0,
            uploadUrl: Routing.generate('img_send'),
            dropZoneTitle: defaultMessage,
            browseOnZoneClick: true,
            uploadExtraData: function(){
                return {
                    js_exercice_upl: $('#exercice').val(),
                    js_dossier_upl: $('#dossier').val(),
                    js_comment_urgent_upl: ((is_urgent) ? $('#js_message_urgent').val().trim() : ''),
                    js_urgents_upl: JSON.stringify(urgents_upls),
                    js_analytiques_upl: JSON.stringify(analytiques_upls),
                    js_mps_upl: JSON.stringify(mode_paiements_upls),
                    js_urgent_stat_upl: $('#js_urgent_stat_upl').val(),
                    js_commentraires_upl: $('#js_commentraires_upl').val(),
                    js_lot_journalier: $('#id_cumuler').is(':checked') ? 1 : 0
                }
            }
        })
        .on('filebatchselected', function(event, files) {
            if ($('#dossier option:selected').text().trim() === '')
            {
                vider_liste();
                show_info('ERREUR','Choisir le DOSSIER','error');
            }
            else
            {

            }
            if ($('#exercice option:selected').text().trim() === '')
            {
                vider_liste();
                show_info('ERREUR','Choisir l EXERCICE','error');
            }
            else
            {

            }
            verrou_fenetre(false);
            set_height();
        })
        .on('fileselect', function(event, numFiles, label) {
            verrou_fenetre(true);
        })
        .on('fileloaded', function(event, file, previewId, index, reader) {
            charger_additifs(previewId);
        })
        .on('filebatchuploadsuccess', function(event, data) {
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response.toString(), reader = data.reader;

            if (response === 'xxxxxxxxx')
            {
                show_info("Une erreur est survenue pendant l'envoi","Veuillez renvoyer les images","error");
                vider_liste();
            }
            else show_info("Envoi images","Les images sont envoyées avec succès.");
        })
        .on('filebatchuploaderror', function(event, data, msg) {
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response, reader = data.reader;
            show_info("Une erreur est survenue pendant l'envoi","Veuillez renvoyer les images","error");
        });

    set_height();

    /*Dropzone.autoDiscover = false;
    myDropzone = new Dropzone('#my-awesome-dropzone', {
        maxFilesize: 200,
        parallelUploads: 10000,
        uploadMultiple: true,
        dictDefaultMessage: defaultMessage,
        addRemoveLinks: true,
        dictRemoveFile: "Enlever",
        thumbnailWidth: 10,
        thumbnailHeight: 10,
        autoProcessQueue: false,
        async: true,
        sendingmultiple: function(){
            show_info('DEBUT','MERCI DE PATIENTER');
            verrou_fenetre(true);
        },
        successmultiple: function (file, response) {
            $('#image_test').html(response);
            show_info("Envoi images","Les images sont envoyées avec succès.");
            verrou_fenetre(false);
        },
        accept: function(file, done) {
            if($('#dossier option:selected').text().trim() == '' || $('#exercice option:selected').text().trim() == '')
            {
                vider_liste();
                show_info('NOTICE','CHOISIR D ABORD LE DOSSIER ET L EXERCICE','error');
                if($('#dossier option:selected').text().trim() == '') $('#dossier').closest('.form-group').addClass('has-error');
                if($('#exercice option:selected').text().trim() == '') $('#exercice').closest('.form-group').addClass('has-error');
            }
            else
            {
                done();
                $('#dossier').closest('.form-group').removeClass('has-error');
                $('#exercice').closest('.form-group').removeClass('has-error');
            }
        }
    });
    set_height_dropzone();*/

    charger_site();

    /*//after add file
    myDropzone.on('addedfile',function(){
        charger_analytique();
    });*/

    //mode de paiement
    $(document).on('change','#js_id_mp',function(){
        $('.js_select_mp').each(function(){
            if ($(this).closest('.kv-preview-thumb').find('.file-actions').find('.glyphicon-ok-sign').length <= 0)
                $(this).val($('#js_id_mp').val());
        });
        /*$('.dz-preview').each(function(){
            $(this).find('.js_select_mp').val($('#js_id_mp').val());
        });*/
        show_info('NOTICE','Mode de paiement changer pour toutes les pièces','warning');
    });

    //Analytique
    $(document).on('change','#js_id_ana',function(){
        $('.js_select_analytique').each(function(){
            if ($(this).closest('.kv-preview-thumb').find('.file-actions').find('.glyphicon-ok-sign').length <= 0)
                $(this).val($('#js_id_ana').val());
        });
        show_info('NOTICE','Analytique changer pour toutes les pièces','warning');
    });

    //Commentaire
    $(document).on('change','#js_id_commentaire',function(){
        $('.js_select_comment').each(function(){
            if ($(this).closest('.kv-preview-thumb').find('.file-actions').find('.glyphicon-ok-sign').length <= 0)
                $(this).val($('#js_id_commentaire').val());
        });
        show_info('NOTICE','Commentaire changer pour toutes les pièces','warning');
    });

    //urgent
    $(document).on('change','.js_is_urgent',function(){
        if($(this).is(':checked'))
        {
            var nb_checked = 0;
            $('.js_is_urgent:checked').each(function(){
                if ($(this).closest('.kv-preview-thumb').find('.file-actions').find('.glyphicon-ok-sign').length <= 0)
                    nb_checked++;
            });

            if(nb_checked > urgent_max)
            {
                $(this).attr('checked',false);
                show_info('ERREUR',"NOMBRE MAXIMUM D'IMAGE URGENT ATTEINT",'error');
            }
        }

        is_urgent = ($('.js_is_urgent:checked').length > 0);
        if(is_urgent) $('#js_message_urgent').closest('.form-horizontal').removeClass('hidden');
        else $('#js_message_urgent').closest('.form-horizontal').addClass('hidden');
    });
});

function charger_additifs(previewId)
{
    var div_preview = $('#'+previewId);
    //Urgent
    if (div_preview.find('.' + class_div_urgent).length <= 0)
    {
        var id_temp = 'js_is_urgent' + urgent_id,
            div_urgent = '<div class="text-center ' + class_div_urgent +  '">';
        div_urgent += '<span class="checkbox checkbox-danger">';
        div_urgent += '<input id="'+ id_temp +'" type="checkbox" class="js_is_urgent">';
        div_urgent += '<label for="'+ id_temp +'">urgent</label>';
        div_urgent += '</span>';
        $(div_urgent).insertBefore(div_preview.find('.file-actions'),null);
        urgent_id++;
    }

    //analytique
    if(div_preview.find('.' + class_div_analytique).length <= 0)
    {
        var div_analytique = '<div class="'+ class_div_analytique +'">';
        div_analytique += '<select class="text-center js_select_analytique form-control input-sm">';

        var opt_anal = '';
        opt_anal += '<option value="0"></option>';
        analytiques.forEach(function(section){

            if (section.code.trim() !== '') opt_anal += '<optgroup label="'+ section.code +'">';
            section.cas.forEach(function(entry){
                var l = '';
                if (section.code !== '') l += section.code + '-';
                if (entry.code !== '') l += entry.code;
                opt_anal += '<option value="' + entry.id + '">' + l + '</option>';
            });
            if (section.code.trim() !== '') opt_anal += '</optgroup>';

            /*opt_anal += '<optgroup label="'+ section.libelle +'">';
            section.cas.forEach(function(entry){
                opt_anal += '<option value="' + entry.id + '">' + entry.libelle + '</option>';
            });
            opt_anal += '</optgroup>';*/
        });

        div_analytique += opt_anal;
        div_analytique += '</select>';
        div_analytique += '</div>';
        $(div_analytique).insertBefore(div_preview.find('.file-actions'),null);
        div_preview.find('.js_select_analytique').val($('#js_id_ana').val());
    }
    if($('#js_analytiques').hasClass('btn-primary'))
    {
        $('.'+class_div_analytique).removeClass('hidden');
        $('#js_id_ana').closest('.form-horizontal').removeClass('hidden');
    }
    else
    {
        $('.'+class_div_analytique).addClass('hidden');
        $('#js_id_ana').closest('.form-horizontal').addClass('hidden');
    }

    //commentaires
    if(div_preview.find('.' + class_div_comment).length <= 0)
    {
        var div_comment = '<div class="'+ class_div_comment +'">';
        div_comment += '<select class="text-center js_select_comment form-control input-sm">';

        var opt_comment = '';
        opt_comment += '<option value="0"></option>';
        comments.forEach(function(entry){
            opt_comment += '<option value="' + entry.id + '">' + entry.libelle + '</option>';
        });

        div_comment += opt_comment;
        div_comment += '</select>';
        div_comment += '</div>';
        $(div_comment).insertBefore(div_preview.find('.file-actions'),null);
        div_preview.find('.js_select_comment').val($('#js_id_commentaire').val());
    }
    if($('#js_commentaires').hasClass('btn-primary'))
    {
        $('.'+class_div_comment).removeClass('hidden');
        $('#js_id_commentaire').closest('.form-horizontal').removeClass('hidden');
    }
    else
    {
        $('.'+class_div_comment).addClass('hidden');
        $('#js_id_commentaire').closest('.form-horizontal').addClass('hidden');
    }

    //mode de paiement
    if(div_preview.find('.' + class_div_mp).length <= 0)
    {
        var div_mp = '<div class="'+ class_div_mp +'">';
        div_mp += '<select class="text-center js_select_mp form-control input-sm">';
        div_mp += $('#js_id_mp').html();
        div_mp += '</select>';
        div_mp += '</div>';
        $(div_mp).insertBefore(div_preview.find('.file-actions'),null);
        div_preview.find('.js_select_mp').val($('#js_id_mp').val());
    }

    $('.js_select_mp').addClass('hidden');
}

function set_height()
{
    $('.file-drop-zone').height($(window).height() - 320);
}

$(document).on('change','#js_is_analytique',function(){
    change_analytique();
});

$(document).on('click','#js_remove_all',function(){
    vider_liste();
});

$(document).on('click','#js_cancel_dossier',function(){
    $('.'+class_div_analytique).addClass('hidden');
    $('#js_is_analytique').attr('checked',false);
    close_modal();
});

$(document).on('click','#js_dossier_choose',function(){
    dossier_selected();
});

$(document).on('click','#js_analytiques',function(){
    if($('#dossier option:selected').text().trim() === '')
    {
        show_info('NOTICE','CHOISIR LE DOSSIER','warning');
    }
    else show_edit_analytiques(false);
});

$(document).on('click','#js_commentaires',function(){
    if($('#dossier option:selected').text().trim() === '')
    {
        show_info('NOTICE','CHOISIR LE DOSSIER','warning');
    }
    else show_edit_commentaires(false);
});

$(document).on('change','#client',function(){
   vider_liste();
});

$(document).on('change','#site',function(){
   vider_liste();
});

$(document).on('change','#dossier',function(){
   vider_liste();
   dossier_selected();
});

/**
 * FONCTIONS
 */
/**
 * gerer height dropzone
 */
/*function set_height_dropzone()
{
    $('#my-awesome-dropzone').height($(window).height() - 280);
}*/

function change_analytique()
{
    if(!$('.dz-preview').length > 0)
    {
        $('#js_is_analytique').attr('checked',$('#js_is_analytique').is(':checked') ? false :  true);
        show_info('Image vide','Ajouter les fichiers','warning');
        return;
    }

    if(!$('#js_is_analytique').is(':checked'))
    {
        $('.'+class_div_analytique).addClass('hidden');
        analytiques = [];
        dossier_id_sel = $('#js_zero_boost').val().trim();
        $('#js_is_analytique_nom_dossier').addClass('hidden').text('');
        return;
    }
    else $('.'+class_div_analytique).removeClass('hidden');

    dossier_selected();

    /*var lien = Routing.generate('img_dossier');
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
            var titre = '<i class="fa fa-cogs fa-2x" aria-hidden="true"></i>&nbsp;<span>Choix dossier</span>',
                animated = 'bounceInRight';
            show_modal(data,titre,animated);
            charger_site();
        }
    });*/
}

/**
 * vider liste
 */
function vider_liste()
{
    $('#js_id_input_image').fileinput('clear');

    //myDropzone.removeAllFiles();
    //analytiques = new Array();

    //$('#js_is_analytique').attr('checked',false);

    //dossier_id_sel = $('#js_zero_boost').val().trim();
    //dossier_selected();
}

/*function charger_analytique()
{
    $('.dz-preview').each(function(){
        //envoi urgent
        if($(this).find('.' + class_div_urgent).length <= 0)
        {
            var id_temp = 'js_is_urgent' + urgent_id,
                div_urgent = '<div class="text-center ' + class_div_urgent +  '">';
            div_urgent += '<span class="checkbox checkbox-danger">';
            div_urgent += '<input id="'+ id_temp +'" type="checkbox" class="js_is_urgent">';
            div_urgent += '<label for="'+ id_temp +'">urgent</label>';
            div_urgent += '</span>';
            $(div_urgent).insertBefore($(this).find('.dz-remove'));
            urgent_id++;
        }

        //analytique
        if($(this).find('.' + class_div_analytique).length <= 0)
        {
            var div_analytique = '<div class="'+ class_div_analytique +'">';
            div_analytique += '<select class="text-center js_select_analytique form-control input-sm">';

            div_analytique += '<option value="0"></option>';
            analytiques.forEach(function(entry){
                div_analytique += '<option value="' + entry.id + '">' + entry.libelle + '</option>';
            });

            div_analytique += '</select>';
            div_analytique += '</div>';
            $(div_analytique).insertAfter($(this).find('.'+class_div_urgent));
        }

        //analytique
        if($(this).find('.' + class_div_mp).length <= 0)
        {
            var div_mp = '<div class="'+ class_div_mp +'">';
            div_mp += '<select class="text-center js_select_mp form-control input-sm">';
            div_mp += $('#js_id_mp').html();
            div_mp += '</select>';
            div_mp += '</div>';
            $(div_mp).insertAfter($(this).find('.'+class_div_urgent));
            $(this).find('.js_select_mp').val($('#js_id_mp').val());
        }
    });

    if($('#js_analytiques').hasClass('btn-primary')) $('.'+class_div_analytique).removeClass('hidden');
    else $('.'+class_div_analytique).addClass('hidden');

    //if($('#js_is_analytique').is(':checked')) $('.'+class_div_analytique).removeClass('hidden');
    //else $('.'+class_div_analytique).addClass('hidden');
}*/

function dossier_selected()
{
    dossier_id_sel = $('#dossier').val().trim();
    var dossier_nom = $('#dossier option:selected').text().trim();
    //$('#js_is_analytique_nom_dossier').text(dossier_nom);

    if(dossier_nom === '')
    {
        //$('#js_is_analytique_nom_dossier').addClass('hidden');
        $('.'+class_div_analytique).addClass('hidden');
        dossier_id_sel = $('#js_zero_boost').val();
        analytiques = [];

        //$('#js_is_analytique').attr('checked',false);
    }
    else
    {
        //$('#js_is_analytique_nom_dossier').removeClass('hidden');
        $('.'+class_div_analytique).removeClass('hidden');
        dossier_id_sel = $('#dossier').val();

        $.ajax({
            data: { dossier:dossier_id_sel },
            type: 'POST',
            url: Routing.generate('code_analytiques_json'),
            //async:false,
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);

                /**
                 * Code analytique
                 */
                var resu = $.parseJSON(data),
                    array_temp = resu.ca,
                    count_analytique = 0;

                analytiques = [];
                array_temp.forEach(function(entry) {
                    count_analytique += entry.cas.length;

                    var array_sous_section = [];
                    entry.cas.forEach(function(cas){
                        array_sous_section.push({
                            id:cas.id,
                            code:cas.code,
                            libelle:cas.libelle
                        })
                    });

                    analytiques.push({
                        id: entry.s.id,
                        code: entry.s.code,
                        libelle: entry.s.libelle,
                        cas: array_sous_section
                    });
                });

                var opt_anal = '';
                opt_anal += '<option value="0"></option>';
                analytiques.forEach(function(section){
                    if (section.code.trim() !== '') opt_anal += '<optgroup label="'+ section.code +'">';
                    section.cas.forEach(function(entry){
                        var l = '';
                        if (section.code !== '') l += section.code + '-';
                        if (entry.code !== '') l += entry.code;
                        opt_anal += '<option value="' + entry.id + '">' + l + '</option>';
                    });
                    if (section.libelle.trim() !== '') opt_anal += '</optgroup>';
                });

                $('#js_id_ana').html(opt_anal);

                $('#js_is_analytique').attr('checked',true);
                $('.' + class_div_analytique).remove();
                $('#js_analytiques').removeClass('btn-primary').removeClass('btn-default');

                if(count_analytique === 0)
                {
                    $('#js_id_ana').closest('.form-horizontal').addClass('hidden');
                    $('#js_analytiques').addClass('btn-default');
                }
                else
                {
                    $('#js_id_ana').closest('.form-horizontal').removeClass('hidden');
                    $('#js_analytiques').addClass('btn-primary');
                }

                /**
                 * Commentaire
                 */
                var array_commentes_temp = resu.cd;
                comments = [];
                array_commentes_temp.forEach(function(entry) {
                    comments.push({id:entry.id,code:entry.code,libelle:entry.libelle});
                });

                var opt_comment = '';
                opt_comment += '<option value="0"></option>';
                comments.forEach(function(entry){
                    opt_comment += '<option value="' + entry.id + '">' + entry.libelle + '</option>';
                });
                $('#js_id_commentaire').html(opt_comment);

                $('#js_is_commentaire').attr('checked',true);
                $('.' + class_div_comment).remove();
                $('#js_commentaires').removeClass('btn-primary').removeClass('btn-default');

                if(comments.length === 0)
                {
                    $('#js_id_commentaire').closest('.form-horizontal').addClass('hidden');
                    $('#js_commentaires').addClass('btn-default');
                }
                else
                {
                    $('#js_id_commentaire').closest('.form-horizontal').removeClass('hidden');
                    $('#js_commentaires').addClass('btn-primary');
                }

                $('.file-preview-frame').each(function(){
                    charger_additifs($(this).attr('id'));
                });
                //charger_analytique();
            }
        });
    }
    //close_modal();
}

function charger_periode()
{
    dossier_selected();
}