/**
 * Created by SITRAKA on 14/09/2016.
 */
$(document).on('click','.js_table_rubrique .js_edit',function(){
    call_edit($(this));
});

$(document).on('change','.js_table_rubrique .js_libelle_edit',function(){
    call_edit($(this));
});

/**
 *
 * @param element
 */
function call_edit(element)
{
    $('.js_tr_edited').removeClass('js_tr_edited');
    element.parent().parent().addClass('js_tr_edited');

    //add
    if(element.hasClass('js_add'))
    {
        id_rubrique = $('#js_zero_boost').val();
        if(!libelle_is_valid(element)) return;
        action_rubrique = 0;
    }
    else
    {
        id_rubrique = element.parent().parent().attr('data-id');
        //edit
        if (element.hasClass('js_libelle_edit'))
        {
            if(!libelle_is_valid(element)) return;
            action_rubrique = 1;
        }
        //remove
        else action_rubrique = 2;
    }

    table_edited = element.parent().parent().parent().parent();
    $('.js_table_edited').removeClass('js_table_edited');
    table_edited.addClass('js_table_edited');
    edit_rubrique(action_rubrique,id_rubrique,element.parent().parent().find('.js_libelle').val().trim(),table_edited.attr('data-type'),element.parent().parent().find('.js_solde').val().trim(),element.parent().parent().find('.js_type_compte').val().trim());
}

/**
 *
 * @param element
 * @returns {boolean}
 */
function libelle_is_valid(element)
{
    var inp = element.parent().parent().find('.js_libelle');
    if(inp.val().trim() == '')
    {
        show_info('Erreur','LIBELLE VIDE','error');
        return false;
    }
    inp.val(inp.val().toString().sansAccent().toUpperCase());
    return true;
}

/**
 *
 * @param action
 * @param id
 * @param libelle
 * @param type
 * @param solde
 * @param type_compte
 */
function edit_rubrique(action,id,libelle,type,solde,type_compte)
{
    lien = Routing.generate('rubriques_admin_edit');
    $.ajax({
        data: { action:action, id:id, libelle:libelle, type:type, solde:solde, type_compte:type_compte },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);

            if(action == 0)
            {
                $(data).insertAfter($('.js_table_edited tr:last'));
                $('.js_table_edited .js_libelle_add').val('');
            }
            if(action == 2) $('.js_tr_edited').remove();
            show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
        }
    });
}