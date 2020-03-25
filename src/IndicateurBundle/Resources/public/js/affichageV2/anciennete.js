/**
 * Created by SITRAKA on 11/01/2017.
 */
/**
 * add anciennete
 */
$(document).on('click','.js_anciennete_add',function(){
    add_anciennete($(this));
});

/**
 * delete anciennete
 */
$(document).on('click','.js_anciennete_delete',function(){
    delete_anciennete($(this));
});

/**
 * reset anciennete
 */
$(document).on('click','.js_reset_anciennete',function(){
    valider_anciennete();
});

/**
 * valider anciennete
 */
$(document).on('click','.js_valider_anciennete',function(){
   valider_anciennete($(this));
});

$(document).on('change','.js_date_anciennete',function(){
    $(this).attr('value',$(this).val());
});

/**
 * add anciennete
 * @param btn
 */
function add_anciennete(btn)
{
    var input_anciennete = btn.parent().find('.js_td_anciennete');
    var new_value = parseInt(input_anciennete.val());
    if(!new_anciennete_is_valid(input_anciennete)) return;

    var nouvelles_valeurs = get_old_anciennetes(input_anciennete);
    nouvelles_valeurs.push(new_value);
    nouvelles_valeurs = sortA(nouvelles_valeurs);

    var tbody = input_anciennete.val('').attr('value','').parent().parent().parent().parent().find('tbody').empty();
    for(var i = 0;i < nouvelles_valeurs.length; i++)
    {
        var value = nouvelles_valeurs[i];
        if(value != 0)
        {
            var new_tr = '<tr>'+
                '<td class="js_td_anciennete">'+value.toString()+'</td>'+
                '<td class="pointer js_anciennete_delete text-center">'+
                '<i class="fa fa-trash-o btn" aria-hidden="true"></i>'+
                '</td>'+
                '</tr>';
            tbody.append(new_tr);
        }
    }
}

/**
 * test if new anciennete is valid
 * @param input
 * @returns {boolean}
 */
function new_anciennete_is_valid(input)
{
    var new_value = parseInt(input.val());
    if(isNaN(new_value))
    {
        show_info('ERREUR','CETTE VALEUR DOIT ETRE UN ENTIER','error');
        input.parent().addClass('has-error');
        return false;
    }
    else input.parent().removeClass('has-error');

    var ancienne_valeurs = get_old_anciennetes(input);
    if(ancienne_valeurs.in_array(new_value))
    {
        show_info('ERREUR','CETTE VALEUR EXISTE DEJA','error');
        input.parent().addClass('has-error');
        return false;
    }
    else input.parent().removeClass('has-error');

    return true;
}

/**
 * old anciennetes
 * @param input
 * @returns {Array}
 */
function get_old_anciennetes(input)
{
    var ancienne_valeurs = new Array();
    ancienne_valeurs.push(0);
    input.parent().parent().parent().parent().find('tbody .js_td_anciennete').each(function(){
        ancienne_valeurs.push($(this).text().trim());
    });
    input.parent().parent().parent().parent().find('tbody .js_td_anciennete_delete').each(function(){
        $(this).parent().remove();
    });
    return ancienne_valeurs;
}

/**
 * delete anciennete
 * @param btn
 */
function delete_anciennete(btn)
{
    btn.parent().addClass('hidden').find('.js_td_anciennete').removeClass('js_td_anciennete').addClass('js_td_anciennete_delete');
}

/**
 * valider anciennete
 * @param btn
 */
function valider_anciennete(btn)
{
    var html_to_set = null,
        div_current = $('.js_current_div');
    if(typeof btn !== 'undefined')
    {
        html_to_set = btn.parent().parent().parent().html();
        div_current.find('.js_anciennete_hidden').html(html_to_set);
        div_current.find('.js_anciennete').attr('data-content',html_to_set);
    }
    else set_anciennete(div_current);
    div_current.find('.js_anciennete').click();
    charger_graphe(div_current);
}

function sortA(arr) {
    return arr.sort(function(a, b) {
        return a - b;
    });
}