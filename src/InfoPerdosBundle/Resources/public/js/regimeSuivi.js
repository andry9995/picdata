/**
 * Created by MAHARO on 12/01/2017.
 */
$(function (){
    var regimeSuiviGrid = $('#js_regimeSuivi_liste');
    var lastsel_regime;

    regimeSuiviGrid.jqGrid({
        url: Routing.generate('info_perdos_regimeSuivi'),
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
        pager: '#pager_regimeSuivi_liste',
        caption: "REGIME SUIVI",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_regimeSuivi_edit'),
        colNames: ['Libelle', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [

            {name: 'regimeSuivi-libelle', index: 'regimeSuivi-libelle', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-regimeSuivi-libelle'},

            {name: 'action', index: 'action', width: 30, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-trash icon-action js-delete-regimeSuivi" title="Supprimer"></i>'},
                classes: 'js-regimeSuivi-action'}
        ],
        onSelectRow: function (id) {
            if (id)
            {

                regimeSuiviGrid.restoreRow(lastsel_regime);
                regimeSuiviGrid.editRow(id, true);
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

            if($("#btn-add-regimeSuivi" + name).length == 0)
            {
                regimeSuiviGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-regimeSuivi" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }


        },
        ajaxRowOptions: {async: true}

    });


    // Ajouter nouvelle regime suivi
    $(document).on('click', '#btn-add-regimeSuivi', function(event) {
        event.preventDefault();
        regimeSuiviGrid.jqGrid('addRow', {
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
    $(document).on('click', '.js-delete-regimeSuivi', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        regimeSuiviGrid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_regimeSuivi_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

    $(document).on("click", ".jqgrid-tabs a", function ()
    {
        regimeSuiviGrid.jqGrid("setGridWidth", regimeSuiviGrid.closest(".tab-content").width());

    });

    $(document).on('click', '#js-caracteristique-tab', function(event)
    {
        $('#js-regime-fiscal-tab').click();
    });

    $(document).on('click', '#js-statut-dirigeant-tab', function(event) {
        $('#js-regime-suivi-tab').click();
    });

});
