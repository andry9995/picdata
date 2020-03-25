/**
 * Created by papa on 12/01/2017.
 */
/**
 * Created by MAHARO on 11/01/2017.
 */
$(function (){
    var typeVenteGrid = $('#js_typeVente_liste');
    var lastsel_typeVente;

    typeVenteGrid.jqGrid({
        url: Routing.generate('info_perdos_typeVente'),
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
        pager: '#pager_typeVente_liste',
        caption: "TYPE VENTES",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_typeVente_edit'),
        colNames: ['Libelle', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [

            {name: 'typeVente-libelle',index: 'typeVente-libelle', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-typeVente-libelle'},

            {name: 'action', index: 'action', width: 30, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-trash icon-action js-delete-typeVente" title="Supprimer"></i>'},
                classes: 'js-typeVente-action'}
        ],
        onSelectRow: function (id) {
            if (id)
            {
                typeVenteGrid.restoreRow(lastsel_typeVente);
                typeVenteGrid.editRow(id, true);
                lastsel_typeVente = id;
            }
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var cell_action = target.hasClass('js-vente-action');
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            if (cell_action || item_action)
            {
                return false;
            }
            return true;

        },
        loadComplete: function() {

            if($("#btn-add-typeVente" + name).length == 0)
            {
                typeVenteGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-typeVente" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }

          

        },
        ajaxRowOptions: {async: true}

    });

    // Ajouter nouvelle type Activite
    $(document).on('click', '#btn-add-typeVente', function(event) {
        event.preventDefault();
        typeVenteGrid.jqGrid('addRow', {
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
    $(document).on('click', '.js-delete-typeVente', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        typeVenteGrid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_typeVente_remove'),
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
        typeVenteGrid.jqGrid("setGridWidth", typeVenteGrid.closest(".tab-content").width())});

});