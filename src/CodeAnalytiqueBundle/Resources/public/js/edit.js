/**
 * Created by SITRAKA on 06/09/2016.
 */
/*$(document).on('change','#dossier',function(){
    charger_code_analytiques();
});*/

$(document).ready(function(){
    $(document).on('click','#js_add_a',function(){
        add_analytique();
    });

    $(document).on('change','.js_input_a',function(){
        edit_analytique($(this));
    });

    $(document).on('click','.js_remove_a',function(){
        remove_analytique($(this));
    });

    $(document).on('change','#id_is_section',function(){
        change_if_section();
    });
});

/**
 *  show fenetre edit codes analytiques
 */
function show_edit_analytiques(dossier_exist)
{
    dossier_exist = typeof dossier_exist !== 'undefined' ? dossier_exist : true;
    var lien = Routing.generate('code_analytique');
    $.ajax({
        data: { dossier: $('#dossier').val() },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            var titre = '<i class="fa fa-cogs fa-2x" aria-hidden="true"></i>&nbsp;<span>Codes Analytiques</span>',
                animated = 'bounceInRight';
            show_modal(data,titre,animated);
            if(dossier_exist) charger_site();
            else charger_code_analytiques();
        }
    });
}

/**
 * charger table code analytique
 */
function charger_code_analytiques()
{
    if($('#js_liste_code_analytique').length > 0)
    {
        var lien = Routing.generate('code_analytiques_liste');
        $.ajax({
            data: { dossier:$('#dossier').val() },
            type: 'POST',
            url: lien,
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                $('#js_liste_code_analytique').html(data);
                change_if_section();
            }
        });
    }
}

/**
 * ajout analytique
 */
function add_analytique()
{
    var section = $('#id_is_section').is(':checked') ?  $('#js_type_add').attr('data-section_val') : $('#js_type_add').val();
    analytique_action(0,$('#js_zero_boost').val(),$('#js_code_a').val(),$('#js_libelle_a').val(),undefined,section);
}

/**
 * edit analytique
 * @param input
 */
function edit_analytique(input)
{
    var tr = input.closest('tr'),
        type = parseInt(tr.attr('data-type')),
        section = $('#js_zero_boost').val().trim();

    if (type === 1)
        section = tr.find('.js_code_section').val();

    analytique_action(1,tr.attr('data-id').trim(),tr.find('.js_code_a').val(),tr.find('.js_libelle_a').val(),input,undefined,type,section);
}

/**
 * remove analytique
 * @param span
 */
function remove_analytique(span)
{
    var tr = span.closest('tr'),
        type = parseInt(tr.attr('data-type'));
    analytique_action(2,tr.attr('data-id').trim(),tr.find('.js_code_a').val(),tr.find('.js_libelle_a').val(),span,undefined,type);
}

/**
 *
 * @param action
 * @param analytique
 * @param code
 * @param libelle
 * @param element
 * @param section
 * @param type
 * @param analytique_section
 */
function analytique_action(action,analytique,code,libelle,element,section,type,analytique_section)
{
    type = typeof dossier_selected === 'undefined' ? 0 : type;
    section = typeof section === 'undefined' ? $('#js_zero_boost').val() : section;
    analytique_section = typeof section === 'undefined' ? $('#js_zero_boost').val() : analytique_section;

    if(analytique_is_valid(element) || action === 2)
    {
        var lien = Routing.generate('code_analytique_edit');
        $.ajax({
            data: {
                dossier:$('#dossier').val(),
                action:action,
                analytique:analytique,
                code:code,
                libelle:libelle,
                section: section,
                type: type,
                analytique_section: analytique_section
            },
            type: 'POST',
            url: lien,
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                if(parseInt(data) === 1)
                {
                    show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
                    charger_code_analytiques();

                    /*if(action === 0)
                    else if(action === 2) element.parent().parent().remove();*/

                    if(typeof dossier_selected === 'function') dossier_selected();
                }
                else
                {
                    charger_code_analytiques();
                    show_info('ERREUR','VERIFIER SI CE CODE EXISTE DEJA OU REESSAYER','error');
                }
            }
        });
    }
}

/**
 * test if analytique is valid
 * @returns {boolean}
 */
function analytique_is_valid(element)
{
    var element_code = $('#js_code_a'),
        element_libelle = $('#js_libelle_a');

    if(typeof element !== 'undefined')
    {
        element_code = element.closest('tr').find('.js_code_a');
        element_libelle = element.closest('tr').find('.js_libelle_a');
    }

    if(element_code.val().trim() === '')
    {
        show_info('erreur','CODE VIDE','error');
        return false;
    }
    if(element_code.val().trim().length > 8)
    {
        show_info('erreur','le code ne doit pas depasser 4 DIGITS','error');
        return false;
    }
    if(element_libelle.val().trim() === '')
    {
        show_info('erreur','LIBELLE VIDE','error');
        return false;
    }

    return true;
}

function change_if_section()
{
    var count_section = 0;
    $('#js_type_add').find('option').each(function(){
        count_section++;
    });

    if ($('#id_is_section').is(':checked') || count_section === 1) $('#js_type_add').addClass('hidden');
    else $('#js_type_add').removeClass('hidden');
}