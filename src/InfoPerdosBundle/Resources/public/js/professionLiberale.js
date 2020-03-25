$(function() {
    var lastsel_professionLiberale;
       var professionLiberaleGrid = $('#js_professionLiberale_liste');


    //Liste prestation générale
    professionLiberaleGrid.jqGrid({
        url: Routing.generate('info_perdos_professionLiberale'),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: 500,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        pager: '#pager_professionLiberale_liste',
        caption: "PROFESSION LIBERALE",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_professionLiberale_edit'),
        colNames: ['Libelle', 'Alpha', 'Categorie', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [

            {name: 'pl-libelle',index: 'pl-libelle', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-pl-libelle'},

            {name: 'pl-alpha',index: 'pl-alpha', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-pl-alpha'},

            {name: 'pl-categorie', index: 'pl-categorie', editable: true, align: "center",  width: 150, fixed: true, edittype: 'select',
                editoptions: { dataUrl: Routing.generate('info_perdos_professionLiberaleCategorie', {json: 0}),
                    dataInit: function (elem) { $(elem).width(100); }}, classes: 'js-pl-categorie'},

            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-professionLiberale" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-professionLiberale" title="Supprimer"></i>'},
                classes: 'js-professionLiberale-action'}
        ],
        onSelectRow: function (id) {
            if (id) {
                professionLiberaleGrid.restoreRow(lastsel_professionLiberale);
                professionLiberaleGrid.editRow(id);
                lastsel_professionLiberale = id;
            }
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            if (item_action) {
                return false;
            }
            return true;
        },

        loadComplete: function() {
            if (professionLiberaleGrid.closest('.ui-jqgrid').find('#btn-add-professionLiberale').length == 0)
            {
                professionLiberaleGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-professionLiberale" class="btn btn-outline btn-primary btn-xs">Ajouter</button></div>');
            }


        },
        ajaxRowOptions: {async: true},
        reloadGridOptions: { fromServer: true }
    });

    // Enregistrement modif ProfessionLiberale
    $(document).on('click', '.js-save-professionLiberale', function (event) {
        event.preventDefault();
        event.stopPropagation();
        professionLiberaleGrid.jqGrid('saveRow', lastsel_professionLiberale, {
            "aftersavefunc": function() {
                reloadGrid(professionLiberaleGrid, Routing.generate('info_perdos_professionLiberale'));
            }
        });
    });

    // Ajouter nouvelle profession liberale
    $(document).on('click', '#btn-add-professionLiberale', function(event) {
        event.preventDefault();
        professionLiberaleGrid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {extraparam:{}}
        });
        $("#" + "new_row", "#js_prestation_general").effect("highlight", 20000);
    });

    // Supprimer une profession liberale
    $(document).on('click', '.js-remove-professionLiberale', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        professionLiberaleGrid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_professionLiberale_remove'),
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
        professionLiberaleGrid.jqGrid("setGridWidth", professionLiberaleGrid.closest(".tab-content").width())});


});