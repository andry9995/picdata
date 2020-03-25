/**
 * Created by MAHARO on 12/01/2017.
 */
$(function (){
    var contratPrevoyanceGrid = $('#js_contratPrevoyance_liste');
    var lastsel_contrat;

    contratPrevoyanceGrid.jqGrid({
        url: Routing.generate('info_perdos_contratPrevoyance'),
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
        pager: '#pager_contratPrevoyance_liste',
        caption: "CONTRATS PREVOYANCE",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_contratPrevoyance_edit'),
        colNames: ['Libelle', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [

            {name: 'contratPrevoyance-libelle', index: 'contratPrevoyance-libelle', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-contratPrevoyance-libelle'},

            {name: 'action', index: 'action', width: 30, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-trash icon-action js-delete-contratPrevoyance" title="Supprimer"></i>'},
                classes: 'js-contratPrevoyance-action'}
        ],
        onSelectRow: function (id) {
            if (id)
            {
                contratPrevoyanceGrid.restoreRow(lastsel_contrat);
                contratPrevoyanceGrid.editRow(id, true);
                lastsel_contrat = id;
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
            if($("#btn-add-contratPrevoyance" + name).length == 0)
            {
                contratPrevoyanceGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-contratPrevoyance" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }

        },
        ajaxRowOptions: {async: true}

    });

    // Ajouter nouvelle contrat prevoyance
    $(document).on('click', '#btn-add-contratPrevoyance', function(event) {
        event.preventDefault();
        contratPrevoyanceGrid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {extraparam:{}}
        });
        $("#" + "new_row", "#js_contratPrevoyance_liste").effect("highlight", 20000);
    });


    // Supprimer contrat prevoyance
    $(document).on('click', '.js-delete-contratPrevoyance', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        contratPrevoyanceGrid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_contratPrevoyance_remove'),
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
        contratPrevoyanceGrid.jqGrid("setGridWidth", contratPrevoyanceGrid.closest(".tab-content").width())});
});