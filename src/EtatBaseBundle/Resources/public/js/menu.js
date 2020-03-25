/*********************************
 *          EVENEMENTS
* *******************************/
$(document).on('click','#js_eb_menu .js_eb_mn_item',function(event){
    event.preventDefault();
    if($(this).hasClass(eb_get_class_menu())) return;
    eb_mn_item_change($(this));
});





/*********************************
 *          FONCTIONS
 * *******************************/
function eb_get_class_menu()
{
    return 'eb_menu_active';
}
function eb_mn_item_change(a)
{
    $('#js_eb_menu .js_eb_mn_item').removeClass(eb_get_class_menu());
    $('#js_eb_menu .js_eb_mn_item span').addClass('text-black');

    a.addClass(eb_get_class_menu());
    a.find('span').removeClass('text-black');

    go();
}