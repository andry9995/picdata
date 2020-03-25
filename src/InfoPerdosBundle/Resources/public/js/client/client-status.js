$(function() {

    var grid_statut_client = $('#js_status_client');
    var window_height = window.innerHeight;
    var grid_statut_client_width = grid_statut_client.closest("div.row").width();
    var grid_statut_client_height = window_height - 200;
    var lastsel;
    var scrollPosition;
    var canFilter = false;

    $('.navbar-minimalize').click(function () {
        setGridSize();
    });

    grid_statut_client.jqGrid({
        datatype: 'json',
        url: Routing.generate('info_perdos_client_status_liste'),
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: grid_statut_client_height,
        width: grid_statut_client_width,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 2000,
        rowList: [2000, 3000, 5000],
        pager: '#pager_status_client',
        hidegrid: false,
        rownumbers: true,
        rownumWidth: 45,
        caption: 'statut des clients',
        colNames: ['Client', 'Status_Code', 'Statut', 'Date modification'],
        colModel: [
            {name: 'client', index: 'client', editable: false, sortable: true, width: 100, align: "left", classes: 'js-client'},
            {name: 'status-code', index: 'status-code', hidden: true, classes: 'js-status-code'},
            {name: 'status', index: 'status', editable: true, sortable: true, formatter: 'checkbox', edittype: 'checkbox',
                editoptions: { value:"1:0" }, width: 50, align: "center", classes: 'js-status'},
            {name: 'date', index: 'date', editable: false, sortable: true, width: 50, align: "center",
                formatter: "date", formatoptions: { srcformat: "d/m/Y", newformat: "d/m/Y" }, classes: 'js-date'}
        ],
        beforeSelectRow: function(rowid, e) {
            return $(e.target).hasClass('js-status');
        },
        onSelectRow: function (id) {
            if(id && id !== lastsel){
                $(this).restoreRow(lastsel);
                lastsel=id;
            }
            $(this).editRow(id, true);
        },
        loadComplete: function() {
            var filtre = '<div id="filtre-status" class="pull-left" style="margin-left: 100px;padding-top: 12px;">';
            filtre += '<div class="radio radio-inline">';
            filtre += '<input type="radio" id="filtre-status-tous" value="all" name="filtre-status-choice" checked="">';
            filtre += '<label for="filtre-status-tous">Tous</label>';
            filtre += '</div>';
            filtre += '<div class="radio radio-inline">';
            filtre += '<input type="radio" id="filtre-status-actif" value="1" name="filtre-status-choice">';
            filtre += '<label for="filtre-status-actif">Actifs</label>';
            filtre += '</div>';
            filtre += '<div class="radio radio-inline">';
            filtre += '<input type="radio" id="filtre-status-inactif" value="0" name="filtre-status-choice">';
            filtre += '<label for="filtre-status-suspendu">Inactifs</label>';
            filtre += '</div>';
            filtre += '</div>';

            if (grid_statut_client.closest('.ui-jqgrid').find('#filtre-status').length === 0) {
                grid_statut_client.closest('.ui-jqgrid').find('.ui-jqgrid-title').after(filtre);
            }
            window_height = window.innerHeight;
            grid_statut_client.closest(".ui-jqgrid-bdiv").scrollTop(scrollPosition);
            scrollPosition = null;
            setTimeout(function() {
                filterClient();
            }, 0);
            setGridSize();
        },
        ajaxRowOptions: {async: true}
    });

    $(document).on('change', '.js-status input[type="checkbox"]', function() {
        var rowId = $(this).closest('tr').attr('id');
        scrollPosition = grid_statut_client.closest(".ui-jqgrid-bdiv").scrollTop();
        canFilter = true;
        grid_statut_client.jqGrid('saveRow',rowId, {
            "url": Routing.generate('info_perdos_client_status_edit', { client: rowId }),
            "aftersavefunc": function() {
                grid_statut_client.setGridParam({
                    url: Routing.generate('info_perdos_client_status_liste'),
                    datatype: 'json',
                    loadonce: true,
                    page: 1
                }).trigger('reloadGrid');
            }
        });
    });

    //Filtrer clients affichés
    $(document).on('change', 'input[name="filtre-status-choice"]', function(event) {
        event.preventDefault();
        canFilter = true;
        filterClient();
    });

    //Resize Document
    $(window).resize(function() {
        setGridSize();
    });

    function filterClient() {
        var value = setSearch();
        if (canFilter) {
            console.log(value);
            canFilter = false;
            grid_statut_client.trigger("reloadGrid", {page: 1});
        }
    }

    function setSearch() {
        var value = $(document).find('input:radio[name="filtre-status-choice"]:checked').val();
        if (typeof value !== 'undefined' && value !== 'all') {
            grid_statut_client.jqGrid("setGridParam", {
                postData: {
                    filters: JSON.stringify({
                        groupOp: "AND",
                        rules: [
                            {field: "status-code", op: "eq", data: value}
                        ]
                    })
                },
                search: true
            });
        }
        else {
            grid_statut_client.jqGrid("setGridParam", {
                search: false
            });
        }
        return value;
    }

    function setGridSize() {
        setTimeout(function() {
            window_height = window.innerHeight;
            grid_statut_client.jqGrid("setGridWidth", grid_statut_client.closest(".row").width() - 30);
            grid_statut_client.jqGrid("setGridHeight", window_height - 200);
        }, 600);
    }
});