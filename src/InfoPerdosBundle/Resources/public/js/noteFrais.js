/**
 * Created by MAHARO on 13/01/2017.
 */
$(function (){
    var noteFraisGrid = $('#js_noteFrais_liste');
    var lastsel_noteFrais;

    noteFraisGrid.jqGrid({
        url: Routing.generate('info_perdos_noteFrais'),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: 500,
        shrinkToFit: true,
        viewrecords: true,
        rownumbers: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        pager: '#pager_liste_regimeFiscal',
        caption: "TYPE DE GESTION DE FRAIS",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_noteFrais_edit'),
        colNames: ['Libelle', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [

            {name: 'noteFrais-libelle', index: 'noteFrais-libelle', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-noteFrais-libelle'},

            {name: 'action', index: 'action', width: 30, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-trash icon-action js-delete-noteFrais" title="Supprimer"></i>'},
                classes: 'js-noteFrais-action'}
        ],
        onSelectRow: function (id) {
            if (id)
            {

                noteFraisGrid.restoreRow(lastsel_noteFrais);
                noteFraisGrid.editRow(id, true);
                lastsel_noteFrais = id;
            }
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var cell_action = target.hasClass('js-activite-action');
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            if (cell_action || item_action)
            {
                return false;
            }
            return true;


        },
        loadComplete: function() {

            if($("#btn-add-noteFrais" + name).length == 0)
            {
                noteFraisGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-noteFrais" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }


        },


        ajaxRowOptions: {async: true}

    });




    // Ajouter nouvelle regime fiscal
    $(document).on('click', '#btn-add-noteFrais', function(event) {

        event.preventDefault();
        noteFraisGrid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {}


        });
        $("#" + "new_row", "#js_notefrais_liste").effect("highlight", 20000);

    });


    // Supprimer regime fiscal
    $(document).on('click', '.js-delete-noteFrais', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        noteFraisGrid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_noteFrais_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

    $(document).on("click", ".jqgrid-tabs a", function () {
        noteFraisGrid.jqGrid("setGridWidth", noteFraisGrid.closest(".tab-content").width())});
});
