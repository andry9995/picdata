/**
 * Created by SITRAKA on 31/01/2020.
 */

var l_type_comptas = ['Résultat','Tva','Bilan'],
    cl_new_tab = 'cl_new_tab',
    tree_dossier_ready_new = false;

$(document).ready(function(){
    $(document).on('mouseup','.js_show_add_cle',function(){
        //return; //modif ***
        var selectedText = window.getSelection().toString().trim(),
            releve = $(this).closest('tr').attr('id'),
            libelle = $(this).text().trim();

        if (selectedText === '' || !usc) return;

        last_action = { element: releve, type: 's', text:selectedText };
        show_edit_cle_new(selectedText,releve);
    });

    $(document).on('click','#id_cle_container .cl_btn_cle',function(){
        //return; //modif ***
        $('#id_cle_container .cl_btn_cle').each(function(){
            $(this).addClass('btn-white').removeClass('btn-primary');
        });

        $(this).addClass('btn-primary').removeClass('btn-white');
        charger_cle_props_new();
    });

    $(document).on('click','.js_show_cle_a_affecter',function(){
        var releve = $(this).closest('tr').attr('id'),
            libelle = $(this).text().trim();

        show_edit_cle_new('', releve);
    });

    $(document).on('change','.cl_cde_option',function(){
        var el_row = $(this).closest('.tab-pane'),
            options = {
                recherche: parseInt(el_row.find('.cl_cde_recherche').val()),
                format: parseInt(el_row.find('.cl_cde_format').val()),
                car_prec: el_row.find('.cl_cde_car_prec').val(),
                car_fin: el_row.find('.cl_cde_car_fin').val(),
                pos_deb: el_row.find('.cl_cde_pos_debut').val(),
                pos_len: el_row.find('.cl_cde_pos_length').val()
            };

        el_row.attr('data-options',encodeURI(JSON.stringify(options)));
    });

    $(document).on('keyup', '.search-input-new', function(){
        var div_actif = $('#id_div_tab').find('.tab-pane.active');
        if (div_actif.length === 0) return;

        var searchString = $(this).val(),
            index = parseInt(div_actif.attr('data-index'));

        $('#js_id_tree_pcc_'+ index).jstree('search', searchString);
    });

    $(document).on('click','.cl_add_compte',function(){
        var last = parseInt($('#id_ul_tab').attr('data-last_id')),
            type_compte = $(this).attr('data-type');

        $('#id_ul_tab').find('li.active').removeClass('active');
        $('#id_div_tab').find('.tab-pane.active').removeClass('active');

        $('.'+cl_new_tab).removeClass(cl_new_tab);

        var options = {
            recherche: 4,
            format: 0,
            car_prec: '',
            car_fin: '',
            pos_deb: '',
            pos_len: ''
        },
            pcgs = [],
            pccs = [];

        $('#id_ul_tab').append('<li class="active '+cl_new_tab+' cl_tab_cle_dossier_ext" data-index="'+ last +'" id="id_li_corr_'+ last +'"><a data-toggle="tab" href="#tab-'+last+'">'+l_type_comptas[type_compte]+'&nbsp;&nbsp;&nbsp;<i class="fa fa-times pointer cl_hide_cle_dossier_ext" aria-hidden="true"></i></a></li>');
        $('#id_div_tab').append('' +
            '<div id="tab-'+last+'" data-options="'+ encodeURI(JSON.stringify(options)) +'" data-pcgs="'+encodeURI(JSON.stringify(pcgs))+'" data-pccs="'+encodeURI(JSON.stringify(pccs))+'" class="tab-pane active" data-index="'+last+'" data-id="'+$('#js_zero_boost').val()+'" data-type_compte="'+ type_compte +'">' +
                '<div class="panel-body"></div>' +
            '</div>'
        );

        charger_param_compte();
        $('#id_ul_tab').attr('data-last_id',last + 1);
    });

    $(document).on('click','.cl_tab_cle_dossier_ext',function(){
        charger_param_compte();
    });

    $(document).on('click','#id_save_cle_dossier_new',function(){
        var tabs = [],
            key = ($('#id_key').length > 0) ? $('#id_key').val().trim() : '',
            errors = [],
            cle = $('#id_cle_container').length > 0 ?
                $('#id_cle_container').find('.cl_btn_cle.btn-primary').attr('data-id') :
                $('#js_zero_boost').val();

        var cles_slaves = [];
        $('#id_cle_container').find('.cl_btn_cle').each(function(){
            if (!$(this).hasClass('btn-primary'))
                cles_slaves.push($(this).attr('data-id'));
        });

        if (key === '' && $('#id_cle_container').length === 0)
        {
            errors.push('La Clé est Vide');
            $('#id_key').closest('.form-group').addClass('has-error');
        }
        else $('#id_key').closest('.form-group').removeClass('has-error');

        var reste = 0;
        $('#id_div_tab').find('.tab-pane').each(function(){
            var options = $.parseJSON(decodeURI($(this).attr('data-options'))),
                recherche = parseInt(options.recherche),
                //format = parseInt(options.format),
                car_prec = options.car_prec,
                car_fin = options.car_fin,
                pos_deb_text = options.pos_deb.toString().trim(),
                pos_len_text = options.pos_len.toString().trim(),
                pos_deb = -1,
                pos_len = -1,
                ind = $(this).attr('data-index'),
                supprimer = $(this).hasClass('hidden') ? 1 : 0;

            if (recherche === 4 && supprimer === 0) reste++;

            if (recherche === 1) format = 1;
            else if (recherche === 2) format = 3;
            else format = 0;

            if (pos_deb_text !== '')
                pos_deb = parseInt(pos_deb_text);
            if (pos_len_text !== '')
                pos_len = parseInt(pos_len_text);

            if (supprimer === 0 && (isNaN(pos_deb) || isNaN(pos_len)))
            {
                errors.push('Valeur non numérique dans la position');
                $('#id_li_corr_'+ ind).find('a[data-toggle="tab"]').addClass('tab-error');
            }
            else $('#id_li_corr_'+ ind).find('a[data-toggle="tab"]').removeClass('tab-error');

            tabs.push({
                pcgs: $.parseJSON(decodeURI($(this).attr('data-pcgs'))),
                pccs: $.parseJSON(decodeURI($(this).attr('data-pccs'))),
                id: $(this).attr('data-id'),
                supprimer: supprimer,
                type_compte: $(this).attr('data-type_compte'),
                options: {
                    recherche: recherche,
                    format: format,
                    car_prec: car_prec,
                    car_fin: car_fin,
                    pos_deb: pos_deb,
                    pos_len: pos_len
                }
            });

            var pccs = $.parseJSON(decodeURI($(this).attr('data-pccs')));
            if (!supprimer && pccs.length === 0)
            {
                errors.push('Compte du dossier non parametré');
                $('#id_li_corr_'+ ind).find('a[data-toggle="tab"]').addClass('tab-error');
            }
            else $('#id_li_corr_'+ ind).find('a[data-toggle="tab"]').removeClass('tab-error');
        });

        if (reste !== 1)
            errors.push("La recherche reste doit être qu'une seule");

        if (errors.length > 0)
        {
            for (var i = 0; i < errors.length; i++)
                show_info('Erreur',errors[i],'error');
            return;
        }

        $.ajax({
            data: {
                tabs: JSON.stringify(tabs),
                banque_type: $('#id_banque_type').val(),
                key: key,
                type_compta: parseInt($('input[name="radio-type-compta"]:checked').val()),
                dossier: $('#dossier').val(),
                pas_piece: $('#jd_id_pas_piece').is(':checked') ? 1 : 0,
                cle: cle,
                cles_slaves: JSON.stringify(cles_slaves)
            },
            type: 'POST',
            url: Routing.generate('cle_new_cle_save'),
            dataType: 'html',
            success: function(data) {
                //$('#test_cle_new').html(data);return;
                show_info('Succès','Modifications bien enregistrées avec succès');
                close_modal();
                go();
            }
        });
    });

    $(document).on('click','.cl_hide_cle_dossier_ext',function(e){
        e.stopPropagation();

        var li = $(this).closest('li'),
            index = parseInt(li.attr('data-index')),
            tab = '#tab-'+index;

        li.addClass('hidden');
        $(tab).addClass('hidden');

        if (li.hasClass('active'))
        {
            li.removeClass('active');
            $(tab).removeClass('active');

            var li_a_activer = null;
            li.closest('ul').find('li').each(function(){
                if (!$(this).hasClass('hidden') && li_a_activer === null)
                    li_a_activer = $(this);
            });

            if (li_a_activer !== null)
            {
                var index_a_activer = parseInt(li_a_activer.attr('data-index'));
                li_a_activer.addClass('active');
                $('#tab-'+index_a_activer).addClass('active');
            }

            charger_param_compte();
        }
    });

    $(document).on('click','.cl_show_piece_a_lettrer_ext',function(){
        $.ajax({
            data: {

            },
            type: 'POST',
            url: Routing.generate('cle_new_show_edit'),
            dataType: 'html',
            success: function(data) {
                show_modal(data,'Clé',undefined,'modal-x-lg');
                charger_param_compte();
            }
        });
    });

    $(document).on('change','#id_banque_type',function(){
        $('#id_ul_tab').find('.cl_hide_cle_dossier_ext').each(function(){
            if (!$(this).closest('cl_tab_cle_dossier_ext').hasClass('hidden'))
                $(this).click();
        });
    });
});

function show_edit_cle_new(la_cle, releve, cle)
{
    cle = (typeof cle === 'undefined') ? $('#js_zero_boost').val() : cle;

    $.ajax({
        data: {
            cle: la_cle,
            banque: $('#js_banque').val(),
            banque_compte: $('#js_banque_compte').val(),
            releve: releve,
            dossier: $('#dossier').val(),
            cle_id: cle
        },
        type: 'POST',
        url: Routing.generate('cle_new_show_edit'),
        dataType: 'html',
        success: function(data) {
            show_modal(data,'Clé',undefined,'modal-x-lg');
            charger_param_compte();
        }
    });
}

function charger_param_compte()
{
    var div_actif = $('#id_div_tab').find('.tab-pane.active');

    if (div_actif.length === 0) return;

    var type_compte = parseInt(div_actif.attr('data-type_compte')),
        cle_dossier_ext = div_actif.attr('data-id'),
        index = parseInt(div_actif.attr('data-index')),
        options = $.parseJSON(decodeURI(div_actif.attr('data-options')));

    $.ajax({
        data: {
            type_compte: type_compte,
            cle_dossier_ext: cle_dossier_ext,
            dossier: $('#dossier').val(),
            banque_type: $('#id_banque_type').val(),
            index: index
        },
        type: 'POST',
        url: Routing.generate('cle_new_cle_dossier_ext_param'),
        dataType: 'html',
        success: function(data) {
            div_actif.find('.panel-body').html(data);

            var pos_deb = parseInt(options.pos_deb),
                pos_len = parseInt(options.pos_len);

            if (isNaN(pos_deb)) pos_deb = -1;
            if (isNaN(pos_len)) pos_len = -1;

            div_actif.find('.cl_cde_recherche').val(parseInt(options.recherche));
            div_actif.find('.cl_cde_format').val(parseInt(options.format));
            div_actif.find('.cl_cde_car_prec').val(options.car_prec);
            div_actif.find('.cl_cde_car_fin').val(options.car_fin);
            div_actif.find('.cl_cde_pos_debut').val(pos_deb < 0 ? '' : pos_deb.toString());
            div_actif.find('.cl_cde_pos_length').val(pos_len < 0 ? '' : pos_len.toString());

            activer_qtip();
            charger_tree_new();
        }
    });
}

function charger_tree_new(asynch)
{
    var div_actif = $('#id_div_tab').find('.tab-pane.active'),
        type_compte = parseInt(div_actif.attr('data-type_compte')),
        cle_dossier_ext = div_actif.attr('data-id'),
        index = parseInt(div_actif.attr('data-index')),
        pcgSelecteds = decodeURI($('#tab-'+index).attr('data-pcgs'));

    asynch = typeof asynch !== 'undefined' ? asynch : true;

    var element = $('#js_id_tree_pcg_' + index);
    element.empty();

    $.ajax({
        data: { type_compte:type_compte, banque_type: $('#id_banque_type').val(), pcgs_selecteds:pcgSelecteds },
        type: 'POST',
        url: Routing.generate('banque_cle_pcg'),
        async: asynch,
        dataType: 'html',
        success: function(data) {
            test_security(data);
            tree_dossier_ready_new = false;
            element.jstree({
                'core' : { 'data' : $.parseJSON(data) } ,
                'checkbox' : { 'keep_selected_style' : false },
                'plugins' : [ "wholerow", "checkbox", 'real_checkboxes' ]})
                .on('changed.jstree', function () {
                    charger_tree_dossier_new();
                    save_compte_pcg_in_view_new();
                })
                .on('ready.jstree', function(){
                    tree_dossier_ready_new = true;
                    charger_tree_dossier_new();
                    save_compte_pcg_in_view_new();
                });
        }
    });
}

function charger_tree_dossier_new()
{
    if (!tree_dossier_ready_new) return;

    var div_actif = $('#id_div_tab').find('.tab-pane.active'),
        index = parseInt(div_actif.attr('data-index')),
        pcgsClickeds = $('#js_id_tree_pcg_'+ index).jstree().get_checked(),
        pccsClickeds = decodeURI($('#tab-'+index).attr('data-pccs'));

    $('#js_id_tree_pcc_container_'+index).html('<div id="js_id_tree_pcc_'+ index +'"></div>');
    var element = $('#js_id_tree_pcc_'+ index);

    $.ajax({
        data: { pcgs:JSON.stringify(pcgsClickeds), dossier:$('#dossier').val(), pccs_selecteds: pccsClickeds},
        type: 'POST',
        url: Routing.generate('banque_cle_pcc'),
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
                .on('changed.jstree', function(){
                    save_compte_in_view_new();
                })
                .on('ready.jstree', function(){
                    save_compte_in_view_new();
                });
        }
    });
}

function save_compte_pcg_in_view_new()
{
    var div_actif = $('#id_div_tab').find('.tab-pane.active'),
        index = parseInt(div_actif.attr('data-index')),
        pcgSelecteds = $('#js_id_tree_pcg_'+index).jstree().get_checked();

    div_actif.attr('data-pcgs', encodeURI(JSON.stringify(pcgSelecteds)));
}

function save_compte_in_view_new()
{
    var div_actif = $('#id_div_tab').find('.tab-pane.active'),
        index = parseInt(div_actif.attr('data-index')),
        pccSelecteds = $('#js_id_tree_pcc_'+ index).jstree().get_checked(),
        pcgSelecteds = $('#js_id_tree_pcg_'+index).jstree().get_checked();

    div_actif.attr('data-pccs', encodeURI(JSON.stringify(pccSelecteds)));
    div_actif.attr('data-pcgs', encodeURI(JSON.stringify(pcgSelecteds)));
}

function charger_cle_props_new()
{
    $.ajax({
        data: {
            cle: $('#id_cle_container').find('span.cl_btn_cle.btn-primary').attr('data-id'),
            dossier: $('#dossier').val()
        },
        type: 'POST',
        url: Routing.generate('cle_new_props'),
        dataType: 'html',
        success: function(data) {
            var props = $.parseJSON(data),
                val_opt = $('#id_banque_type').find('option[data-value="'+props.bt+'"]').val();

            $('#id_banque_type').val(val_opt);
            $('#jd_id_pas_piece').prop('checked', parseInt(props.pp) === 1);

            $('#js_id_engagement').prop('checked', parseInt(props.tc) === 0);
            $('#js_id_tresorerie').prop('checked', parseInt(props.tc) !== 0);

            charger_cle_dossier_exts();
        }
    });
}

function charger_cle_dossier_exts()
{
    $.ajax({
        data: {
            cle: $('#id_cle_container').find('span.cl_btn_cle.btn-primary').attr('data-id'),
            dossier: $('#dossier').val()
        },
        type: 'POST',
        url: Routing.generate('cle_dossier_exts'),
        dataType: 'html',
        success: function(data) {
            $('#id_cle_dossier_exts_container').html(data);
            charger_param_compte();
        }
    });
}

function set_italic_eclater()
{
    $('.cl_italic_row').each(function(){
        if (parseInt($(this).text()) === 1) $(this).closest('tr').addClass('italic-text');
    });
}

function annuler_lettrage_releve_ext(id_releve, cle_dossier_ext)
{
    $.ajax({
        data: {
            id_releve: id_releve,
            cle_dossier_ext: cle_dossier_ext
        },
        type: 'POST',
        url: Routing.generate('cle_dossier_annuler_imputation'),
        dataType: 'html',
        success: function(data) {
            update_row();
        }
    });
}

