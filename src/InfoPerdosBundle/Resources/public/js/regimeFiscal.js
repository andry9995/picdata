$(function (){
    var regimeFiscalGrid = $('#js_regimeFiscal_liste');
    var lastsel_regime;

    regimeFiscalGrid.jqGrid({
        url: Routing.generate('info_perdos_regimeFiscal'),
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
        caption: "REGIMES FISCAL",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_regimeFiscal_edit'),
        colNames: ['Libelle', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [

            {name: 'regimeFiscal-libelle', index: 'regimeFiscal-libelle', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-regimeFiscal-libelle'},

            {name: 'action', index: 'action', width: 30, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-trash icon-action js-delete-regimeFiscal" title="Supprimer"></i>'},
                classes: 'js-regimeFiscal-action'}
        ],
        onSelectRow: function (id) {
            if (id)
            {

                regimeFiscalGrid.restoreRow(lastsel_regime);
                regimeFiscalGrid.editRow(id, true);
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

        afterSubmit: function () {
            reloadGrid(regimeFiscalGrid, Routing.generate('info_perdos_regimeFiscal'));
        },
        loadComplete: function() {

            if($("#btn-add-regimeFiscal" + name).length == 0)
            {
                regimeFiscalGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-regimeFiscal" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }

            
        },


        ajaxRowOptions: {async: true}

    });


    function reloadGrid(selector, url, callback) {
        selector.setGridParam({
            url: url,
            datatype: 'json',
            loadonce: true,
            page: 1
        }).trigger('reloadGrid');

        if (typeof callback == 'function') {
            callback();
        }
    }
   


    // Ajouter nouvelle regime fiscal
    $(document).on('click', '#btn-add-regimeFiscal', function(event) {

        event.preventDefault();
        regimeFiscalGrid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {}


        });
        $("#" + "new_row", "#js_regimeFiscal_liste").effect("highlight", 20000);

    });


    // Supprimer regime fiscal
    $(document).on('click', '.js-delete-regimeFiscal', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        regimeFiscalGrid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_regimeFiscal_remove'),
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
        regimeFiscalGrid.jqGrid("setGridWidth", regimeFiscalGrid.closest(".tab-content").width())});
});