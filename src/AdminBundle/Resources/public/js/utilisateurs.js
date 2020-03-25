/********************************
*          EVENEMENTS
********************************/
//change client
$(document).on('change','#client',function(){
    set_table_utilisateurs();
});
//edit utilsateur
$(document).on('dblclick','#table_utilisateurs tr',function(){
    alert($(this).attr('id'));
});
//afficher menu utilisateur
$(document).on('click','#table_utilisateurs tr',function(){
    set_menus($(this));
});



/********************************
*           FONCTIONS
********************************/
//set table utilisateurs
function set_table_utilisateurs()
{
    data = get_utilisateurs();
    height = height;
    colNames = ['login','password','Nom','Prénom','Email','Tel','Skype','Actif'];
    colModel = [
                    /*{name: 'id', index: 'id', width: 7, sorttype: "integer", formatter: "number"},*/
                    {name: 'login', index: 'login', width: 7, sorttype: "str", editable:true, edittype:'text'},
                    {name: 'password', index: 'password', width: 7, sorttype: "str"},        
                    {name: 'nom', index: 'nom', width: 7, sorttype: "str"},
                    {name: 'prenom', index: 'prenom', width: 7,sorttype: "str"},
                    {name: 'email', index: 'email', width: 7, sorttype: "str"},
                    {name: 'tel', index: 'tel', width: 7, sorttype: "str"},
                    {name: 'skype', index: 'skype', width: 7, sorttype: "str"},
                    {name: 'status', index: 'status', width: 7, sorttype: "integer", align:'center',
                                editable:true, edittype:'checkbox', editoptions: { value:"True:False"}, formatter: "checkbox"},
                ];
    caption = 'Liste Utilisateurs';
    table = $('#table_utilisateurs');

    set_table_jqgrid(data,height,colNames,colModel,table);
    activer_checkbox();
}

//fonction get utilisateurs
function get_utilisateurs()
{
    lien = Routing.generate('app_utilisateurs')+'/1'+(($('#client').val() == 0) ? '' : '/'+$('#client').val());
    var result = [];
    $.ajax({
        data: {},
        url: lien,
        async:false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            result = $.parseJSON(data);
        }
    });
    return result; 
}

//fonction get menu
function set_menus(tr_select)
{
    lien = Routing.generate('admin_content_menus')+'/'+tr_select.attr('id');
    var result = [];
    $.ajax({
        data: {},
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            $('#ul_menus').html(data);
            activer_checkbox();
        }
    });
}