/**
 * Created by SITRAKA on 05/12/2018.
 */
$(document).ready(function(){
    $(document).on('click','.js_show_upload_image',function(){
        show_upload_image($(this));
    });
});

function show_upload_image(span)
{
    var type = parseInt(span.attr('data-type')),
        datas = span.closest('tr').attr('id').split('-'),
        type_banque = parseInt(datas[0]),
        id = datas[1],
        is_releve = parseInt(span.attr('data-is_releve')),
        sc = span.closest('tr').find('.cl_sc_id').text().trim();

    $('.'+tr_edited).removeClass(tr_edited);
    var releve = (type === 0) ? span.closest('tr').addClass(tr_edited).attr('id') : $('#js_zero_boost').val(),
        mois = (type === 0) ? '2018-12' : span.attr('data-mois'),
        banque_compte = (type === 0) ? $('#js_zero_boost').val() : id;

    $.ajax({
        data: {
            releve: releve,
            type: type,
            mois: mois,
            banque_compte: banque_compte,
            type_banque: type_banque,
            is_releve: is_releve,
            sc: sc
        },
        type: 'POST',
        url: Routing.generate('banque_pm_image_uploader'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_modal(data,'Charger l\'image');

            var defaultMessage =
                'CLIQUER pour sélectionner la PIECE ou DEPOSER la ici.';
            $('#id_image')
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
                    uploadUrl: Routing.generate('banque_pm_upload'),
                    dropZoneTitle: defaultMessage,
                    browseOnZoneClick: true,
                    uploadExtraData: function(){
                        var type = parseInt($('#id_image').attr('data-type')),
                            type_banque = $('#id_mois').attr('data-type_banque'),
                            releve = (type === 0) ? $('#js_releve_selected').attr('data-id'): $('#js_zero_boost').val(),
                            mois = $('#id_mois').val(),
                            banque_compte = $('#id_mois').attr('data-banque_compte'),
                            banque_ob_manquant = $('#id_mois').attr('data-banque_ob_manquant'),
                            releve_ob = $('input[name="radio-name-rel"]:checked').length > 0 ?
                                $('input[name="radio-name-rel"]:checked').closest('tr').attr('data-id') :
                                $('#js_zero_boost').val();

                        return {
                            exercice: $('#exercice').val(),
                            dossier: $('#dossier').val(),
                            releve: releve,
                            mois: mois,
                            banque_compte: banque_compte,
                            banque_ob_manquant: banque_ob_manquant,
                            type_banque: type_banque,
                            releve_ob: releve_ob
                        }
                    }
                })
                .on('filebatchselected', function(event, files) { })
                .on('fileselect', function(event, numFiles, label) {
                    if ($('#id_releve_manquants').length > 0 && $('input[name="radio-name-rel"]:checked').length <= 0)
                    {
                        show_info('Erreur','Choisir la ligne de relevé','error');
                        $('#id_image').fileinput('clear');
                    }
                })
                .on('fileloaded', function(event, file, previewId, index, reader) { })
                .on('filebatchuploadsuccess', function(event, data) {
                    var form = data.form, files = data.files, extra = data.extra,
                        response = data.response.toString(), reader = data.reader;

                    if (parseInt(response) === -1)
                    {
                        show_info("Une erreur est survenue pendant l'envoi","Veuillez renvoyer l\' image","error");
                        vider_liste();
                    }
                    else
                    {
                        //-- upload releve manquant
                        var type = parseInt($('#id_image').attr('data-type')),
                            can_close_modal = true;
                        if (type !== 0) $('.' + tr_edited).removeClass(tr_edited);
                        show_info("Envoi images","Les images sont envoyées avec succès.");
                        $('#id_image').fileinput('clear');

                        if ($('#id_releve_manquants').length > 0)
                        {
                            $('input[name="radio-name-rel"]:checked').closest('tr').remove();
                            can_close_modal = $('#id_releve_manquants .table tbody tr').length === 0;
                        }

                        if (can_close_modal)
                        {
                            close_modal();
                            go();
                        }
                    }
                })
                .on('filebatchuploaderror', function(event, data, msg) {
                    var form = data.form, files = data.files, extra = data.extra,
                        response = data.response, reader = data.reader;
                    show_info("Une erreur est survenue pendant l'envoi","Veuillez renvoyer l\' image","error");
                });
        }
    });
}