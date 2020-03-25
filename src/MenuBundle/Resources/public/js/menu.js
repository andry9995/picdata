/*______________________________
*           EVENEMENTS
________________________________*/
$(document).on('click','#side-menu li.js_menu_item',function(event){
    //event.preventDefault();
    //change_left_menu($(this));
});


/*______________________________
*           FONCTIONS
________________________________*/
//click on menu gauche
function change_left_menu(li_select){
    if(li_select.hasClass(get_class_menu_item())) return;
    $('#side-menu li').removeClass(get_class_menu_item());
    li_select.addClass(get_class_menu_item());

    lien = li_select.attr('data-lien').trim();
    //si menu parent ou pas de lien
    if(li_select.attr('data-has_child') == 1 || lien == '') $('#wrapper-content').html('');
    else show_content();
}

//show content
function show_content(){
    lien = $('#side-menu li.active').attr('data-lien').trim();
    $('#wrapper-header-text').text($('#side-menu li.active').attr('data-libelle'));
    data = get_class_menu_item(lien);
    $.ajax({
        data: data,
        type: 'POST',
        url: Routing.generate(lien),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            $('#wrapper-content').html(data);
            content_ready(lien);
        },
        error: function (xhr, ajaxOptions, thrownError){
            $('#wrapper-content').html(xhr.responseText);
        }
    });
}

//function ready content
function content_ready(lien){
    if(lien == 'admin_content_clients') set_table_clients();
    if(lien == 'admin_content_utilisateurs') set_table_utilisateurs();
}

//function get data
function get_data(lien){
    
    return {};
}

//function get class menu item
function get_class_menu_item(){
    return 'active';
}