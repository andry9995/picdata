/**
 * Created by SITRAKA on 02/11/2016.
 */

$(document).ready(function(){
    $(document).on('click','.js_indicateur_show_edit',function(){
        show_edit_indicateur($(this));
    });

    $(document).on('change','.js_radio_type',function(){
        change_type();
    });

    $(document).on('click','#js_add_rubrique',function(){
        add_rubrique();
    });

    $(document).on('click','.js_remove_indicateur',function(event){
        event.preventDefault();
        delete_indicateur($(this));
    });

    $(document).on('click','#js_formule',function(){
        $('#js_focus').focus();
    });

    $(document).on('click','#js_btn_save_indicateur_item',function(){
        save_indicateur();
    });

    $(document).on('click','.js_graphe',function(){
        change_graphe($(this));
    });

    $(document).on('change','.js_radio_operateur',function(){
        change_operateur();
    });

    $(document).on('click','.collapse-link',function(){
        $(this).closest('.js_indicateur_sortable').find('.table-resizable').resizableColumns('destroy').resizableColumns();
    });

    $(document).on('click','.js_valider_indicateur',function(){
        //if(ctrl_mode) return;
        var btn = $(this),
            div_indicateur = btn.closest('.js_indicateur_sortable'),
            indicateur = div_indicateur.attr('data-id'),
            is_etat = (div_indicateur.hasClass('js_etat')) ? 1 : 0,
            status = (btn.hasClass('btn-primary')) ? 0 : 1;

        $.ajax({
            data: { status:status, indicateur:indicateur, is_etat:is_etat },
            url: Routing.generate('ind_valider'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                test_security(data);
                if(parseInt(data) === 1)
                {
                    btn.removeClass('btn-primary').removeClass('btn-default');
                    if(status === 1) btn.addClass('btn-primary');
                    else btn.addClass('btn-default');
                    show_info('SUCCES','MODIFICATION BIEN ENREGISTREE AVEC SUCCES');
                }
                else show_info('ERREUR','UNE ERREUR C EST PRODUITE PENDANT LA MODIFICATION','error');
            }
        });
    });
});


/**
 * show indicateur edit
 * @param btn
 */
function show_edit_indicateur(btn)
{
    $('.js_pack_edited').removeClass('js_pack_edited');
    $('.js_indicateur_edited').removeClass('js_indicateur_edited');
    var indicateur;
    if(btn.hasClass('js_add'))
    {
        btn.parent().parent().parent().parent().parent().addClass('js_pack_edited');
        indicateur = $('#js_zero_boost').val();
    }
    else indicateur = btn.parent().parent().parent().addClass('js_indicateur_edited').attr('data-id');

    var lien = Routing.generate('ind_indicateur_edit');
    $.ajax({
        data: { action:0, indicateur:indicateur },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var titre,animated = 'bounceInRight';
            if(indicateur == $('#js_zero_boost').val()) titre = '<i class="fa fa-plus-circle"></i> <span>Nouveau Indicateur</span>';
            else titre = '<i class="fa fa-pencil-square-o"></i><span>Modification</span>';
            show_modal(data,titre,animated,'modal-lg');
            charger_rubriques_();
            $('#js_formule').resizable({
                //handles: 's',
                stop: function(event, ui) {
                    $(this).css("width", '');
                }
            });
        }
    });
}

/**
 * charger rubrique to combow
 * @private
 */
function charger_rubriques_()
{
    var type_rubrique = parseInt($("#js_group_rubrique input[type='radio']:checked").attr('data-val'));
    if(isNaN(type_rubrique)) return;

    var lien = Routing.generate('rubriques_sel')+'/'+ type_rubrique;
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
            $('#js_conteneur_rubrique').html(data);
        }
    });
}

/**
 * change type
 */
function change_type()
{
    $('#js_formule').html('<span class="blink" id="js_blink_formule">|</span>');
    charger_rubriques_();
}

/**
 * keydown number,operateur
 */
$(window).keydown(function(e) {
    //if(ctrl_mode) return;
    if( !($('#js_form_edit_indicateur_item').length > 0) ||
        $('#js_indicateur_libelle').is(':focus') ||
        $('#js_indicateur_description').is(':focus') ||
        //$('#js_rubrique').is(':focus') ||
        $('#js_varation_n').is(':focus') ||
        $('#js_indicateur_unite').is(':focus') ||
        $('#js_indicateur_limit').is(':focus') ||
        $('#js_indicateur_nom_afficher').is(':focus')) return;
    e.preventDefault();

    var code = -1;

    //chiffre
    if(e.key == '0') code = 96;
    else if(e.key == '1') code = 97;
    else if(e.key == '2') code = 98;
    else if(e.key == '3') code = 99;
    else if(e.key == '4') code = 100;
    else if(e.key == '5') code = 101;
    else if(e.key == '6') code = 102;
    else if(e.key == '7') code = 103;
    else if(e.key == '8') code = 104;
    else if(e.key == '9') code = 105;
    //operateur
    else if(e.key == '.') code = 110;
    else if(e.key == '/') code = 111;
    else if(e.key == '*') code = 106;
    else if(e.key == '-') code = 109;
    else if(e.key == '+') code = 107;
    else if(e.key == ',') code = 188;
    else if(e.key == '(') code = 112;
    else if(e.key == ')') code = 113;
    else if(e.key == ';') code = 114;
    //mouvement
    else
    {
        code = parseInt(e.keyCode || e.which);
        if(code == 37) code = 0;
        if(code == 39) code = 1;
        if(code == 46) code = 2;
        if(code == 8) code = 3;
    }

    if(code >= 96 && code <= 114 || code >= 0 && code <= 3)
    {
        var span;
        if(code >= 96 && code <= 114)
            $( "<span class='operateur'>" + e.key + "</span>" ).insertBefore( "#js_blink_formule" );
        else if(code == 0)
        {
            span = $('#js_blink_formule').prev('.operateur');
            move_blink(span,'ib');
        }
        else if(code == 1)
        {
            span = $('#js_blink_formule').next('.operateur');
            move_blink(span, 'ia');
        }
        else if(code == 2) move_blink(null,'da');
        else if(code == 3) move_blink(null,'db');
    }
});

/**
 * deplacement curseur
 *
 * @param span
 * @param deplacement
 */
function move_blink(span,deplacement)
{
    //if(ctrl_mode) return;
    deplacement = (typeof deplacement !== 'undefined') ? deplacement : 'ib';
    var blink = '<span class="blink" id="js_blink_formule">|</span>';

    if(deplacement == 'ib')
    {
        $('.blink').remove();
        $(blink).insertBefore(span);
    }
    else if(deplacement == 'ia')
    {
        $('.blink').remove();
        $(blink).insertAfter(span);
    }
    else if(deplacement == 'da')
    {
        $('.blink').next('.operateur').remove();
    }
    else if(deplacement == 'db')
    {
        $('.blink').prev('.operateur').remove();
    }
}

/**
 * add rubrique to formule
 */
function add_rubrique()
{
    //if(ctrl_mode) return;
    var libelle = $('#js_rubrique option:selected').text(),
        id_operande = $('#js_rubrique').val().trim(),
        variation = parseInt($('#js_varation_n').val()),
        class_v = $('#js_varation_n option:selected').attr('data-class'),
        operande_span = '<span class="label '+ class_v +' operateur operande" data-variation="'+variation+'" style="margin-right: 1px !important;margin-left: 1px !important;" data-id="'+id_operande+'">'+libelle+'</span>';
    $(operande_span).insertBefore($('#js_blink_formule'));
}

/**
 * save indicateur
 */
function save_indicateur()
{
    if(!indicateur_is_valid()) return;
    var libelle = $('#js_indicateur_libelle').val().trim().sansAccent().toUpperCase(),
        description = $('#js_indicateur_description').val().trim().sansAccent().toUpperCase(),
        unite = $('#js_indicateur_unite').val().trim().sansAccent().toUpperCase(),
        indicateur = $('#js_form_edit_indicateur_item').attr('data-id'),
        pack_indicateur = $('.js_pack_edited').attr('data-id'),
        formule = '',
        operandes = new Array(),
        zero_boost = $('#js_zero_boost').val(),
        client = zero_boost,
        dossier = zero_boost,
        nom_afficher = $('#js_indicateur_nom_afficher').val().trim(),
        theme = $('#js_indicateur_theme').val(),
        show_exercice_valide = $('#id_show_clot').is(':checked') ? 1 : 0;

    if(!$('#js_is_general').is(':checked'))
    {
        if($('#dossier').length > 0 && $('#dossier option:selected').text().trim() != '' ||
            !($('#dossier').length > 0) && $('#js_dossier_table').find('tr.ui-state-highlight').length > 0)
            dossier =  ($('#dossier').length > 0) ? $('#dossier').val() : $('#js_dossier_table').find('tr.ui-state-highlight').find('.js_td_dossier_id').text().trim();
        else client = $('#client').val();
    }

    $('#js_formule .operateur').each(function(){
        if($(this).hasClass('operande'))
        {
            formule += '#';
            operandes.push({id:$(this).attr('data-id') , variation:$(this).attr('data-variation')});
        }
        else formule += $(this).text().trim();
    });

    var is_table = 0;
    var graphes = new Array();
    $('#js_indicateur_graphes .js_graphe').each(function(){
        if($(this).hasClass('graphe-selected'))
        {
            if($(this).attr('data-code').trim() == 'TAB') is_table = 1;
            graphes.push($(this).attr('data-id'));
        }
    });

    var is_decimal = ($('#js_is_decimal').is(':checked')) ? 1 : 0;
    var type_operation = parseInt($("#js_indicateur_type_operateur input[type='radio']:checked").attr('data-value'));
    var limit = parseInt($('#js_indicateur_limit').val().trim());
    if(isNaN(limit)) limit = -1;

    //analyse
    var analyse = '1';
    $('#js_indicateur_analyses .checkbox input').each(function(){
        if($(this).is(':checked')) analyse += '1';
        else analyse += '0';
    });

    //periode
    var periode = '';
    $('#js_indicateur_periode .checkbox input').each(function(){
        if($(this).is(':checked')) periode += '1';
        else periode += '0';
    });

    if(is_table === 1)
    {
        limit = -1;
        unite = '';
        type_operation = 0;
        operandes = new Array();
    }

    $.ajax({
        data: {
            action:1, libelle:libelle, description:description, unite:unite,
            indicateur:indicateur, pack_indicateur:pack_indicateur,
            operandes:JSON.stringify(operandes), formule:formule,
            client:client, dossier:dossier, is_table:is_table,
            type_operation:type_operation, graphes:JSON.stringify(graphes),
            limit:limit, is_decimal:is_decimal, analyse:analyse,
            periode:periode, nom_afficher:nom_afficher, theme:theme,
            show_exercice_valide: show_exercice_valide
        },
        type: 'POST',
        url: Routing.generate('ind_indicateur_edit'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            var result = parseInt(data);
            if(result === 1)
            {
                $('.js_indicateur_edited').find('.js_indicateur_libelle_text').text(libelle);
                if($('.js_pack_edited').length > 0) reload_pack();

                close_modal();
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
            }
            else show_info('CET INDICATEUR EXISTE DEJA','CHOISIR UN AUTRE NOM','error');
        }
    });
}

/**
 * delete indicateur
 * @param btn
 */
function delete_indicateur(btn)
{
    var indicateur = btn.parent().parent().parent().attr('data-id');
    var lien = Routing.generate('ind_indicateur_edit');
    $.ajax({
        data: { action:2 , indicateur:indicateur },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            var result = parseInt(data);
            if(result === 1)
            {
                btn.parent().parent().parent().remove();
                show_info('SUCCES','INDICATEUR BIEN SUPPRIME');
            }
            else show_info('ERREUR:Indicateur non vide','une erreur s\'est produite pendant la SUPPRESION','error');
        }
    });
}

/**
 * test if indicateur is valid
 * @returns {boolean}
 */
function indicateur_is_valid()
{
    //libelle
    var libelle = $('#js_indicateur_libelle').val().trim();
    if(libelle === '')
    {
        show_info('ERREUR','NOM VIDE','error');
        $('#js_indicateur_libelle').closest('div.form-group').addClass('has-error');
        return false;
    }
    else $('#js_indicateur_libelle').closest('div.form-group').removeClass('has-error');

    //graphe
    var graphe_checked = false;
    var is_table = false;
    $('#js_indicateur_graphes .js_graphe').each(function(){
        if($(this).hasClass('graphe-selected'))
        {
            graphe_checked = true;
            if($(this).attr('data-code').trim().toUpperCase() === 'TAB') is_table = true;
        }
    });
    if(!graphe_checked)
    {
        show_info('ERREUR','CHOISIR TYPE D\'AFFICHAGE','error');
        return false;
    }

    //periode
    var periode_checked = false;
    $('#js_indicateur_periode .checkbox input').each(function(){
        if($(this).is(':checked'))
        {
            periode_checked = true;
            return true;
        }
    });
    if(!periode_checked)
    {
        show_info('ERREUR','CHOISIR AU MOINS UNE PERIODE D AFFICHAGE','error');
        return false;
    }

    //if table
    if(is_table) return true;
    //formule
    if($('#js_formule .operateur').length <= 0)
    {
        show_info('ERREUR','FORMULE VIDE','error');
        return false;
    }
    if($('#js_formule .operateur').length <= 0)
    {
        show_info('ERREUR','VOTRE FORMULE N\'A PAS DE RUBRIQUE','error');
        return false;
    }

    //nom a afficher
    var formule = '',
        nom_afficher = $('#js_indicateur_nom_afficher').val().trim();
    $('#js_formule .operateur').each(function(){
        if($(this).hasClass('operande')) formule += '#';
        else formule += $(this).text().trim();
    });
    if(nom_afficher === '')
    {
        show_info('ERREUR','Nom a afficher VIDE','error');
        $('#js_indicateur_nom_afficher').closest('div.form-group').addClass('has-error');
        return false;
    }
    else $('#js_indicateur_nom_afficher').closest('div.form-group').removeClass('has-error');

    var formule_spliter = formule.split(';'),
        nom_afficher_spliter = nom_afficher.split(';');

    if(formule_spliter.length !== nom_afficher_spliter.length)
    {
        show_info('ERREUR','VERIFIER L EQUIVALENCE FORMULE ET NOM A AFFICHER','error');
        $('#js_indicateur_nom_afficher').closest('div.form-group').addClass('has-error');
        return false;
    }
    else $('#js_indicateur_nom_afficher').closest('div.form-group').removeClass('has-error');

    //limit
    if($('#js_indicateur_limit').val().trim() !== '')
    {
        if(isNaN(parseInt($('#js_indicateur_limit').val().trim())))
        {
            show_info('ERREUR','LA LIMITE DOIT ETRE UN ENTIER ','error');
            $('#js_indicateur_limit').closest('div.form-group').addClass('has-error');
            return false;
        }
        else $('#js_indicateur_limit').closest('div.form-group').removeClass('has-error');
    }

    return true;
}

/**
 * change graphe
 * @param chk
 */
function change_graphe(chk)
{
    var code = chk.attr('data-code').trim().toUpperCase();
    if(chk.hasClass('graphe-selected'))
    {
        chk.removeClass('graphe-selected');
        if(code === 'TAB')
        {
            $('#js_indicateur_unite').prop('disabled',false);
            $('#js_indicateur_limit').parent().parent().removeClass('hidden').val('').attr('value','');
            $('#js_indicateur_type_operateur').parent().parent().removeClass('hidden');
            $('#js_indicateur_formule_panel').removeClass('hidden');
            $('#js_indicateur_analyses').parent().parent().removeClass('hidden');
        }
    }
    else
    {
        chk.addClass('graphe-selected');
        if(code === 'TAB')
        {
            $('#js_indicateur_unite').prop('disabled',true);
            $('#js_indicateur_limit').parent().parent().addClass('hidden');
            $('#js_indicateur_type_operateur').parent().parent().addClass('hidden');
            $('#js_indicateur_formule_panel').addClass('hidden');
            $('#js_indicateur_analyses').parent().parent().addClass('hidden');
        }
    }
}

/**
 * change operateur
 */
function change_operateur()
{
    if(parseInt($("#js_indicateur_type_operateur input[type='radio']:checked").attr('data-value')) === 1)
    {
        $('#js_indicateur_limit_label').removeClass('hidden');
        $('#js_indicateur_limit').parent().removeClass('hidden');
    }
    else
    {
        $('#js_indicateur_limit_label').addClass('hidden');
        $('#js_indicateur_limit').val('').attr('value','').parent().addClass('hidden');
    }
}