/**
 * Created by MAHARO on 11/01/2017.
 */
$(function (){
    var typeActiviteGrid = $('#js_typeActivite_liste');
    var lastsel_typeActivite;

    typeActiviteGrid.jqGrid({
        url: Routing.generate('info_perdos_typeActivite'),
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
        pager: '#pager_typeActivite_liste',
        caption: "TYPE ACTIVITES",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_typeActivite_edit'),
        colNames: ['Libelle', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [

            {name: 'typeActivite-libelle',index: 'typeActivite-libelle', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-typeActivite-libelle'},

            {name: 'action', index: 'action', width: 30, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-trash icon-action js-delete-typeActivite" title="Supprimer"></i>'},
                classes: 'js-typeActivite-action'}
        ],
        onSelectRow: function (id) {
            if (id)
            {
                typeActiviteGrid.restoreRow(lastsel_typeActivite);
                typeActiviteGrid.editRow(id, true);
                lastsel_typeActivite = id;
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

            if($("#btn-add-typeActivite" + name).length == 0)
            {
                typeActiviteGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-typeActivite" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }
        },
        ajaxRowOptions: {async: true}

    });

    // Ajouter nouvelle type Activite
    $(document).on('click', '#btn-add-typeActivite', function(event) {
        event.preventDefault();
        typeActiviteGrid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {extraparam:{}}
        });
        $("#" + "new_row", "#js_typeActivite_liste").effect("highlight", 20000);
    });

    // Supprimer type Activite
    $(document).on('click', '.js-delete-typeActivite', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        typeActiviteGrid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_typeActivite_remove'),
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
        typeActiviteGrid.jqGrid("setGridWidth", typeActiviteGrid.closest(".tab-content").width())});
});