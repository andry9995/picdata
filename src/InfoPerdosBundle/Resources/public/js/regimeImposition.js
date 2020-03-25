$(function (){
    var regimeImpositionGrid = $('#js_regimeImposition_liste');
    var lastsel_regime;

    regimeImpositionGrid.jqGrid({
        url: Routing.generate('info_perdos_regimeImposition'),
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
        pager: '#pager_liste_regimeImposition',
        caption: "REGIMES IMPOSITIONS",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_regimeImposition_edit'),
        colNames: ['Libelle', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [

            {name: 'regimeImposition-libelle',index: 'regimeImposition-libelle', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-regimeImposition-libelle'},

            {name: 'action', index: 'action', width: 30, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-trash icon-action js-delete-regimeImposition" title="Supprimer"></i>'},
                classes: 'js-regimeImposition-action'}
        ],
        onSelectRow: function (id) {
            if (id)
            {

                regimeImpositionGrid.restoreRow(lastsel_regime);
                regimeImpositionGrid.editRow(id, true);
                lastsel_regime = id;
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

            if($("#btn-add-regimeImposition" + name).length == 0)
            {
                regimeImpositionGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-regimeImposition" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }


        },
        ajaxRowOptions: {async: true}

    });

    // Ajouter nouvelle regime imposition
    $(document).on('click', '#btn-add-regimeImposition', function(event) {
        event.preventDefault();
        regimeImpositionGrid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {extraparam:{}}
        });
        $("#" + "new_row", "#js_regimeImposition_liste").effect("highlight", 20000);
    });

    // Supprimer regime imposition
    $(document).on('click', '.js-delete-regimeImposition', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        regimeImpositionGrid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_regimeImposition_remove'),
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
        regimeImpositionGrid.jqGrid("setGridWidth", regimeImpositionGrid.closest(".tab-content").width())});
});