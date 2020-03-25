/**
 * Created by SITRAKA on 09/08/2018.
 */

$(document).ready(function(){
    $(document).on('click','.js_type_banque_li',function(){
        $('.js_type_banque_li').removeClass('active');
        $(this).addClass('active');
        charger_pcc_selects($(this));
    });

    $(document).on('click','#id_cle_container .cl_btn_cle',function(){
        return; //modif ***
        $('#id_cle_container .cl_btn_cle').each(function(){
            $(this).addClass('btn-white').removeClass('btn-primary');
        });

        $(this).addClass('btn-primary').removeClass('btn-white');

        charger_cle_props();
    });

    $(document).on('click','#id_save_cle_dossier',function(){
        save_cle_dossier();
    });

    $(document).on('click','.js_pas_cle',function(){
        var releve = $('#js_releve_selected').attr('data-id');
        $.ajax({
            data: { releve: releve },
            type: 'POST',
            url: Routing.generate('banque2_pas_piece'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                close_modal();
                show_info('SUCCES','Modification bien enregistrée avec succès');
                update_row();
            }
        });
    });

    $(document).on('click','.cl_desactiver_cle',function(){
        var type = parseInt($(this).attr('data-type')),
            dossier = $('#js_zero_boost').val(),
            banque = $('#js_zero_boost').val();


        if (type === 0) dossier = $('#dossier').val();

        $.ajax({
            data: {
                dossier: dossier,
                banque: banque
            },
            type: 'POST',
            url: Routing.generate('banque2_cle_desactiver'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_modal(data,'cle');
            }
        });
    });

    $(document).on('change','input[name="radio-exlure"]',function(){
        change_desactiver();
    });
});

function charger_pcc_selects(btn)
{
    var banque_type = btn.attr('data-id');
    $.ajax({
        data: { banque_type:banque_type, dossier:$('#dossier').val() },
        type: 'POST',
        url: Routing.generate('banque2_pccs_in_banque_type'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            /*modal_ui({ modal: false, resizable: true,title: 'test' },data);return;*/

            test_security(data);
            var res = $.parseJSON(data);
            $('#id_ecr_bilan').html(res.b);
            $('#id_ecr_resultat').html(res.r);
            $('#id_ecr_tva').html(res.t);
        }
    });
}

function charger_cle_props()
{
    var cle = $('#id_cle_container').find('span.btn-primary').attr('data-id');

    if (cle !== undefined)
    {
        $.ajax({
            data: { dossier:$('#dossier').val(), cle:cle },
            type: 'POST',
            url: Routing.generate('banque2_cle_props'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                var res = $.parseJSON(data);

                $('#id_ecr_bilan').html(res.b);
                $('#id_ecr_resultat').html(res.r);
                $('#id_ecr_tva').html(res.t);
                $('#id_ecr_tva_taux').val(res.tt);

                var exercice = parseInt($('#exercice').val().trim());
                $('#js_id_n_1').next('label').text(exercice - 1);
                $('#js_id_n').next('label').text(exercice);
                $('#js_id_n_p_1').next('label').text(exercice + 1);

                if (parseInt(res.tc) === 0) $('#js_id_engagement').prop('checked',true);
                else if (parseInt(res.tc) === 1) $('#js_id_tresorerie').prop('checked',true);
                else if (parseInt(res.tc) === 2) $('#js_id_tresorerie_avec_piece').prop('checked',true);
                after_check_type_compta();
            }
        });
    }
}

function after_check_type_compta()
{
    var type = parseInt($('input[name="radio-type-compta"]:checked').val());
    $('#id_container_ecriture').removeClass('hidden');
    $('#id_container_desactiver').addClass('hidden');

    if (type === 0)
    {
        $('#id_ecr_bilan').closest('td').removeClass('hidden');
        $('#id_ecr_resultat').closest('td').removeClass('hidden');
        $('#id_ecr_tva').closest('td').removeClass('hidden');
        $('#id_ecr_tva_taux').closest('td').removeClass('hidden');

        $('label[for="id_ecr_bilan"]').closest('th').removeClass('hidden');
        $('label[for="id_ecr_resultat"]').closest('th').removeClass('hidden');
        $('label[for="id_ecr_tva"]').closest('th').removeClass('hidden');
        $('label[for="id_ecr_tva_taux"]').closest('th').removeClass('hidden');
    }
    else if (type === 5)
    {
        $('#id_container_ecriture').addClass('hidden');
        $('#id_container_desactiver').removeClass('hidden');
        change_desactiver();
    }
    else
    {
        $('#id_ecr_bilan').closest('td').addClass('hidden');
        $('#id_ecr_resultat').closest('td').removeClass('hidden');
        $('#id_ecr_tva').closest('td').removeClass('hidden');
        $('#id_ecr_tva_taux').closest('td').removeClass('hidden');

        $('label[for="id_ecr_bilan"]').closest('th').addClass('hidden');
        $('label[for="id_ecr_resultat"]').closest('th').removeClass('hidden');
        $('label[for="id_ecr_tva"]').closest('th').removeClass('hidden');
        $('label[for="id_ecr_tva_taux"]').closest('th').removeClass('hidden');
    }

    if (type === 0 || type === 1)
    {
        $('#id_container_type-0_1').removeClass('hidden');
        $('.js_cl_container_image_affecter').addClass('hidden');
    }
    else if (type === 2)
    {
        $('#id_container_type-0_1').addClass('hidden');
        $('.js_cl_container_image_affecter').removeClass('hidden');
        var cle = $('#id_cle_container').find('span.btn-primary').attr('data-id');

        $.ajax({
            data: { dossier:$('#dossier').val(), cle:cle },
            type: 'POST',
            url: Routing.generate('banque2_images_by_cle'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                $('#js_cl_tb_affecter').closest('.js_container_tb').html('<table id="js_cl_tb_affecter"></table>');
                var res = $.parseJSON(data),
                    table_selected = $('#js_cl_tb_affecter'),
                    w = table_selected.parent().width(),
                    editurl = 'index.php';

                set_table_jqgrid(res,h_table,get_col_model_image_affecter(),get_col_model_image_affecter(w),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined);
                charger_ecriture_temp();
            }
        });
    }
}

function change_desactiver()
{
    var desactiver = parseInt($('input[name="radio-exlure"]:checked').val());
    if (desactiver === 0)
    {
        $('#id_desactiver_dossier').removeClass('hidden');
        $('#id_desactiver_banque').addClass('hidden');
    }
    else
    {
        $('#id_desactiver_dossier').addClass('hidden');
        $('#id_desactiver_banque').removeClass('hidden');
    }
}

function save_cle_dossier()
{
    var type_compta = parseInt($('input[name="radio-type-compta"]:checked').val());
    if (type_compta === 2)
    {
        save_imputation_piece();
        return;
    }

    var releve = $('#js_releve_selected').attr('data-id');

    var default_val = '0#0',
        bilan = default_val,
        resultat = default_val,
        tva = default_val,
        tva_taux = parseFloat($('#id_ecr_tva_taux').val()),
        desactiver = parseInt($('input[name="radio-exlure"]:checked').val()),
        dossier_desactiver = $('#js_zero_boost').val(),
        banque_desactiver = $('#js_zero_boost').val();

    if (type_compta === 0)
    {
        bilan = $('#id_ecr_bilan').val();
        if (bilan === default_val)
        {
            show_info('Erreur','Compte non parametré','error');
            return;
        }

        resultat = $('#id_ecr_resultat').val();
        tva = $('#id_ecr_tva').val();
    }
    else if (type_compta === 1)
    {
        resultat = $('#id_ecr_resultat').val();
        tva = $('#id_ecr_tva').val();

        if (resultat === default_val && tva === default_val)
        {
            show_info('Erreur','Compte non parametré','error');
            return;
        }
    }
    else if (type_compta === 5)
    {
        if (desactiver === 0) dossier_desactiver = $('#id_desactiver_dossier').val();
        else banque_desactiver = $('#id_desactiver_banque').val();
    }

    if (tva === default_val && isNaN(tva_taux) && type_compta !== 5)
    {
        show_info('Erreur','Le Taux doit être un nombre','error');
        return;
    }

    var cles_slaves = [];
    $('#id_cle_container').find('.cl_btn_cle').each(function(){
        if (!$(this).hasClass('btn-primary'))
            cles_slaves.push($(this).attr('data-id'));
    });

    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            cle: $('#id_cle_container').find('span.btn-primary').attr('data-id'),
            type_compta: type_compta,
            bilan: bilan,
            resultat: resultat,
            tva: tva,
            tva_taux: tva_taux,
            releve: releve,
            methode: methode_dossier,
            pas_de_piece: $('#jd_id_pas_piece').is(':checked') ? 1 : 0,
            dossier_desactiver: dossier_desactiver,
            banque_desactiver: banque_desactiver,
            cles_slaves: JSON.stringify(cles_slaves)
        },
        type: 'POST',
        url: Routing.generate('banque2_save_cle_dossier'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            close_modal();
            show_info('Succès','Modification bien enregistrée');
            charger_analyse();
        }
    });
}
