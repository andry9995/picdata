/**
 * Created by SITRAKA on 01/08/2018.
 */

var tree_dossier_ready = false;

$(document).ready(function(){
    $(document).on('click','.js_cl_edit_cle',function(){
        $('.' + class_tr_edited).removeClass(class_tr_edited);
        $(this).closest('tr').addClass(class_tr_edited);
    });

    $(document).on('mouseup','.js_show_add_cle',function(){
        var selectedText = window.getSelection().toString().trim(),
            releve = $(this).closest('tr').attr('id'),
            libelle = $(this).text().trim();

        if (selectedText === '' || !usc) return;

        return; //modif ***

        last_action = { element: releve, type: 's', text:selectedText };
        show_edit_cle(selectedText,releve);
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

    $(document).on('click','.js_compte',function(){
        if ($(this).hasClass('compte-edited')) return;
        save_compte_in_view();

        $(this).closest('tr').find('.js_compte').each(function(){
            $(this).removeClass('fa-edit').removeClass('compte-edited').addClass('fa-square-o');
        });

        $(this).removeClass('fa-square-o').addClass('fa-edit').addClass('compte-edited');

        charger_tree();
    });

    $(document).on('click','.js_save_cle',function(){
        $('.js_save_compte').click();
        save_cle($(this));
    });

    $(document).on('click','.js_save_compte',function(){
        save_compte_in_view();
    });

    $(document).on('keyup', '.search-input', function(){
        var searchString = $(this).val();
        $('#js_id_tree_pcc').jstree('search', searchString);
    });

    $(document).on('click','.js_show_cle_a_affecter',function(){
        return; //modif ***

        $('.image_a_affecter').removeClass('image_a_affecter');
        $(this).addClass('image_a_affecter');

        $('.' + class_tr_edited).removeClass(class_tr_edited);
        $(this).closest('tr').addClass(class_tr_edited);

        last_action = { element: $(this), type: 'c', text:'' };
        show_affecation_cle($(this));
    });

    $(document).on('click','.cl_image_multiple_bsca',function(){
        var id = $(this).attr('data-image_flague');
        $.ajax({
            data: { id:id, releve: $('#js_zero_boost').val() },
            type: 'POST',
            url: Routing.generate('banque2_imputation_items'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                modal_ui({ modal: false, resizable: true,title: 'Rapprochement' },data, false, 0.8);
            }
        });
    });

    $(document).on('change','#js_id_is_auxilliaire',function(){
        if ($(this).is(':checked')) $('#container_radio_auxilliaire').removeClass('hidden');
        else  $('#container_radio_auxilliaire').addClass('hidden');
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
                if (parseInt(data) === 0)
                {
                    show_info('SUCCES','Compte bien enregistré');
                    $('[data-toggle="popover"]').popover('hide');
                    charger_tree_dossier();
                }
                else show_info('ERREUR','Compte déja éxistant','error');
            }
        });
    });
});

/**
 *
 * @param selectedText
 * @param releve
 * @param cle_id
 * @param tous_dossier  --tous dossier = 1; 0 sinon
 */
function show_edit_cle(selectedText,releve,cle_id,tous_dossier)
{
    cle_id = (typeof cle_id === 'undefined') ? $('#js_zero_boost').val() : cle_id;
    tous_dossier = (typeof tous_dossier === 'undefined') ? 2 : tous_dossier;

    $.ajax({
        data: {
            cle: selectedText,
            banque:$('#js_banque').val(),
            compte:$('#js_banque_compte').val(),
            releve:releve,
            dossier:$('#dossier').val(),
            cle_id:cle_id,
            tous_dossier: tous_dossier,
            methode: methode_dossier
        },
        type: 'POST',
        url: Routing.generate('banque2_show_add_cle'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);

            show_modal(data,'Insertion nouvelle CLE',undefined,'modal-x-lg');
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

            charger_tree();
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
            //$('#test_banque').html(data);return;
            element
                .jstree({
                'core' : { 'data' : $.parseJSON(data) } ,
                'checkbox' : { 'keep_selected_style' : false },
                'plugins' : [ 'checkbox','search' ],
                'search': {
                    'case_sensitive': false,
                    'show_only_matches': true
                }})
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
        text_pcc += '<select style="margin:0!important;border:none!important;width: 100%!important;" class="sel_type_'+type+' sel_'+ ((type === 2) ? 0 : 1) +'">' +
            '<option value="0#0">--Selectionner--</option>';
        for (i = 0; i < pccsClickeds.length; i++)
        {
            var node = $('#js_id_tree_pcc').jstree(true).get_node(pccsClickeds[i]);
            text_pcc += '<option value="'+ node.id +'" '+ ((pccsClickeds.length === 1) ? 'selected' : '') +'>'+ node.text +'</option>'; //
        }
        text_pcc += '</select>';
    }

    $('#td_'+ type).html(text);
    $('#td_'+ type +'_pcc').html(text_pcc);

    charger_cle_dossier();
}

function charger_cle_dossier()
{
    var typeCompta = parseInt($('input[name="radio-type-compta"]:checked').val());
    if (typeCompta === 0)
    {
        $('.sel_0').removeClass('hidden');

        if ($.parseJSON($('.js_compte_t').attr('data-pccs')).length > 0) $('.sel_type_1').removeClass('hidden');
        else $('.sel_type_1').addClass('hidden');

        if ($.parseJSON($('.js_compte_r').attr('data-pccs')).length > 0) $('.sel_type_0').removeClass('hidden');
        else $('.sel_type_0').addClass('hidden');
    }
    else
    {
        $('.sel_0').addClass('hidden');
        $('.sel_1').removeClass('hidden');
    }

    if (typeCompta === 2) $('#js_id_save_and_propage').addClass('hidden');
    else $('#js_id_save_and_propage').removeClass('hidden');
}

function save_cle(span)
{
    var cle = $('#js_id_cle_libelle').val().trim(),
        bilans = { pcgs: $.parseJSON($('.js_compte_b').attr('data-pcgs')), pccs:$.parseJSON($('.js_compte_b').attr('data-pccs')) },
        tvas = { pcgs: $.parseJSON($('.js_compte_t').attr('data-pcgs')), pccs:$.parseJSON($('.js_compte_t').attr('data-pccs')) },
        resultats = { pcgs: $.parseJSON($('.js_compte_r').attr('data-pcgs')), pccs:$.parseJSON($('.js_compte_r').attr('data-pccs')) },
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

    var mess = '',
        pcc = { r:'0#0', t:'0#0', b:'0#0' };
    if (type !== 2)
    {
        if (bilans.pcgs.length === 0 || tvas.pcgs.length === 0 || resultats.pcgs.length === 0)
        {
            if (bilans.pcgs.length === 0 && type_compta === 0) mess += 'Compte BILAN du pcg non parametré; \n';
            if (tvas.pcgs.length === 0 && type_compta > 0) mess += 'Compte TVA du pcg non parametré; \n';
            if (resultats.pcgs.length === 0 && type_compta > 0) mess += 'Compte RESULTAT du pcg non parametré';
            if (bilans.pcgs.length === 0 && tvas.pcgs.length === 0 && resultats.pcgs.length === 0)
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

        pcc = { r:res, b:bilan, t:tva };

        if (type === 1 && type_compta !== 2 && (is_eng && bilan === v_default || !is_eng && res === v_default))
        {
            show_info('ERREUR','Comptes du DOSSIER non parametrés','error');
            return;
        }

        if (isNaN(taux_tva) && tvas.pcgs.length !== 0)
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
            save_cle_ajax(cle,bilans,tvas,resultats,pas_piece,type,bilan,tva,res,banque_type,type_compta,pcc);
        }, function (dismiss) {
            if (dismiss === 'cancel') {
            }
        });
    }
    else save_cle_ajax(cle,bilans,tvas,resultats,pas_piece,type,bilan,tva,res,banque_type,type_compta,pcc);
}

function save_cle_ajax(cle,bilans,tvas,resultats,pas_piece,type,bilan,tva,res,banque_type,type_compta,pcc)
{
    var taux_tva = parseInt($('#js_id_taux_tva').val().trim());
    if (isNaN(taux_tva)) taux_tva = 0;

    var tous_dossier = $('#js_spec_for_dossier').is(':checked') ? 1 : 0;
    $.ajax({
        data: {
            cle: cle,
            banques: $('#id_banques').val(),
            taux_tva: taux_tva,
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
            tous_dossier: tous_dossier,
            pcc: JSON.stringify(pcc)
        },
        type: 'POST',
        url: Routing.generate('banque2_cle_save'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            //0: ajout; 1:erreur; 2: modification; 3:supprimer

            if (parseInt(data) === 1)
            {
                show_info('ERREUR','CETTE CLE EXISTE DEJA','error');
                return;
            }
            else if (parseInt(data) === 1) show_info('SUCCES','NOUVELLE CLE BIEN ENREGISTREE');
            else if (parseInt(data) === 2) show_info('MODIFICATION BIEN ENREGISTREE','REINITIALISATION DE LA CLE');
            else if (parseInt(data) === 3) show_info('MODIFICATION BIEN ENREGISTREE','REINITIALISATION DE LA CLE');

            close_modal();
            scroll_position = $('#gbox_js_id_releve_liste').find('.ui-jqgrid-bdiv').scrollTop();
            charger_analyse();

            return;
            $('#test_cle').html(data);
        }
    });
}

function show_affecation_cle(span,cle)
{
    cle = typeof hidden === 'undefined' ? $('#js_zero_boost').val() : cle;
    montantTTC = number_fr_to_float(span.closest('tr').find('.js_cl_ttc').text());
    $.ajax({
        data: {
            releve: span.closest('tr').attr('id'),
            exercice:$('#exercice').val(),
            cle: cle,
            methode: methode_dossier
        },
        type: 'POST',
        url: Routing.generate('banque2_cle_propositions'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_modal(data,'Affectation par CLE',undefined,'modal-lg');
            $('input[name="radio-type-compta"]').change();
            $('#id_cle_container').find('.cl_btn_cle.btn-primary').click();
            //charger_cle_props();
        }
    });
}

function loadCompleteJQgrid()
{
    $(function() {

        if (usc)
        {
            $.contextMenu({
                selector: '.js_cl_edit_cle',
                trigger: 'left',
                callback: function(key, options) {
                    var releve = $(this).closest('tr').attr('id'),
                        cle = $(this).attr('data-id'),
                        tous_dossier = parseInt($(this).attr('data-tous_dossier'));

                    if (key === 'delete') annuler_imputation(releve,0);
                    else if (key === 'edit')
                    {
                        /*if (tous_dossier === 0) show_affecation_cle($(this),cle);
                        else show_edit_cle('',releve,cle, tous_dossier);*/

                        show_edit_cle_new('', releve, cle);
                    }
                    else if (key === 'edit_i')
                    {
                        $('.' + class_tr_edited).removeClass(class_tr_edited);
                        $(this).closest('tr').addClass(class_tr_edited);
                        show_imputation_manuel(releve);
                    }
                },
                items: {
                    'delete': {name: "Supprimer la CLE pour cette ligne", icon: 'delete'},
                    'edit_i': {name: "Modifier l'imputation", icon: 'edit'},
                    'edit': {name: 'Modifier la CLE', icon: 'edit'}
                }
            });
            $('.js_cl_edit_cle').on('click', function(e){
                console.log('clicked', this);
            });
        }


        var items = { 'show_image': {name: "Afficher l'image", icon: 'paste'} };

        if (usc)
        {
            items.edit = {name: "Modifier l'imputation", icon: 'edit'};
            items.delete = {name: "Annuler l'imputation", icon: 'delete'};
        }
        $.contextMenu({
            selector: '.cl_detail_imputation',
            trigger: 'left',
            callback: function(key, options) {
                var type = parseInt($(this).attr('data-type')),
                    id_releve = $(this).closest('tr').attr('id');

                if (key === 'show_image')
                {
                    if (type === 0)
                    {
                        show_image_pop_up($(this).attr('data-it'));
                    }
                    else if (type === 1 || type === 2)
                    {
                        var id = $(this).attr('data-id');
                        $.ajax({
                            data: { id:id, releve:id_releve },
                            type: 'POST',
                            url: Routing.generate('banque2_imputation_items'),
                            contentType: "application/x-www-form-urlencoded;charset=utf-8",
                            beforeSend: function(jqXHR) {
                                jqXHR.overrideMimeType('text/html;charset=utf-8');
                            },
                            dataType: 'html',
                            success: function(data) {
                                test_security(data);
                                show_modal(data,'Rapprochement');
                            }
                        });
                    }
                    else if (type === 3)
                    {
                        $('.' + class_tr_edited).removeClass(class_tr_edited);
                        $(this).closest('tr').addClass(class_tr_edited);

                        index_autre++;
                        var ind = index_autre;
                        $.ajax({
                            data: { releve: id_releve },
                            type: 'POST',
                            url: Routing.generate('banque2_sous_categorie_autres_details'),
                            contentType: "application/x-www-form-urlencoded;charset=utf-8",
                            beforeSend: function(jqXHR) {
                                jqXHR.overrideMimeType('text/html;charset=utf-8');
                            },
                            dataType: 'json',
                            success: function(data) {
                                show_modal('<table id="id_detail_banque_autre"></table>','Détails',undefined,'modal-x-lg');
                                var table_selected = $('#id_detail_banque_autre'),
                                    w = table_selected.parent().width(),
                                    h = $(window).height() - 300,
                                    tot_debit = 0,
                                    tot_credit = 0;

                                jQuery('#id_detail_banque_autre').jqGrid({
                                    data: data,
                                    datatype: 'local',
                                    height: h,
                                    width: w,
                                    rowNum: 10000000,
                                    rowList: [10,20,30],
                                    colNames:col_model_banque_autre(),
                                    colModel:col_model_banque_autre(w),
                                    viewrecords: true,
                                    footerrow: true,
                                    userDataOnFooter: true,
                                    userData: { 'db': tot_debit, 'cr': tot_credit }
                                });
                            }
                        });
                    }
                }
                else if (key === 'delete')
                {
                    $(this).closest('td').find('.cl_annuler_imputation').click();
                }
                else if (key === 'edit')
                {
                    $('.' + class_tr_edited).removeClass(class_tr_edited);
                    var releve = $(this).closest('tr').addClass(class_tr_edited).attr('id');
                    show_imputation_manuel(releve);
                }
            },
            items: items
        });
        $('.cl_detail_imputation').on('click', function(e){
            console.log('clicked', this);
        });

        //
        items = { 'show_image': {name: "Afficher l'image", icon: 'paste'} };
        if (usc)
            items.delete = {name: "Supprimer le lettrage", icon: 'delete'};
        $.contextMenu({
            selector: '.cl_det_imp_re_ext',
            trigger: 'left',
            callback: function(key, options) {
                var type = parseInt($(this).attr('data-type')),
                    id_releve = $(this).closest('tr').attr('id'),
                    cle_dossier_ext = $(this).closest('tr').find('.cl_cde_id').text(),
                    images = $.parseJSON(decodeURI($(this).attr('data-images')));

                $('.' + class_tr_edited).removeClass(class_tr_edited);
                $(this).closest('tr').addClass(class_tr_edited);

                if (key === 'show_image')
                {
                    if (images.length === 1) show_image_pop_up(images[0]);
                }
                else if (key === 'delete')
                {
                    annuler_lettrage_releve_ext(id_releve,cle_dossier_ext);
                }
            },
            items: items
        });
        $('.cl_det_imp_re_ext').on('click', function(e){
            console.log('clicked', this);
        });
    });

    if ($('#id_sc_param').length > 0)
    {
        $('.cl_input_chk_all').each(function(){
            $(this).closest('.ui-jqgrid-sortable').removeClass('ui-jqgrid-sortable');
        });
    }

    set_italic_eclater();
}

function col_model_banque_autre(w)
{
    var colM = [];

    if(typeof w !== 'undefined')
    {
        colM.push({ name:'date', index:'date', width: 8 * w/100, sortable:true, align:'center', sorttype: 'date', formatter: 'date', formatoptions: {srcformat: 'd/m/Y', newformat: 'd/m/Y'} });
        colM.push({ name:'image',index:'image', width: 8 * w/100, align:'center', formatter:function(v){ return image_formatter(v) } });
        colM.push({ name:'libelle',index:'libelle', width: 18 * w/100 });
        colM.push({ name:'bilan', index:'bilan', width: 8 * w/100, align:'center', formatter: function (v) { return compte_formatter(v) } });
        colM.push({ name:'tva', index:'tva', width: 8 * w/100, align:'center', formatter: function (v) { return compte_formatter(v) } });
        colM.push({ name:'resultat', index:'resultat', width: 8 * w/100, align:'center', formatter: function (v) { return compte_formatter(v) } });
        colM.push({ name:'mHt',index:'mHt', align:'right', width: 8 * w/100, sorttype: 'number', formatter: function(v){ return '<strong>'+number_format(v, 2, ',', ' ')+'</strong>'} });
        colM.push({ name:'mTva', index:'mTva', align:'right', width: 8 * w/100, sorttype: 'number', formatter: function(v){ return '<strong>'+number_format(v, 2, ',', ' ')+'</strong>'} });
        colM.push({ name:'mTtc', index:'mTtc', align:'right', classes:'js_cl_ttc', width: 8 * w/100, sorttype: 'number', formatter: function(v){ return '<strong>'+number_format(v, 2, ',', ' ')+'</strong>'} });
        colM.push({ name:'status', index:'status', width: 18 * w/100, formatter: function(v){ return status_autre_formatter(v)} });
    }
    else
    {
        colM = [
            'Date',
            'Image',
            'libellé',
            'Bilan',
            'Tva',
            'Résultat',
            'M. Ht',
            'M. Tva',
            'M. Ttc',
            'Rapprochement'
        ];
    }
    return colM;
}

function annuler_imputation(releve,type)
{
    $.ajax({
        data: { releve:releve, type:type },
        type: 'POST',
        url: Routing.generate('banque2_annuler_imputation'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            if (parseInt(data) === 0)
            {
                update_row(releve);
                show_info('SUCCES','Imputation Annulée avec succes');
            }
            else show_info('ERREUR','Une Erreur c est produite pendant l enregistrement','error');
        }
    });
}

/**
 * @var int $status
 * 0 : lettré
 * 1 : piece à valider
 * 2 : piece manquante
 */
function status_autre_formatter(v)
{
    var fas = ['fa-file-pdf-o', 'fa-file-powerpoint-o', 'fa-question'],
        classes = ['label-success','label-warning','label-default'],
        s = parseInt(v.status),
        label = '<label class="label '+classes[s]+'"><i class="fa '+fas[s]+'"></i></label>',
        element = '';

    var annuler_imputation = ' <i class="fa fa-times pointer cl_annuler_imputation_bsca" aria-hidden="true"></i>';
    if (s === 0)
    {
        if (s.length === 1)
            element = image_formatter(v.image) + annuler_imputation;
        else
            element = '<span class="cl_image_multiple_bsca pointer" data-image_flague="'+v.image_flague+'">Multiple</span>';
    }
    else if (s === 1) element = '<span class="text-warning pointer cl_lettrage_autre">Pi&egrave;ce&nbsp;&agrave;&nbsp;lettrer</e></span>';
    else element = '<span class="cl_show_lettrage_compta pointer">PM&nbsp;/&nbsp;Lettrage&nbsp;Multiple</span>';

    return label + '&nbsp;' + element;
}