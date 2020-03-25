/**
 * Created by MAHARO on 12/01/2017.
 */
$(function (){
    var regimeTvaGrid = $('#js_regimeTva_liste');
    var lastsel_regime;

    regimeTvaGrid.jqGrid({
        url: Routing.generate('info_perdos_regimeTva'),
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
        pager: '#pager_regimeTva_liste',
        caption: "REGIMES TVA",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_regimeTva_edit'),
        colNames: ['Libelle', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [

            {name: 'regimeTva-libelle', index: 'regimeTva-libelle', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-regimeTva-libelle'},

            {name: 'action', index: 'action', width: 30, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-trash icon-action js-delete-regimeTva" title="Supprimer"></i>'},
                classes: 'js-regimeTva-action'}
        ],
        onSelectRow: function (id) {
            if (id)
            {

                regimeTvaGrid.restoreRow(lastsel_regime);
                regimeTvaGrid.editRow(id, true);
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

            if($("#btn-add-regimeTva" + name).length == 0)
            {
                regimeTvaGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-regimeTva" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }


        },
        ajaxRowOptions: {async: true}

    });




    // Ajouter nouvelle regime tva
    $(document).on('click', '#btn-add-regimeTva', function(event) {
        event.preventDefault();
        regimeTvaGrid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {extraparam:{}}
        });
        $("#" + "new_row", "#js_regimeTva_liste").effect("highlight", 20000);
    });


    // Supprimer regime fiscal
    $(document).on('click', '.js-delete-regimeTva', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        regimeTvaGrid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_regimeTva_remove'),
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
        regimeTvaGrid.jqGrid("setGridWidth", regimeTvaGrid.closest(".tab-content").width())});

});
