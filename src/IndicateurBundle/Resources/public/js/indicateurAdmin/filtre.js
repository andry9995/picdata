/**
 * Created by SITRAKA on 02/11/2016.
 */

$(document).on('change','#js_is_general',function(){
    change_is_general();
});

$(document).on('change','#dossier',function(){
    change_is_general();
});

$(document).on('change','#client',function(){
    change_is_general();
});

function change_is_general()
{
    if($('#js_is_general').is(':checked')) $('#js_div_dossier').addClass('hidden');
    else $('#js_div_dossier').removeClass('hidden');
    group_charger();
}
