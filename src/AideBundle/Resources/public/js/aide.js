/**
/**
 * Created by INFO on 07/09/2017.
 */


$(document).ready( function () {

    // setScroller();

    var last_sel_aide_3;
    var last_sel_aide_3_associe;
    var aide_2_id = 0;




    /* ************************DEBUT CENTRE AIDE ************************ */


    $(document).on('change', '#js_type_texte', function(){
        var aide3Id = $('#js_edit_texte_aide_3').attr('data-id');
        var typeContenu = $('#js_type_texte').val();
        checkIfEmpty(aide3Id, typeContenu);

    });



    /* ************************DEBUT AIDE 1 ************************ */

    $(document).on('click', '.js_delete_aide_1', function() {

        var aide1Id = $(this).attr('data-id'),
            aideType = $(this).attr('data-type');

        swal({
            title: 'Suppression Aide',
            text: "Voulez-vous supprimer l'ensemble des articles contenus dans ce bloc?",
            type: 'question',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui',
            cancelButtonText: 'Non'
        }).then(function () {

                $.ajax({
                    url: Routing.generate('centre_aide_1_delete'),
                    type: 'POST',

                    async: true,
                    data: {
                        aide1Id: aide1Id,
                        aideType: aideType
                    },
                    success: function (data) {
                        $('#js_contenu_aide').html(data);
                    }
                });
            },
            function (dismiss) {
                if (dismiss === 'cancel') {

                } else {

                }
            });
    });

    $(document).on('click', '.js_edit_aide_1, .js_add_aide_1', function () {
        var aide_id = $(this).attr('data-id'),
            aide_type = $(this).attr('data-type');

        if(typeof aide_id === 'undefined'){
            aide_id = 0;
        }

        if(typeof aide_type === 'undefined'){
            aide_type = 0;
        }

        $.ajax({

            data: {
                aide_1_id: aide_id,
                aide_type: aide_type
            },
            url: Routing.generate('centre_aide_1_edit'),
            type: 'POST',
            async: true,
            dataType: 'html',
            success: function (data) {
                $('#js_aide_1_form').html(data);
            }
        });

        $('#js_aide_1_modal').modal('show');

    });

    $(document).on('click', '.js_save_aide_1', function () {

        $('#js_aide_1_modal').modal('hide');

        var titre = $(this).closest('.centre_aide_1').find('.js_titre').val(),
            contenu = $(this).closest('.centre_aide_1').find('.js_contenu').val(),
            aide_1_id = $(this).closest('.centre_aide_1').find('.js_aide_1_id').val(),
            aide_type = $(this).attr('data-type');

        $.ajax({

            url: Routing.generate('centre_aide_1_edit', {json: 1}),
            type: 'POST',
            async: true,
            data: {
                titre: titre,
                contenu: contenu,
                aide_1_id: aide_1_id,
                aide_type: aide_type
            },
            success: function (data) {
                $('#js_contenu_aide').html(data);
            }
        });


    });


    /* ************************DEBUT AIDE 2 ------------------------ */

    var aide3Grid = $('#js_aide_3_liste');

    var aide3AssocieGrid = $('#js_aide_3_associe_liste');

    aide3Grid.jqGrid({

        datatype: 'json',
        loadonce: false,
        sortable: true,
        autowidth: true,
        width: 560,
        shrinkToFit: true,
        viewrecords: true,
        pager: '#js_aide_3_pager',
        hidegrid: false,
        caption: 'Aides',
        colNames: ['Titre',
            'Rang',
            '<span class="fa fa-bookmark-o " style="display:inline-block"/> Action'
        ],
        colModel: [
            {
                name: 'aide-3-titre',
                index: 'aide-3-titre',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js_aide_3_titre'
            },
            {
                name: 'aide-3-rang',
                index: 'aide-3-rang',
                editable: true,
                sortable: true,
                width: 30,
                align: "center"
            },
            {
                name: 'aide-3-action', index: 'aide-3-action', width: 60, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js_save_aide_3" title="Enregistrer"></i><i class="fa fa-trash icon-action js_delete_aide_3" title="Supprimer"></i>'},

                classes: 'js_save_aide_3_action'
            }
        ],

        onSelectRow: function (id) {
            if (id && id !== last_sel_aide_3) {
                aide3Grid.restoreRow(last_sel_aide_3);
                last_sel_aide_3 = id;
            }
            aide3Grid.editRow(id, false);
        },

        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },

        loadComplete: function () {

            if ($(".js_add_aide_3").length == 0) {
                aide3Grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button class="btn btn-outline btn-primary btn-xs js_add_aide_3" style="margin-right: 20px;">Ajouter</button></div>');
            }

            aide3Grid.jqGrid('setGridWidth', 560);

        },

        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}
    });

    $(document).on('click', '.js_delete_aide_2', function() {

        var aide2Id = $(this).attr('data-id-2');
        var aide1Id = $('.js_add_aide_2').attr('data-id-1');

        swal({
            title: 'Suppression Aide',
            text: "Voulez-vous supprimer l'ensemble des articles contenus dans ce bloc?",
            type: 'question',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui',
            cancelButtonText: 'Non'
        }).then(function () {

                $.ajax({
                    url: Routing.generate('centre_aide_2_delete'),
                    type: 'POST',

                    async: true,
                    data: {
                        aide2Id: aide2Id
                    },
                    success: function () {

                        setContenuAide2(aide1Id, 0);

                    }
                });
            },
            function (dismiss) {
                if (dismiss === 'cancel') {

                } else {

                }
            });
    });

    $(document).on('click', '.js_edit_aide_2, .js_add_aide_2', function() {

        aide_2_id = $(this).attr('data-id-2');
        var aide_1_id = 0;
        if (typeof aide_2_id === 'undefined') {
            aide_2_id = 0;
            aide_1_id = $(this).attr('data-id-1');
        }

        $.ajax({

            data: {
                aide_2_id: aide_2_id,
                aide_1_id: aide_1_id
            },
            url: Routing.generate('centre_aide_2_edit'),
            type: 'POST',
            async: true,
            dataType: 'html',
            success: function (data) {
                $('#js_aide_2_form').html(data);
                if(aide_1_id != 0) {
                    $('.js_save_aide_2').attr('data-id-1', aide_1_id);
                }
            }
        });

        $('#js_aide_2_modal').modal('show');

        var url = Routing.generate('aide_3', {json: aide_2_id});
        var editUrl = Routing.generate('aide_3_edit', {aide_2_id: aide_2_id});

        aide3Grid.jqGrid('clearGridData');

        aide3Grid.jqGrid('setGridParam', {url: url});
        aide3Grid.jqGrid('setGridParam', {editurl: editUrl}
        ).trigger('reloadGrid');

    });

    $(document).on('click', '.js_save_aide_2', function () {

        var titre = $('.js_titre_aide_2').val();
        aide_2_id = $(this).attr('data-id-2');
        var aide_1_id = $(this).attr('data-id-1');

        var h2 = $(this).closest('.ibox').find('h2');

        $.ajax({

            url: Routing.generate('centre_aide_2_edit', {json: 1}),
            type: 'POST',
            async: true,
            data: {
                titre: titre,
                aide_2_id: aide_2_id,
                aide_1_id: aide_1_id
            },
            success: function (data) {

                // console.log(data);

                aide_2_id = data;

                var url = Routing.generate('aide_3', {json: aide_2_id});
                var editUrl = Routing.generate('aide_3_edit', {aide_2_id: aide_2_id});

                aide3Grid.jqGrid('clearGridData');

                aide3Grid.jqGrid('setGridParam', {url: url});
                aide3Grid.jqGrid('setGridParam', {editurl: editUrl}
                ).trigger('reloadGrid');




                setContenuAide2(aide_1_id,aide_2_id);
            }
        });


    });

    $(document).on('click', '.js_add_aide_3', function(){
        if(canAddRow(aide3Grid)) {
            event.preventDefault();
            aide3Grid.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
            $("#" + "new_row", "#js_aide_3_liste").effect("highlight", 20000);
        }
    });

    $(document).on('click', '.js_save_aide_3_action', function (event) {

        var aide_1_id = $('.js_add_aide_2').attr('data-id-1');

        var url = Routing.generate('aide_3', {json: aide_2_id});

        event.preventDefault();
        event.stopPropagation();
        aide3Grid.jqGrid('saveRow', last_sel_aide_3, {
            "aftersavefunc": function() {
                aide3Grid.jqGrid('setGridParam', { url: url }, {page:1}).trigger('reloadGrid');

                setContenuAide2(aide_1_id,aide_2_id);
            }
        });
    });

    $(document).on('click', '.js_delete_aide_3', function() {

        event.stopPropagation();
        event.preventDefault();

        var aide_1_id = $('.js_add_aide_2').attr('data-id-1');

        var rowid = $(this).closest('tr').attr('id');

        if (rowid == 'new_row') {
            $(this).closest('tr').remove();
            return;
        }

        $('#js_aide_3_liste').jqGrid('delGridRow', rowid, {
            url: Routing.generate('aide_3_delete'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet article?',
            afterComplete: function(){
                setContenuAide2(aide_1_id,aide_2_id);
            }
        });
    });

    $(document).on('click', '.href3', function(){
        var aide_3_id = $(this).attr('data-id');
        var url = Routing.generate('aide_recent');

        $.ajax({
           url: url,
           type: 'POST',
            data: {aide_3_id: aide_3_id},
            async: true,
            success: function(data){
                if(data == 1){
                    // show_info("Info","Aide recent enregistré", "info");
                }
            }
        });
    });


    /* ************************DEBUT AIDE 3 ************************ */

    $(document).on('click','#js_edit_texte_aide_3',function () {
        $('.js_aide_3_contenu').summernote(
            {
                lang: 'fr-FR',
                   focus: true,
                callbacks:{
                    onImageUpload: function (files) {
                        sendFile(files[0]);
                    }
                },
                toolbar: [
                    ['style', ['style']],
                    ['fontstyle', ['fontname','fontsize']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['table', 'picture', 'link', 'unlink']]
                ],
                fontSizes: ['8', '9', '10', '11', '12', '14', '18', '20', '22' ,'24', '26', '28', '30', '32', '34', '36', '48' , '64', '82'],
                lineHeights: ['1','1.2','1.4','1.5', '1.6', '1.8', '2', '2.5', '3']
            }
        );
    });

    $(document).on('click','#js_save_texte_aide_3',function () {
        // var aHTML = $('.js_aide_3_contenu').code();
        var aHTML = $('.js_aide_3_contenu').summernote('code');
        var motCles = $('.js_mot_cles').val();
        save_aide_3(aHTML,motCles);
        // $('.js_aide_3_contenu').destroy();
        $('.js_aide_3_contenu').summernote('destroy');

    });


    aide3AssocieGrid.jqGrid({

        datatype: 'json',
        loadonce: false,
        sortable: true,
        autowidth: true,
        width: 500,
        shrinkToFit: true,
        viewrecords: true,
        pager: '#js_aide_3_pager',
        hidegrid: false,
        caption: 'Aides',
        colNames: ['Titre', 'Associés', 'Action'],
        colModel: [
            {
                name: 'aide-3-titre',
                index: 'aide-3-titre',
                sortable: true,
                align: "center"
            },
            {
                name: 'aide-3-associe',
                index: 'aide-3-associe',
                editable: true,
                align: "center",
                fixed: true,
                edittype: 'checkbox',
                formatter: 'checkbox',
                cellattr: function () { return ' title="Clickez ici si cet article est associé à celui ci"'; }
            },
            {
                name: 'action',
                index: 'action',
                width: 60,
                align: "center",
                sortable: false,
                classes: 'js_save_aide_3_associe'
            }
        ],

        onSelectRow: function (idx) {
            if (idx && idx !== last_sel_aide_3_associe) {
                aide3AssocieGrid.restoreRow(last_sel_aide_3_associe);
                last_sel_aide_3_associe = idx;
            }
            aide3AssocieGrid.editRow(idx, false);
        },

        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },

        loadComplete: function () {

            aide3AssocieGrid.jqGrid('setGridWidth', 500);

        },

        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}
    });



    $(document).on('click', '#js_edit_aide_3_associe', function(){
        $('#js_aide_3_associe_modal').modal('show');
        var aide_3_id = $(this).attr('data-id');
        $('#js_aide_3_associe_modal').attr('data-id', aide_3_id);
        var url = Routing.generate('centre_aide_3_associe', {json: aide_3_id});
        var editUrl = Routing.generate('centre_aide_3_associe_edit', {json: aide_3_id});
        aide3AssocieGrid.jqGrid('clearGridData');

        aide3AssocieGrid.jqGrid('setGridParam', {
            url: url,
            editurl: editUrl
        }).trigger('reloadGrid');

    });


    $(document).on('click', '.js_save_aide_3_associe', function(){
        var aide_3_id = $('#js_aide_3_associe_modal').attr('data-id')  ;
        var url = Routing.generate('centre_aide_3_associe', {json: aide_3_id});
        var editUrl = Routing.generate('centre_aide_3_associe_edit', {json: aide_3_id});

        aide3AssocieGrid.jqGrid('saveRow', last_sel_aide_3_associe , {
            "aftersavefunc": function() {
                aide3AssocieGrid.jqGrid('setGridParam', {
                    url: url,
                    editurl: editUrl
                }).trigger('reloadGrid');

            }
        });
    });

    $(document).on('click', '#js_edit_menu_aide_3', function(){

        $('#js_aide_3_menu_modal').modal('show');

        //Initialisation champ
        $.ajax({
            url: Routing.generate('aide_3_menu_form'),
            type: 'POST',
            data: {aide3Id: $(this).attr('data-id')},
            success: function (data) {

                // console.log(data);

                $('#js_menu_aide').val(data.libelle);
                $('#js_menu_aide').attr('data-id',data.id);
            }

        });


        $('#js_save_menu_aide_3').attr('data-id', $(this).attr('data-id'));

        //Tree
        $.ajax({
            datatype: 'json',
            url: Routing.generate('aide_3_menu_tree'),
            type: 'GET',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function (jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            async: true
            ,
            dataType: 'html',
            success: function (data) {

                $('#js_tree_menu').jstree("destroy");

                $('#js_tree_menu').jstree({
                    'core': $.parseJSON(data)
                })
                    .bind("dblclick.jstree", function (event) {
                        if ($(this).jstree().get_selected(true)[0].children == 0){
                            var CurrentNode = $(this).jstree("get_selected");
                            var selectId = $(this).jstree().get_selected(true)[0].id;
                            selectId = selectId.substr(3);

                            var selectText = $('#' + CurrentNode).text();

                            $('#js_menu_aide').val(selectText);
                            $('#js_menu_aide').attr('data-id', selectId);
                        }
                    });
            }
        });
    });

    $(document).on('click', '#js_edit_mot_cle_aide_3', function(){

        $('#js_aide_3_mot_cle_modal').modal('show');

        $.ajax({
            url: Routing.generate('aide_3_mot_cle_form'),
            type: 'POST',
            data: {aide3Id: $(this).attr('data-id')},
            success: function (data) {
                $('#js_mot_cle_aide').val(data);
            }
        });

        $('#js_save_mot_cle_aide_3').attr('data-id', $(this).attr('data-id'));

    });

    $(document).on('click', '#js_save_menu_aide_3', function(){

        var lien = Routing.generate('aide_3_menu_edit');
        save_menu_aide_3(lien);

    });

    $(document).on('click', '#js_save_mot_cle_aide_3', function(){

        var lien = Routing.generate('aide_3_mot_cle_edit');
        save_mot_cle_aide_3(lien);
    });





    /* ************************DEBUT MINIATURE ************************ */
    $(document).on('click', '#js_open_miniature, .js_open_miniature', function () {
        var chatBox = $('.small-chat-box');

        if (!chatBox.hasClass('active')) {
            chatBox.addClass('active');
        }
        else {
            chatBox.removeClass('active');
        }

    });

    $(document).on('click', /*'#js_laisser_message,*/ '#js_annuler_message, .btn-principale', function () {

        var lien = "";
        var typeMiniature = 0;
        if ($(this).attr('id') == 'js_laisser_message') {
            lien = Routing.generate('aide_miniature', {json: 1});
            typeMiniature = 2;
        }
        else {
            lien = Routing.generate('aide_miniature');
            typeMiniature = 1;
        }

        initialize_contenu_aide(lien, typeMiniature, '');

    });

    $(document).on('click', '#js_laisser_message', function(){

        window.open(Routing.generate('centre_aide_chat'), '', 'left=500,width=380,height=450');

    });


    $(document).on('click', '#js_envoyer_message', function () {

        // var nom = $('#js_nom').val();
        // var mail = $('#js_mail').val();
        var texte = $('#js_texte').val();

        var lien = Routing.generate('aide_min_envoi_mail');
        $.ajax({
            url: lien,
            data: {
                // nom: nom,
                // mail: mail,
                texte: texte
            },
            async: true,
            type: 'POST',
            dataType: 'html',
            success: function (data) {
                $('#js_aide').html(data);
                ready_inspinia();
                set_aide_size(1);

            }
        });
    });

    $(document).on('click', '.aide', function () {
       var aide = $(this).attr('data-id');

        var lien = Routing.generate('aide_min_affichage', {json: aide});
        initialize_contenu_aide(lien, 3, '');

    });

    $(document).on('click', '#btn-aide-search', function(){
       var search = $('#js_aide_search').val();

       var lien = Routing.generate('aide_miniature', {json: 2});

        initialize_contenu_aide(lien, 1, search);
    });

    $(document).on('keydown', '#js_aide_search', function(e){

        var keyCode = e.keyCode || e.which;


        if(keyCode == 13 || keyCode == 9) {
            var search = $('#js_aide_search').val();
            var lien = Routing.generate('aide_miniature', {json: 2});
            initialize_contenu_aide(lien, 1, search);
        }
    });

    $(document).on('click', '.article-originale', function(){

        var aide3Id = $(this).attr('data-id');
        var url = Routing.generate('centre_aide_3', {json: aide3Id});


        window.open(url, '_blank');
    });
    
    
    $(document).on('click', '#js_aide .content img', function(){

        var src = $(this).attr('src');

        var contenu = '<embed src="'+src+'" width="100%"/>';

        var options = {modal: true, resizable: true, title: 'Image'};
        modal_ui(options, contenu, undefined, 0.80, 0.80);
    });

    /* ************************DEBUT SEARCH ************************ */
    $(document).on('click', '.js_aide_search', function(){
        var search = $('.js_search_text').val();
        var lien = Routing.generate('aide_search');
        setContenuSearch(lien, search);
    });

    $(document).on('keydown', '.js_search_text', function(e){

        var keyCode = e.keyCode || e.which;


        if(keyCode == 13 || keyCode == 9) {
            var search = $('.js_search_text').val();
            var lien = Routing.generate('aide_search');
            setContenuSearch(lien, search);
        }
    });

    window.onresize =  function(event) {
     var width = $(window).width();

     setCentreAide1(width);
 };




});


function canAddRow(jqGrid) {
    var canAdd = true;
    var rows = jqGrid.find('tr');

    rows.each(function () {
        if ($(this).attr('id') == 'new_row') {
            canAdd = false;
        }
    });
    return canAdd;
}

function save_aide_3(aHTML,motCles){

    var aide3Id = $('#js_edit_texte_aide_3').attr('data-id');
    var typeContenu = $('#js_type_texte').val();

    var lien = Routing.generate('centre_aide_3_edit');



    $.ajax({

        data:{
            aide3Id:aide3Id,
            aide3Contenu:aHTML,
            motCles:motCles,
            typeContenu: typeContenu
        },
        url: lien,
        type: 'POST',
        dataType: 'html',
        success: function(data){

            var res = parseInt(data);

            if (res == 1) {
                show_info('SUCCES', 'MODIFICATION BIEN ENREGISTREE');
            }

        }
    });
}

function save_menu_aide_3(lien){
    var menuId = $('#js_menu_aide').attr('data-id');
    var aide3Id = $('#js_save_menu_aide_3').attr('data-id');

    $.ajax({
        url: lien,
        type: 'POST',
        data: {menuId: menuId, aide3Id:aide3Id},
        success: function(data){
            $('#js_aide_3_menu_modal').modal('hide');
            show_info('Info','Mise à jour effectuée', 'success');
        }
    })
}

function save_mot_cle_aide_3(lien) {

    var aide3Id = $('#js_save_mot_cle_aide_3').attr('data-id');
    var motCle = $('#js_mot_cle_aide').val();
    $.ajax({
        url: lien,
        type: 'POST',
        data: {aide3Id:aide3Id, motCle:motCle},
        success: function () {
            $('#js_aide_3_mot_cle_modal').modal('hide');
            show_info('Info', 'Mise à jour effectuée', 'success');
        }
    });

}

function sendFile(file) {
    data = new FormData();
    data.append("file", file);//You can append as many data as you want. Check mozilla docs for this
    $.ajax({
        data: data,
        type: "POST",
        url: Routing.generate('aide_upload_image'),
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {

            $('.js_aide_3_contenu').summernote('insertImage', data);
        }
    });
}

function setContenuAide2(aide1,aide2){
    $.ajax({
        url: Routing.generate('centre_aide_2_contenu'),
        type: 'POST',
        async: true,
        data: {
            aide1Id:aide1,
            aide2Id:aide2
        },
        success: function (data) {
            if(data != -1) {
                $('.js_contenu_aide_2').html(data);
            }
        }
    });
}

function setContenuSearch(lien, search){

    $.ajax({
        url: lien,
        type: 'POST',
        data: {search: search},
        async: true,
        success: function (data) {

            $('#js_contenu_aide').html(data);

            var lien = Routing.generate('centre_aide');

            var navigation = "<ol class='breadcrumb'><li><a href="+lien+">Centre d'aide PicData</a></li><li>Recherche</li></ol>";

            $('#js_navigation').html(navigation);

        }

    });
}


function setCentreAide1(winWidth){
    if(winWidth <= 	576){
        $('.desc').css({display:'none'});
    }
    else{
        $('.desc').css({display:'block'});
    }
}


function checkIfEmpty(aide3, typeContenu) {
    $.ajax({
        url: Routing.generate('aide_3_check'),
        type: 'POST',
        data: {
            aide3Id: aide3,
            typeContenu: typeContenu
        },
        success: function (data) {



            if (data.existed === false) {

                swal({
                    title: "Il n'y pas encore de texte",
                    text: "Clickez sur Oui pour dupliquer le texte, sur Non pour rediger un nouveau!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Oui",
                    cancelButtonText: "Non",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }).then(
                    function (isConfirm) {
                        console.log(isConfirm);
                        if (isConfirm) {

                            var aHTML = $('.js_aide_3_contenu').summernote('code');
                            var motCles = $('.js_mot_cles').val();
                            save_aide_3(aHTML,motCles);
                            $('.js_aide_3_contenu').html(data.content);
                        }
                    }
                    , function (dismiss) {
                        if (dismiss === 'cancel') {
                            $('.js_aide_3_contenu').html("");
                        } else {
                            throw dismiss;
                        }
                    });
            }
            else{
                $('.js_aide_3_contenu').html(data.content);
            }
        }
    });
}