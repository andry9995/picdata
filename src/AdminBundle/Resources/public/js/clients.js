/*______________________________
*           FONCTIONS
________________________________*/
//set table clients
function set_table_clients(){   
    data = get_clients();
    height = height;
    colNames = ['','Nom','Type','Siren','Siège','Tel','Web','Commentaire','Instruction','Logo'];
    colModel = [
                {name: 'id', index: 'id', width: 7, sorttype: "integer", formatter: "number"},
                {name: 'nom', index: 'nom', width: 7, sorttype: "str"},
                {name: 'type', index: 'type', width: 7,sorttype: "integer"},
                {name: 'siren', index: 'siren', width: 7, sorttype: "str"},
                {name: 'adresseSiege', index: 'adresseSiege', width: 7, sorttype: "str"},
                {name: 'telFixe', index: 'telFixe', width: 7, sorttype: "str"},
                {name: 'siteWeb', index: 'siteWeb', width: 7, sorttype: "str"},
                {name: 'commentaire', index: 'commentaire', width: 7, sorttype: "str"},
                {name: 'instruction', index: 'instruction', width: 7, sorttype: "str"},
                {name: 'logo', index: 'logo', width: 7, sorttype: "str"},
                ];
    caption = 'Liste Clients';
    table = $('#table_clients');

    set_table_jqgrid(data,height,colNames,colModel,table);
    $('#gbox_table_clients').addClass('margin-top-1');
}

//fonction get client
function get_clients(){
    lien = Routing.generate('app_clients')+'/1';
    var result = [];
    $.ajax({
        data: {},
        type: 'POST',
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