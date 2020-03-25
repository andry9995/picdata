/**
 * Created by SITRAKA on 02/08/2018.
 */

var montantTTC = 0,
    h_table = 150,
    class_tr_edited = 'tr_releve_edited';

$(document).ready(function(){
    $(document).on('click','.js_show_image_a_affecter',function(){
        $('.image_a_affecter').removeClass('image_a_affecter');
        $(this).addClass('image_a_affecter');
        $('.' + class_tr_edited).removeClass(class_tr_edited);
        $(this).closest('tr').addClass(class_tr_edited);
        //retour
        last_action = { element: $(this), type: 'c', text:'' };

        show_image_a_affecter($(this));
    });

    $(document).on('click','.cl_annuler_imputation',function(){
        $('.' + class_tr_edited).removeClass(class_tr_edited);
        $(this).closest('tr').addClass(class_tr_edited);

        var releve = $(this).closest('tr').attr('id');
        swal({
            title: 'Toutes les pièces flaguées à cette ligne sera modifiées',
            text: 'Voulez-vous quand même annuler cette imputation ?',
            type: 'question',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui',
            cancelButtonText: 'Annuler'
        }).then(function () {
            annuler_imputation(releve,1);
        }, function (dismiss) {
            if (dismiss === 'cancel') {
            }
        });
    });

    $(document).on('change','input[name="radio-type-compta"]',function(){
        if ($('#js_cl_tb_ecriture').length > 0) charger_ecriture_temp();
        if ($('#js_id_table_ecriture_temp').length > 0) charger_ecriture_cle_temp();
        if ($('#td_2').length > 0) charger_cle_dossier();
        if ($('#id_ecr_bilan').length > 0) after_check_type_compta();
        if ($('#id_tbody_imputation').length > 0) imputation_set_hidden_radio();
    });

    $(document).on('click','.js_show_image_temp',function(){
        show_image_pop_up($(this).closest('tr').next('tr').find('.js_id_image').text());
    });

    $(document).on('click','.js_show_image_affecter',function(){
        if (parseInt($(this).closest('.type').attr('data-type')) !== 2) show_image_pop_up($(this).attr('data-id'));
        else
        {
            var images = $(this).attr('data-id');
            $.ajax({
                data: {
                    images: images
                },
                url: Routing.generate('banque_liste_images'),
                type: 'POST',
                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                beforeSend: function(jqXHR) {
                    jqXHR.overrideMimeType('text/html;charset=utf-8');
                },
                dataType: 'html',
                success: function(data){
                    test_security(data);
                    show_modal(data,'Images');
                }
            });
        }
    });

    $(document).on('change','.js_variation',function(){
        gerer_ligne_hidden();
    });

    $(document).on('change','#js_id_sens',function(){
        gerer_ligne_hidden();
    });

    $(document).on('change','#js_id_flagguer',function(){
        gerer_ligne_hidden();
    });

    $(document).on('change','.js_piece_item',function(){
        var cocher = false,
            piece = $(this).closest('tr').find('.js_show_image_').text().trim(),
            current_status = $(this).is(':checked');
        $('.js_piece_item').each(function(){
            if ($(this).is(':checked')) cocher = true;
            $('#js_cl_tb_affecter').find('tr').each(function(){
                if (!$(this).hasClass('jqgfirstrow'))
                    if ($(this).find('.js_show_image_').text().trim() === piece)
                        $(this).find('.js_piece_item').prop('checked',current_status);
            });
        });

        if (cocher)
        {
            $('#js_id_valider_piece').removeClass('hidden');
            $('#js_id_valider_piece_dis').addClass('hidden');
        }
        else
        {
            $('#js_id_valider_piece_dis').removeClass('hidden');
            $('#js_id_valider_piece').addClass('hidden');
        }

        charger_ecriture_temp();
    });

    $(document).on('click','.js_id_pas_piece',function(){
        var type = parseInt($(this).attr('data-type')),
            images = [];

        if (type === 1)
        {
            $('#js_cl_tb_affecter').find('tr').each(function(){
                if (!$(this).hasClass('jqgfirstrow'))
                {
                    images.push($(this).find('.js_id_image').text().trim());
                }
            });
        }

        $.ajax({
            data: {
                releve: $('#js_releve_selected').attr('data-id'),
                type: type,
                images: JSON.stringify(images)
            },
            url: Routing.generate('banque_pas_image'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(datav){
                test_security(datav);
                //show_modal(datav);return;
                scroll_position = $('#gbox_js_id_releve_liste').find('.ui-jqgrid-bdiv').scrollTop();
                update_row();
            }
        });
    });

    $(document).on('click','#js_id_valider_piece',function(){
        save_imputation_piece(undefined,$(this));
    });
});

function save_imputation_piece(par_piece,span)
{
    var m = number_fr_to_float($('.js_ecart').text().trim());
    if (m !== 0)
    {
        show_info('Affectation déséquilibrée de ' + number_format(m,2,',',' ') ,'Les ecritures de cette pièce ne corréspond pas à la ligne de banque','error');
        return;
    }

    var type = 0;
    if (typeof span !== 'undefined' && typeof span.closest('div').attr('data-type') !== 'undefined')
        type = parseInt(span.closest('div').attr('data-type'));
    if (isNaN(type)) type = 0;

    par_piece = typeof par_piece !== 'undefined' ? par_piece : 1;
    var images = [];
    $('.js_piece_item').each(function(){
        if ($(this).is(':checked'))
            images.push({
                id: $(this).closest('tr').find('.js_id_image').text().trim(),
                type: $(this).closest('tr').find('.cl_type_categorie').text().trim().trim()
            });
    });

    var releve_ext = ($('#id_releve_ext').length > 0) ? $('#id_releve_ext').val() : $('#js_zero_boost').val();

    $.ajax({
        data: {
            releve: $('#js_releve_selected').attr('data-id'),
            images:JSON.stringify(images),
            eng_tres:$('input[name="radio-type-compta"]:checked').val(),
            par_piece: par_piece,
            type: type,
            releve_ext: releve_ext
        },
        url: Routing.generate('banque2_save_imputation_piece'),
        type: 'POST',
        dataType: 'html',
        success: function(data){
            test_security(data);
            if (type === 1)
            {
                update_tr_bq_autre();
                remove_last_ui();
            }
            else update_row();
        }
    });
}

function resize_in_modal()
{
    var h = $('#modal-body').height() - 200;
    var table = $('#js_cl_tb_affecter');
    table.closest('.js_container_tb').height(h  * 2 / 3);
    updateTableGridSize(table,table.closest('.js_container_tb'),-30);

    table = $('#js_cl_tb_ecriture');
    table.closest('.js_container_tb').height(h / 3);
    updateTableGridSize(table,table.closest('.js_container_tb'),-30);

    table = $('#js_id_tb_releve');
    table.closest('.js_container_tb').height(h / 3 + 60);
    updateTableGridSize(table,table.closest('.js_container_tb'),-30);

    $('#cle_occurence').height(h - 100);
}

function charger_ecriture_temp()
{
    var table_selected = $('#js_cl_tb_ecriture'),
        w = table_selected.parent().width(),
        editurl = 'index.php';

    var datas = [],
        images_a_chargers = [];
    $('#js_cl_tb_affecter').find('tr').each(function(){
        if ($(this).hasClass('jqgfirstrow') || !$(this).find('.js_piece_item').is(':checked')) return;

        var type = parseInt($(this).find('.cl_type_categorie').text().trim());

        if (type === 0)
        {
            var dataTemps = getData($(this));
            for (var i = 0; i < dataTemps.length; i++) datas.push(dataTemps[i]);
        }
        else images_a_chargers.push($(this).find('.js_id_image').text());
    });

    if (images_a_chargers.length === 0)
    {
        set_table_jqgrid(datas,h_table / 2,get_col_model_image_affecter(undefined,true),get_col_model_image_affecter(w,true),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined,undefined,undefined,undefined,undefined);
        set_ecart('#js_cl_tb_ecriture');
        gerer_ligne_hidden();
    }
    else
    {
        $.ajax({
            data: {
                images_a_chargers: JSON.stringify(images_a_chargers)
            },
            url: Routing.generate('banque2_ecriture_banque_categorie_autre'),
            type: 'POST',
            dataType: 'json',
            success: function(data){
                datas = datas.concat(data);
                set_table_jqgrid(datas,h_table / 2,get_col_model_image_affecter(undefined,true),get_col_model_image_affecter(w,true),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined,undefined,undefined,undefined,undefined);
                set_ecart('#js_cl_tb_ecriture');
                gerer_ligne_hidden();
            }
        });
    }
}

function set_ecart(selecteur)
{
    var total_ttc_ecritures = 0;
    $(selecteur).find('.js_cl_ttc').each(function(){
        if ($(this).text().trim() !== '') total_ttc_ecritures += number_fr_to_float($(this).text());
    });

    var ecart = parseFloat((montantTTC + total_ttc_ecritures).toFixed(2));

    $('.js_ecart').removeClass('label-primary').removeClass('label-danger').text((ecart === 0) ? '0' : number_format(ecart,2,',',' ',false));
    $('.js_ecart').addClass((ecart === 0) ? 'label-primary' : 'label-danger');
}

function getData(tr_,piece)
{
    var datas = [],
        is_eng = parseInt($('input[name="radio-type-compta"]:checked').val().toString()) === 0,
        stop = false;

    var tr = tr_;
    piece = (typeof piece !== 'undefined') ? piece : true;
    var m = (piece) ? 0 : number_fr_to_float($('#js_id_m').attr('data-m')),
        tva_taux = (piece) ? 0 : parseFloat(tr.find('.js_tva_taux').text());

    var compte_b = null,
        compte_r = null,
        compte_tva = null;

    if (piece)
    {
        compte_b =
            {
                id: tr.find('.js_cl_b').find('.cl_compte_detail').attr('data-id'),
                l: tr.find('.js_cl_b').find('.cl_compte_detail').text(),
                t: tr.find('.js_cl_b').find('.cl_compte_detail').attr('data-type')
            };
        compte_r =
            {
                id: tr.find('.js_cl_r').find('.cl_compte_detail').attr('data-id'),
                l: tr.find('.js_cl_r').find('.cl_compte_detail').text(),
                t: tr.find('.js_cl_r').find('.cl_compte_detail').attr('data-type')
            };
        compte_tva =
            {
                id: tr.find('.js_cl_tva').find('.cl_compte_detail').attr('data-id'),
                l: tr.find('.js_cl_tva').find('.cl_compte_detail').text(),
                t: tr.find('.js_cl_tva').find('.cl_compte_detail').attr('data-type')
            };
    }
    else
    {
        compte_b = tr.find('.js_cl_b').find('select option:selected').text();
        compte_r = tr.find('.js_cl_r').find('select option:selected').text();
        compte_tva = tr.find('.js_cl_tva').find('select option:selected').text();
    }

    var ttc = (piece) ? number_fr_to_float(tr.find('.js_cl_ttc').text()) : m,
        mtva = (piece) ? number_fr_to_float(tr.find('.js_cl_mtva').text()) : m * tva_taux / 100,
        ht = (piece) ? number_fr_to_float(tr.find('.js_cl_ht').text()) : (m - mtva);

    //1ere ligne
    datas.push({
        ii: (piece) ? tr.find('.js_id_image').text() : $('#js_zero_boost').val(),
        i: (piece) ? tr.find('.js_show_image_').text() : 'PM',
        d: (piece) ? $('#js_releve_selected .js_cl_date_operation').text() : tr.find('.js_cl_d').text(),
        t: tr.find('.js_cl_t').text(),
        b: (is_eng) ? compte_b : null ,
        r: (is_eng) ? null : compte_r,
        tva: (is_eng) ? null : compte_tva,
        ht: (is_eng) ? 0 : -ht,
        mtva: (is_eng) ? 0 : -mtva,
        ttc: -ttc
    });

    return datas;
}

function show_image_a_affecter(span)
{
    var cle_dossier_ext = (typeof span !== 'undefined' && span.hasClass('has_cde')) ?
            span.closest('tr').find('.cl_cde_id').text() :
            $('#js_zero_boost').val();

    montantTTC = number_fr_to_float(span.closest('tr').find('.js_cl_ttc').text());
    $.ajax({
        data: {
            releve: span.closest('tr').attr('id'),
            methode:methode_dossier,
            cle_dossier_ext: cle_dossier_ext
        },
        url: Routing.generate('banque2_images_view'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var exercice = parseInt($('#exercice').val().trim());
            show_modal(data,'Images à affecter',undefined,'modal-lg');

            $('#js_id_n_1').next('label').text(exercice - 1);
            $('#js_id_n').next('label').text(exercice);
            $('#js_id_n_p_1').next('label').text(exercice + 1);
            show_pieces_a_affecter(span);
        }
    });
}

function show_pieces_a_affecter(span)
{
    var releve = (typeof span !== 'undefined') ? span.closest('tr').attr('id') : $('#js_zero_boost').val(),
        cle_dossier_ext = (typeof span !== 'undefined' && span.hasClass('has_cde')) ?
            span.closest('tr').find('.cl_cde_id').text() :
            $('#js_zero_boost').val();

    $.ajax({
        data: {
            releve: releve,
            dossier:$('#dossier').val(),
            exercice:$('#exercice').val(),
            cle_dossier_ext: cle_dossier_ext
        },
        url: Routing.generate('banque2_images_a_affecter'),
        type: 'POST',
        dataType: 'html',
        success: function(data){
            test_security(data);

            //$('#test-rp').html(data);return;

            //show_modal(data);return;

            var table_selected = $('#js_cl_tb_affecter'),
                w = table_selected.parent().width(),
                editurl = 'index.php',
                text =
                    '<div class="checkbox checkbox-inline" style="margin-top:1px;margin-left:50px">' +
                    '<input type="checkbox" class="js_piece_item" id="chk_{0}">' +
                    '<label for="chk_{0}">&nbsp;</label>' +
                    '</div>'+
                    '<span class="js_show_image_temp pointer text-primary">' + '{0}' + '</span>',
                group_object = {
                    groupField : ['g'],
                    //groupCollapse : true,
                    groupText : [ text ],
                    groupColumnShow : [false]
                };
            //set_table_jqgrid($.parseJSON(data),h_table,get_col_model_image_affecter(),get_col_model_image_affecter(w),table_selected,'hidden',w,editurl,false,undefined,true,group_object,'asc','p');
            set_table_jqgrid($.parseJSON(data),h_table,get_col_model_image_affecter(),get_col_model_image_affecter(w),table_selected,'hidden',w,editurl,false,undefined,false,undefined,undefined,undefined);
            charger_ecriture_temp();
        }
    });
}

function get_col_model_image_affecter(w,ecriture,lettrage)
{
    ecriture = (typeof ecriture !== 'undefined') ? ecriture : false;
    lettrage = (typeof lettrage !== 'undefined') ? lettrage : false;
    var colModel1 = [];
    if(typeof w !== 'undefined')
    {

        if (lettrage) colModel1.push({ name:'x', index:'x', width:w_l, hidden:ecriture, formatter: function() { return lettrage_formatter();  } });
        else colModel1.push({ name:'x', index:'x', width:w*4/100, hidden:ecriture, formatter: function() { return check_box_formatter();  } });

        colModel1.push({ name:'f', index:'f', hidden:true, classes:'js_cl_flague' });
        colModel1.push({ name:'p' , index:'p', hidden:true, sorttype:'integer', align:'right' });
        colModel1.push({ name:'g' , index:'g', hidden:true, sorttype:'integer', align:'right' });
        colModel1.push({ name:'ii', index:'ii', hidden:true, classes:'js_id_image' });
        colModel1.push({ name:'i', index:'i', width:w*10/100, classes:'js_show_image_ pointer text-primary' });
        colModel1.push({ name:'d', index:'d', width:w*((!ecriture) ? 7 : 10)/100, classes:'js_cl_d' });
        colModel1.push({ name:'t', index:'t', width:w*((!ecriture) ? 7 : 20)/100, classes:'js_cl_t' });
        colModel1.push({ name:'b', index:'b', width:w*((!ecriture) ? 7 : 10)/100, classes:'js_cl_b', formatter: function (v) { return compte_formatter(v) ; } });
        colModel1.push({ name:'r', index:'r', width:w*((!ecriture) ? 7 : 10)/100, classes:'js_cl_r', formatter: function (v) { return compte_formatter(v) ; } });
        colModel1.push({ name:'tva', index:'tva', width:w*((!ecriture) ? 7 : 10)/100, classes:'js_cl_tva', formatter: function (v) { return compte_formatter(v) ; } });
        colModel1.push({ name:'ht', index:'ht', width:w*((!ecriture) ? 8 : 10)/100,classes:'js_cl_ht', align:'right', formatter: function(v) { return number_format(v, 2, ',', ' ') } });
        colModel1.push({ name:'mtva', index:'mtva', width:w*((!ecriture) ? 7 : 10)/100,classes:'js_cl_mtva', align:'right', formatter: function(v) { return number_format(v, 2, ',', ' ') } });
        colModel1.push({ name:'ttc', index:'ttc', width:w*((!ecriture) ? 7 : 10)/100,classes:'js_cl_ttc', align:'right', formatter: function(v) { return number_format(v, 2, ',', ' ') } });
        if(!ecriture)
        {
            colModel1.push({ name:'tr', index:'tr', width:w*7/100 });
            colModel1.push({ name:'nr', index:'nr', width:w*7/100 });
            colModel1.push({ name:'dr', index:'dr', width:w*7/100 });
            colModel1.push({ name:'e', index:'e', width:w*8/100 + ((lettrage) ? -w_l : 0), classes:'js_cl_e' });
        }
        colModel1.push({ name:'sm', index:'sm', width:w*4/100, classes:'cl_similarity' });
        colModel1.push({ name:'type', index:'type', hidden:true, classes:'cl_type_categorie' });
    }
    else
    {
        colModel1.push((lettrage) ? 'L' : '');
        colModel1.push('flaguer');
        colModel1.push('p');
        colModel1.push('');
        colModel1.push('id image');
        colModel1.push('Image');
        colModel1.push((ecriture) ? 'Date rgmt' : 'Date facture');
        colModel1.push('Libellé');
        colModel1.push('Bilan');
        colModel1.push('Résultat');
        colModel1.push('Cpt. Tva');
        colModel1.push('Mt. HT');
        colModel1.push('Mt. Tva');
        colModel1.push('Mt. TTC');
        if(!ecriture)
        {
            colModel1.push('Type rgmt');
            colModel1.push('N° rgmt');
            colModel1.push('Date rgmt');
            colModel1.push('Exercice');
        }
        colModel1.push('Sim');
        colModel1.push('type');
    }
    return colModel1;
}

function check_box_formatter()
{
    return '<input type="checkbox" class="js_piece_item">'; //name="radio-piece"
}

function gerer_ligne_hidden()
{
    var exercice = parseInt($('#exercice').val().trim()),
        exercices = [],
        sens_ = $('#js_id_sens').is(':checked'),
        avec_piece_flagguer = $('#js_id_flagguer').is(':checked');

    $('.js_variation').each(function(){
        if ($(this).is(':checked'))
        {
            var variation = parseInt($(this).val().trim());
            exercices.push(exercice + variation);
        }
    });

    var montantTTCIsNeg = (montantTTC <= 0),
        montantThisIsNeg = false;
    $('#js_cl_tb_affecter').find('tr').each(function(){
        if (!$(this).hasClass('jqgfirstrow'))
        {
            var hidden = false;
            if ($(this).hasClass('jqgroup'))
            {
                if (!exercices.in_array(parseInt($(this).next('tr').find('.js_cl_e').text().trim()))) hidden = true;
                montantThisIsNeg = (parseFloat(number_fr_to_float($(this).next('tr').find('.js_cl_ttc').text().trim())) <= 0);

                //if (!sens_ && montantThisIsNeg !== montantTTCIsNeg) hidden = true;
                if (!avec_piece_flagguer && parseInt($(this).next('tr').find('.js_cl_flague').text().trim()) === 1) hidden = true;
            }
            else
            {
                montantThisIsNeg = (parseFloat(number_fr_to_float($(this).find('.js_cl_ttc').text())) <= 0);
                if (!exercices.in_array(parseInt($(this).find('.js_cl_e').text().trim()))) hidden = true;
                //if (!sens_ && montantThisIsNeg !== montantTTCIsNeg ) hidden = true;
                if (!avec_piece_flagguer && parseInt($(this).find('.js_cl_flague').text().trim()) === 1) hidden = true;
            }

            if (hidden) $(this).addClass('hidden');
            else $(this).removeClass('hidden');
        }
    });

    if ($('.js_cl_container_image_affecter').length > 1)
    {
        var typeCompta = parseInt($('input[name="radio-type-compta"]:checked').val());
        if (typeCompta === 2)
        {
            $('.js_cl_prop_cle_container').addClass('hidden');
            $('.js_cl_container_image_affecter').removeClass('hidden');
            $('#js_cl_tb_affecter').jqGrid('setGridWidth', $('#js_cl_tb_affecter').closest('.js_container_tb').width());
            $('#js_cl_tb_ecriture').jqGrid('setGridWidth', $('#js_cl_tb_ecriture').closest('.js_container_tb').width());
        }
        else
        {
            $('.js_cl_prop_cle_container').removeClass('hidden');
            $('.js_cl_container_image_affecter').addClass('hidden');
        }

        $('#js_id_prop_cle').find('tbody').find('tr').each(function(){
            //alert(parseInt($(this).attr('data-niveau').toString()));
            if (parseInt($(this).attr('data-niveau').toString()) !== 2)
            {
                var bilan_sel = $(this).find('.js_imputation_bilan'),
                    tva_sel = $(this).find('.js_imputation_tva'),
                    resultat_sel = $(this).find('.js_imputation_resultat');

                var bilan_vide = null,
                    bilan_one = null,
                    index = 0;
                bilan_sel.find('option').each(function(){
                    if ($(this).text().trim() !== '')
                    {
                        if (index === 0) bilan_one = $(this);
                        else bilan_one = null;
                        index++;
                    }
                    else bilan_vide = $(this);
                });

                var tva_vide = null,
                    tva_one = null;
                index = 0;
                tva_sel.find('option').each(function(){
                    if ($(this).text().trim() !== '')
                    {
                        if (index === 0) tva_one = $(this);
                        else tva_one = null;
                        index++;
                    }
                    else tva_vide = $(this);
                });
                var resultat_vide = null,
                    resultat_one = null;
                index = 0;
                resultat_sel.find('option').each(function(){
                    if ($(this).text().trim() !== '')
                    {
                        if (index === 0) resultat_one = $(this);
                        else resultat_one = null;
                        index++;
                    }
                    else resultat_vide = $(this);
                });

                var bilan_selected = bilan_sel.find('option:selected'),
                    tva_selected = tva_sel.find('option:selected'),
                    resultat_selected = resultat_sel.find('option:selected');
                if (typeCompta === 0)
                {
                    if (bilan_selected.text().trim() === '' && bilan_one !== null) bilan_one.prop('selected', true);
                    if (tva_vide !== null) tva_vide.prop('selected',true);
                    if (resultat_vide !== null) resultat_vide.prop('selected',true);
                }
                else
                {
                    if (bilan_vide !== null) bilan_vide.prop('selected', true);
                    if (tva_selected.text().trim() === '' && tva_one !== null) tva_one.prop('selected',true);
                    if (resultat_selected.text().trim() === '' && resultat_one !== null) resultat_one.prop('selected',true);
                }
            }
        });
    }
}
