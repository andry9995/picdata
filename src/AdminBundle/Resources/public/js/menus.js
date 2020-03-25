/********************************
*          EVENEMENTS
********************************/
//change value menu disables
$(document).on('click','div.icheckbox_square-green',function(){
    if($(this).find('.js_status_menu').length > 0)
        change_menu_disabled($(this));
});





/********************************
*          FONCTIONS
********************************/
//change value menu disables
function change_menu_disabled(div_select)
{
    id = parseInt(div_select.find('.js_status_menu').attr('data-id'));
    id_menu = parseInt(div_select.find('.js_status_menu').attr('data-id-menu'));
    id_user = parseInt($('#table_utilisateurs tr.ui-state-highlight').attr('id'));

    lien = Routing.generate('admin_content_edit_menu_utilisateur')+'/'+id_menu+'/'+id_user+'/'+id;
    $.ajax({
        data: {},
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            if(id == 0) div_select.find('.js_status_menu').attr('data-id',parseInt(data));
            else div_select.find('.js_status_menu').attr('data-id',0);
        }
    });
}