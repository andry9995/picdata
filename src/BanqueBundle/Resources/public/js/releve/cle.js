/*$(document).on('click','.js_show_add_cle',function(){
    var releve = $(this).closest('tr').find('.js_id_releve').text();
    $.ajax({
        data: { releve: releve },
        type: 'POST',
        url: Routing.generate('banque_show_add_cle'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_modal(data,'test');
        }
    });
});*/
var tree_dossier_ready = false;
$(document).on('mouseup','.js_show_add_cle',function(){
    var selectedText = window.getSelection().toString().trim(),
        releve = $(this).closest('tr').attr('id'),
        libelle = $(this).text().trim();

    if (selectedText === '') return;

    last_action = { element: releve, type: 's', text:selectedText };
    show_edit_cle(selectedText,releve);
});

$(document).on('click','.js_cl_edit_cle',function(){
    $('.' + class_tr_edited).removeClass(class_tr_edited);
    $(this).closest('tr').addClass(class_tr_edited);
});

$(document).click(function(event) {
    var element = $(event.target);

    if (!(element.closest('.popover').length > 0 || element.hasClass('popover') || element.closest('.js_add_pcc').length > 0 || element.hasClass('js_add_pcc')))
        $('[data-toggle="popover"]').popover('hide');
});

$(document).on('click','#js_id_save_new_compte',function(){
    var is_auxilliaire = ($('#js_id_is_auxilliaire').is(':checked') ? 1 : 0),
        type = $('input[name="radio-type-tiers"]:checked').val(),
        compte = $('#js_id_compte').val().trim(),
        intitule = $('#js_id_intitule').val().trim();

    if (compte === '' || intitule === '')
    {
        show_info('ERREUR','Compte et/ou intitulé vide','error');
        return;
    }

    $.ajax({
        data: {
            is_auxilliaire: is_auxilliaire,
            type: type,
            compte: compte,
            intitule: intitule,
            dossier:  $('#dossier').val()
        },
        type: 'POST',
        url: Routing.generate('banque_save_new_compte'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            if (parseInt(data) == 0)
            {
                show_info('SUCCES','Compte bien enregistré');
                $('[data-toggle="popover"]').popover('hide');
                charger_tree_dossier();
            }
            else show_info('ERREUR','Compte déja éxistant','error');
        }
    });
});

$(document).on('click','.js_compte',function(){
    if ($(this).hasClass('compte-edited')) return;
    save_compte_in_view();

    $(this).closest('tr').find('.js_compte').each(function(){
        $(this).removeClass('fa-edit').removeClass('compte-edited').addClass('fa-square-o');
    });

    $(this).removeClass('fa-square-o').addClass('fa-edit').addClass('compte-edited');

    charger_tree();
});

$(document).on('click','.js_save_compte',function(){
    save_compte_in_view();
});

$(document).on('click','.js_cl_banque_type',function(){
    if ($(this).hasClass('active')) return;

    $('.compte-edited').attr('data-pcgs','[]').attr('data-pccs','[]');
    $('#td_' + $('.compte-edited').closest('td').attr('data-type')).empty();
    $('.js_cl_banque_type').removeClass('active');
    $(this).addClass('active');
    $(this).closest('.btn-group').find('.dropdown-toggle').text($(this).text());
    charger_tree();
});

$(document).on('click','.js_annuler_imputation',function(){
    var btn = $(this),
        releve = $(this).closest('tr').attr('id'),
        type = parseInt($(this).closest('span.type').attr('data-type'));

    $('.' + class_tr_edited).removeClass(class_tr_edited);
    $(this).closest('tr').addClass(class_tr_edited);

    annuler_imputation(btn,releve,type);
});

$(document).on('click','input[name="radio_type_compta"]',function() {
    var typeCompta = parseInt($(this).val().trim());
    $('#js_id_prop_cle').find('tbody').find('tr').each(function(){
        //alert(parseInt($(this).attr('data-niveau').toString()));
        if (parseInt($(this).attr('data-niveau').toString()) != 2)
        {
            var bilan_sel = $(this).find('.js_imputation_bilan'),
                tva_sel = $(this).find('.js_imputation_tva'),
                resultat_sel = $(this).find('.js_imputation_resultat');

            var bilan_vide = null,
                bilan_one = null,
                index = 0;
            bilan_sel.find('option').each(function(){
                if ($(this).text().trim() != '')
                {
                    if (index == 0) bilan_one = $(this);
                    else bilan_one = null;
                    index++;
                }
                else bilan_vide = $(this);
            });

            var tva_vide = null,
                tva_one = null;
            index = 0;
            tva_sel.find('option').each(function(){
                if ($(this).text().trim() != '')
                {
                    if (index == 0) tva_one = $(this);
                    else tva_one = null;
                    index++;
                }
                else tva_vide = $(this);
            });
            var resultat_vide = null,
                resultat_one = null;
            index = 0;
            resultat_sel.find('option').each(function(){
                if ($(this).text().trim() != '')
                {
                    if (index == 0) resultat_one = $(this);
                    else resultat_one = null;
                    index++;
                }
                else resultat_vide = $(this);
            });

            var bilan_selected = bilan_sel.find('option:selected'),
                tva_selected = tva_sel.find('option:selected'),
                resultat_selected = resultat_sel.find('option:selected');
            if (typeCompta == 0)
            {
                if (bilan_selected.text().trim() == '' && bilan_one != null) bilan_one.prop('selected', true);
                if (tva_vide != null) tva_vide.prop('selected',true);
                if (resultat_vide != null) resultat_vide.prop('selected',true);
            }
            else
            {
                if (bilan_vide != null) bilan_vide.prop('selected', true);
                if (tva_selected.text().trim() == '' && tva_one != null) tva_one.prop('selected',true);
                if (resultat_selected.text().trim() == '' && resultat_one != null) resultat_one.prop('selected',true);
            }
        }
    });
});

$(document).on('click','.js_save_cle',function(){
    $('.js_save_compte').click();
    save_cle($(this));
});

$(document).on('click','.js_show_cle_a_affecter',function(){
    $('.image_a_affecter').removeClass('image_a_affecter');
    $(this).addClass('image_a_affecter');

    last_action = { element: $(this), type: 'c', text:'' };
    show_affecation_cle($(this));
});

$(document).on('change','.js_check_cle',function(){
    if ($(this).is(':checked'))
    {
        $('.js_check_cle').each(function(){
            $(this).prop('checked',false);
        });

        $(this).prop('checked',true);
    }
});

$(document).on('click','.js_save_imputation_cle',function(){
    save_imputation_cle();
});

$(document).on('change','#js_id_is_auxilliaire',function(){
    if ($(this).is(':checked')) $('#container_radio_auxilliaire').removeClass('hidden');
    else  $('#container_radio_auxilliaire').addClass('hidden');
});

$(document).on('click','.js_show_image_multiple',function(){
    show_image_pop_up($(this).closest('tr').attr('data-id'));
});

$(document).on('keyup', '.search-input', function(){
    var searchString = $(this).val();
    $('#js_id_tree_pcc').jstree('search', searchString);
});

function save_compte_in_view(load_pcc)
{
    load_pcc = (typeof load_pcc === 'undefined') ? true : load_pcc;

    var element_compte = $('.compte-edited'),
        pcgsClickeds = $('#js_id_tree_pcg').jstree().get_checked(),
        pccsClickeds = (load_pcc) ? $('#js_id_tree_pcc').jstree().get_checked() : [];

    if (pcgsClickeds.length === 0) element_compte.closest('td').addClass('text-danger');
    else element_compte.closest('td').removeClass('text-danger');

    element_compte
        .attr('data-pcgs',JSON.stringify(pcgsClickeds))
        .attr('data-pccs',JSON.stringify(pccsClickeds));

    //--------
    var i,
        text = '',
        text_pcc = '',
        type = parseInt(element_compte.closest('td').attr('data-type'));

    if (pcgsClickeds.length > 0)
    {
        text = '';
        for (i = 0; i < pcgsClickeds.length; i++) text += '[' + $('#js_id_tree_pcg').jstree(true).get_node(pcgsClickeds[i]).text + ']&nbsp;';
        if (pccsClickeds.length > 0) text += '<br/>';
    }
    /*if (pccsClickeds.length > 0)
    {
        text += '<strong>PCC</strong>:&nbsp;';
        for (i = 0; i < pccsClickeds.length; i++) text += '[' + $('#js_id_tree_pcc').jstree(true).get_node(pccsClickeds[i]).text + ']&nbsp;';
    }*/

    if (pccsClickeds.length > 0)
    {
        text_pcc += '<select style="margin:0!important;border:none!important;width: 100%!important;" class="sel_'+ ((type == 2) ? 0 : 1) +'">' +
            '<option value="0#0">--Selectionner--</option>';
        for (i = 0; i < pccsClickeds.length; i++)
        {
            var node = $('#js_id_tree_pcc').jstree(true).get_node(pccsClickeds[i]);
            text_pcc += '<option value="'+ node.id +'" '+ ((pccsClickeds.length == 1) ? 'selected' : '') +'>'+ node.text +'</option>';
        }
        text_pcc += '</select>';
    }

    $('#td_'+ type).html(text);
    $('#td_'+ type +'_pcc').html(text_pcc);

    charger_cle_dossier();
}

function save_imputation_cle()
{
    var tr_selected = $('.js_check_cle:checked').closest('tr'),
        type_compta = parseInt($('input[name="radio-type-compta"]:checked').val());

    if (type_compta !== 2)
    {
        if (tr_selected.length === 0)
        {
            show_info('Erreur','Selectionner votre imputation','error');
            return;
        }

        if ( tr_selected.find('.js_imputation_resultat').find('option:selected').text().trim() === '' &&
            tr_selected.find('.js_imputation_tva').find('option:selected').text().trim() === '' &&
            tr_selected.find('.js_imputation_bilan').find('option:selected').text().trim() === '')
        {
            show_info('Erreur','Pas d imputation','error');
            return;
        }
    }
    else
    {
        var images = [];
        $('.js_piece_item').each(function(){
            if ($(this).is(':checked')) images.push($(this).closest('tr').next('tr').find('.js_id_image').text().trim());
        });

        if (images.length === 0) show_info('Erreur','Pas d IMAGE selectionnée','error');
        else save_imputation_piece(2);
        return;
    }

    var resultat = tr_selected.find('.js_imputation_resultat').val(),
        tva = tr_selected.find('.js_imputation_tva').val(),
        bilan = tr_selected.find('.js_imputation_bilan').val(),
        releve =  $('#js_releve_selected').attr('data-id').trim(),
        cle = tr_selected.attr('data-id').trim(),
        resultat_type = parseInt(tr_selected.find('.js_imputation_resultat').find('option:selected').attr('data-type')),
        tva_type = parseInt(tr_selected.find('.js_imputation_tva').find('option:selected').attr('data-type')),
        bilan_type = parseInt(tr_selected.find('.js_imputation_bilan').find('option:selected').attr('data-type'));

    $.ajax({
        data: {
            'resultat': resultat,
            'bilan' : bilan,
            'tva' : tva,
            'resultat_type' : resultat_type,
            'bilan_type' : bilan_type,
            'tva_type' : tva_type,
            'releve' : releve,
            'cle' : cle,
            'dossier' : $('#dossier').val(),
            'type_compta' : type_compta
        },
        type: 'POST',
        url: Routing.generate('banque_save_imputation_cle'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            if (parseInt(data) === 0)
            {
                show_info('Succes','Ligne bien imputée par Clé');
                close_modal();
                scroll_position = $('#gbox_js_id_releve_liste').find('.ui-jqgrid-bdiv').scrollTop();
                charger_analyse();
            }
            else show_info('Erreur','Une erreur c est produite pendant l imputation','error');
        }
    });
}

function show_affecation_cle(span)
{
    montantTTC = number_fr_to_float(span.closest('tr').find('.js_cl_ttc').text());
    $.ajax({
        data: { releve: span.closest('tr').attr('id'), exercice:$('#exercice').val() },
        type: 'POST',
        url: Routing.generate('banque_cle_propositions'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            /*show_modal(data,'Affectation CLE',undefined,'modal-lg');
            return;*/
            show_modal(data,'Affectation CLE',undefined,'modal-lg');
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
                },
                exercice = parseInt($('#exercice').val().trim());
            show_modal(data,'Images à affecter',undefined,'modal-lg');

            $('#js_id_n_1').next('label').text(exercice - 1);
            $('#js_id_n').next('label').text(exercice);
            $('#js_id_n_p_1').next('label').text(exercice + 1);

            //set_table_jqgrid($.parseJSON(data),h_table,get_col_model_image_affecter(),get_col_model_image_affecter(w),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined);
            //set_table_jqgrid($.parseJSON(data),h_table,get_col_model_image_affecter(),get_col_model_image_affecter(w),table_selected,'hidden',w,editurl,false,undefined,false,undefined,undefined,undefined);
            set_table_jqgrid($.parseJSON(table_selected.closest('.js_container_tb').attr('data-datas')),h_table,get_col_model_image_affecter(),get_col_model_image_affecter(w),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined);
            charger_ecriture_temp();
        }
    });
}

function charger_tree(asynch)
{
    asynch = typeof asynch !== 'undefined' ? asynch : true;
    var type_compte = parseInt($('.compte-edited').closest('td').attr('data-type').toString()),
        banque_type = $('#js_id_type_compta').find('li.active').attr('data-id');

    $('#js_id_tree_pcg_container').html('<div id="js_id_tree_pcg"></div>');
    var element = $('#js_id_tree_pcg');
    element.empty();

    $.ajax({
        data: { type_compte:type_compte, banque_type:banque_type, pcgs_selecteds:$('.compte-edited').attr('data-pcgs') },
        type: 'POST',
        url: Routing.generate('banque_cle_pcg'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        async: asynch,
        dataType: 'html',
        success: function(data) {
            test_security(data);
            tree_dossier_ready = false;
            element.jstree({
                'core' : { 'data' : $.parseJSON(data) } ,
                'checkbox' : { 'keep_selected_style' : false },
                'plugins' : [ "wholerow", "checkbox", 'real_checkboxes' ]})
                .on('changed.jstree', function () {
                    charger_tree_dossier();
                })
                .on('ready.jstree', function(){
                    tree_dossier_ready = true;
                    charger_tree_dossier();
                });
        }
    });
}

function charger_tree_dossier()
{
    if (!tree_dossier_ready) return;

    var pcgsClickeds = $('#js_id_tree_pcg').jstree().get_checked();

    $('#js_id_tree_pcc_container').html('<div id="js_id_tree_pcc"></div>');
    var element = $('#js_id_tree_pcc');
    $.ajax({
        data: { pcgs:JSON.stringify(pcgsClickeds), dossier:$('#dossier').val(), pccs_selecteds:$('.compte-edited').attr('data-pccs') },
        type: 'POST',
        url: Routing.generate('banque_cle_pcc'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            element.jstree({
                'core' : { 'data' : $.parseJSON(data) } ,
                'checkbox' : { 'keep_selected_style' : false },
                'plugins' : [ 'checkbox','search' ],
                'search': {
                    'case_sensitive': false,
                    'show_only_matches': true
                }
            })
            .on('changed.jstree', function () {
                save_compte_in_view($.parseJSON(data).length > 0);
            })
            .on('ready.jstree', function(){
                save_compte_in_view($.parseJSON(data).length > 0);
            });

            if ($.parseJSON(data).length > 0) $('.js_add_pcc').removeClass('hidden');
            else $('.js_add_pcc').addClass('hidden');
        }
    });
}

function save_cle(span)
{
    var cle = $('#js_id_cle_libelle').val().trim(),
        bilans = { pcgs: $.parseJSON($('.js_compte_b').attr('data-pcgs')), pccs:$.parseJSON($('.js_compte_b').attr('data-pccs')) },
        tvas = { pcgs: $.parseJSON($('.js_compte_t').attr('data-pcgs')), pccs:$.parseJSON($('.js_compte_t').attr('data-pccs')) },
        resultats = { pcgs: $.parseJSON($('.js_compte_r').attr('data-pcgs')), pccs:$.parseJSON($('.js_compte_r').attr('data-pccs')) },
        pcgsResParametre = true,
        pcgsTvaParametre = true,
        pcgsBilanParametre = true,
        pas_piece = $('#jd_id_pas_piece').is(':checked') ? 1 : 0,
        type = parseInt(span.attr('data-type')),
        banque_type = $('#js_id_type_compta').find('li.active').attr('data-id').trim(),
        type_compta = parseInt($('input[name="radio-type-compta"]:checked').val()),
        taux_tva = parseFloat($('#js_id_taux_tva').val());

    if (cle === '')
    {
        show_info('ERREUR','CLE VIDE','error');
        return;
    }

    var mess = '';
    if (type !== 2)
    {
        //0: resultat; 1:tva; 2 : bilan
        var counts_object = banque_type_status[$('#js_id_type_compta').find('li.active').attr('data-id-uncrypted')],
            count_res = parseInt((counts_object.c)[0]),
            count_tva = parseInt((counts_object.c)[1]),
            count_bilan = parseInt((counts_object.c)[2]);

        $('.js_compte').each(function(){
            if (parseInt($(this).closest('td').attr('data-type')) === 0 && count_res > 0 && $.parseJSON($(this).attr('data-pcgs')).length === 0)
                pcgsResParametre = false;
            if (parseInt($(this).closest('td').attr('data-type')) === 1 && count_tva > 0 && $.parseJSON($(this).attr('data-pcgs')).length === 0)
                pcgsTvaParametre = false;
            if (parseInt($(this).closest('td').attr('data-type')) === 2 && count_bilan > 0 && $.parseJSON($(this).attr('data-pcgs')).length === 0)
                pcgsBilanParametre = false;
        });

        var nb_error = 0;
        if (!pcgsResParametre || !pcgsTvaParametre || !pcgsBilanParametre)
        {
            if (!pcgsBilanParametre)
            {
                mess += 'Compte BILAN du pcg non parametré\n';
                nb_error++;
            }
            if (!pcgsTvaParametre)
            {
                mess += 'Compte TVA du pcg non parametré\n';
                nb_error++;
            }
            if (!pcgsResParametre)
            {
                mess += 'Compte RESULTAT du pcg non parametré';
                nb_error++;
            }

            if (nb_error === 3)
            {
                show_info('ERREUR','Comptes non parametés','error');
                return;
            }
        }

        var v_default = '0#0',
            is_eng = parseInt($('input[name="radio-type-compta"]:checked').val()) === 0,
            select_b = $('#td_2_pcc').find('select'),
            bilan = (select_b.length > 0 && !select_b.hasClass('hidden')) ? select_b.val() : v_default,
            select_t = $('#td_1_pcc').find('select'),
            tva = (select_t.length > 0 && !select_t.hasClass('hidden')) ? select_t.val() : v_default,
            select_r = $('#td_0_pcc').find('select'),
            res = (select_r.length > 0 && !select_r.hasClass('hidden')) ? select_r.val() : v_default;

        if (type === 1 && (is_eng && bilan === v_default || !is_eng && res === v_default) && type_compta !== 2)
        {
            show_info('ERREUR','Comptes du DOSSIER non parametrés','error');
            return;
        }

        if (isNaN(taux_tva))
        {
            show_info('ERREUR','Le Taux doit etre un nombre','error');
            return;
        }
    }

    if (parseInt($('#js_id_cle_a_modifier').attr('data-is_edit').trim()) === 1 || mess !== '')
    {
        var titre = (mess === '') ? 'Voulez-vous vraiment '+ ((type === 2) ? 'SUPPRIMER' : 'MODIFIER') +' cette CLE ?' : 'Voulez-vous quand même enregistrer ?',
            text = (mess === '') ? 'Toutes les IMPUTATIONS reliées à cette CLE seront réinitialisées !!!' : mess;

        swal({
            title: titre,
            text: text,
            type: 'question',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui',
            cancelButtonText: 'Annuler'
        }).then(function () {
            save_cle_ajax(cle,bilans,tvas,resultats,pas_piece,type,bilan,tva,res,banque_type,type_compta);
        }, function (dismiss) {
            if (dismiss === 'cancel') {
            }
        });
    }
    else save_cle_ajax(cle,bilans,tvas,resultats,pas_piece,type,bilan,tva,res,banque_type,type_compta);
}

function save_cle_ajax(cle,bilans,tvas,resultats,pas_piece,type,bilan,tva,res,banque_type,type_compta)
{
    $.ajax({
        data: {
            cle: cle,
            banques: $('#id_banques').val(),
            taux_tva: parseInt($('#js_id_taux_tva').val().trim()),
            dossier: $('#dossier').val(),
            bilans: JSON.stringify(bilans),
            tvas: JSON.stringify(tvas),
            resultats: JSON.stringify(resultats),
            pas_piece: pas_piece,
            type: type,
            bilan: bilan,
            tva: tva,
            res: res,
            banque_type: banque_type,
            cle_id: $('#js_id_cle_a_modifier').val(),
            type_compta: type_compta,
        },
        type: 'POST',
        url: Routing.generate('banque_cle_save'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            if (parseInt(data) !== 0)
            {
                if (parseInt(data) === 10 || parseInt(data) === 11)
                {
                    show_info('MODIFICATION BIEN ENREGISTREE','REINITIALISATION DE LA CLE');
                    close_modal();
                    scroll_position = $('#gbox_js_id_releve_liste').find('.ui-jqgrid-bdiv').scrollTop();
                    charger_analyse();
                }
                else show_info('ERREUR','CETTE CLE EXISTE DEJA','error');
                return;
            }
            show_info('SUCCES','NOUVELLE CLE BIEN ENREGISTREE');
            close_modal();
            scroll_position = $('#gbox_js_id_releve_liste').find('.ui-jqgrid-bdiv').scrollTop();
            charger_analyse();
            return;
            $('#test_cle').html(data);
        }
    });
}

function charger_cle_dossier()
{
    var typeCompta = parseInt($('input[name="radio-type-compta"]:checked').val());
    if (typeCompta === 0)
    {
        $('.sel_0').removeClass('hidden');
        $('.sel_1').addClass('hidden');
    }
    else
    {
        $('.sel_0').addClass('hidden');
        $('.sel_1').removeClass('hidden');
    }

    if (typeCompta === 2) $('#js_id_save_and_propage').addClass('hidden');
    else $('#js_id_save_and_propage').removeClass('hidden');
}

function annuler_imputation(btn,releve,type)
{
    $.ajax({
        data: { releve:releve, type:type },
        type: 'POST',
        url: Routing.generate('banque_annuler_imputation'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            if (parseInt(data) === 0)
            {
                update_row();

                /*if (btn.closest('tr').find('.js_status_hidden').text().trim() !== '')
                {
                    var html = btn.closest('tr').find('.js_status_hidden').html();
                    btn.closest('td')
                        .attr('title',html)
                        .html(html);
                    return;
                }

                if (type === 0 || type === 2)
                {
                    btn.closest('td')
                        .attr('title','<span class="text-warning pointer js_show_cle_a_affecter">Cl&eacute;s&nbsp;&agrave;&nbsp;valider</span>')
                        .html('<span class="text-warning pointer js_show_cle_a_affecter">Cl&eacute;s&nbsp;&agrave;&nbsp;valider</span>');
                }
                else if (type === 1)
                {
                    btn.closest('td')
                        .attr('title','<span class="text-danger pointer js_show_image_a_affecter pointer">Pi&egrave;ce&nbsp;&agrave;&nbsp;affecter</span>')
                        .html('<span class="text-danger pointer js_show_image_a_affecter pointer">Pi&egrave;ce&nbsp;&agrave;&nbsp;affecter</span>');
                }

                btn.closest('tr').find('td[aria-describedby="js_id_releve_liste_t"]').find('select').val(0).attr('values',0);
                btn.closest('tr').find('td[aria-describedby="js_id_releve_liste_c"]').find('select').val(0).attr('values',0);
                btn.closest('tr').find('td[aria-describedby="js_id_releve_liste_tva"]').find('select').val(0).attr('values',0);*/

                //show_info('SUCCES','Imputation Annulée avec succes');
            }
            else show_info('ERREUR','Une Erreur c est produite pendant l enregistrement','error');
        }
    });
}

function loadCompleteJQgrid()
{
    $(function() {
        $.contextMenu({
            selector: '.js_cl_edit_cle',
            trigger: 'left',
            callback: function(key, options) {
                var releve = $(this).closest('tr').attr('id'),
                    cle = $(this).attr('data-id');
                if (key === 'delete') annuler_imputation($(this),releve,0);
                else if (key === 'edit') show_edit_cle('',releve,cle);
            },
            items: {
                'delete': {name: "Supprimer la CLE pour cette ligne", icon: 'delete'},
                'edit': {name: 'Modifier la CLE', icon: 'edit'}
            }
        });

        $('.js_cl_edit_cle').on('click', function(e){
            console.log('clicked', this);
        })
    });
}

function show_edit_cle(selectedText,releve,cle_id)
{
    cle_id = (typeof cle_id === 'undefined') ? $('#js_zero_boost').val() : cle_id;
    $.ajax({
        data: { cle: selectedText, compte:$('#js_banque_compte').val(), releve:releve, dossier:$('#dossier').val(), cle_id:cle_id },
        type: 'POST',
        url: Routing.generate('banque_show_add_cle'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);

            show_modal(data,'Insertion nouvelle CLE',undefined,'modal-lg');
            $("[data-toggle=popover]").popover({html:true});

            var config = {
                '.chosen-select'           : {},
                '.chosen-select-deselect'  : {allow_single_deselect:true},
                '.chosen-select-no-single' : {disable_search_threshold:10},
                '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
                '.chosen-select-width'     : {width:'95%'}
            };
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }

            /*$('.compte-edited').addClass('compte-edited-save');
            $('#jd_id_tr_container').find('.js_compte').each(function(){
                if (!$(this).hasClass('compte-edited-save'))
                {
                    $('.compte-edited').removeClass('compte-edited');
                    $(this).addClass('compte-edited');
                    charger_tree(false);
                }
            });
            $('.compte-edited-save').addClass('compte-edited').removeClass('compte-edited-save');*/
            charger_tree();
        }
    });
}