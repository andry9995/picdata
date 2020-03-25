/**
 * Created by TEFY on 09/12/2016.
 */

var jq_integer_format = {thousandsSeparator: " ", defaultValue: ''};
var jq_number_format = {decimalSeparator: ".", thousandsSeparator: " ", decimalPlaces: 4, defaultValue: ''};

function reloadGrid(selector, url, callback) {
    selector.setGridParam({
        url: url,
        datatype: 'json',
        loadonce: true,
        page: 1
    }).trigger('reloadGrid');

    if (typeof callback === 'function') {
        callback();
    }
}

$(document).on('change', '#client', function (event) {
    event.preventDefault();
    var client = $(this).val();
    var prestation_client_grid = $('#js_prestation_client');
    clearGrid(prestation_client_grid);
    var tarif_client_grid = $('#js_tarif_client');
    clearGrid(tarif_client_grid);
    $.ajax({
        url: Routing.generate('app_sites', {conteneur: 0, client: client, tous: 1}),
        success: function (data) {
            $('#js_conteneur_site').html(data);
            $(document)
                .find('#site')
                .trigger('change');
        }
    });
});

$(document).on('change', '#site', function (event) {
    event.preventDefault();
    var site = $(this).val();
    var prestation_client_grid = $('#js_prestation_client');
    clearGrid(prestation_client_grid);
    var tarif_client_grid = $('#js_tarif_client');
    clearGrid(tarif_client_grid);
    var client = $(document)
        .find('#client')
        .val();
    $.ajax({
        url: Routing.generate('app_dossiers_tmp', {conteneur: 0, client: client, site: site, tous: 1}),
        success: function (data) {
            $('#js_conteneur_dossier').html(data);
        }
    });
});

$(document).on('change', '#dossier', function (event) {
    event.preventDefault();
    var prestation_client_grid = $('#js_prestation_client');
    clearGrid(prestation_client_grid);
    var tarif_client_grid = $('#js_tarif_client');
    clearGrid(tarif_client_grid);
});

function clearGrid(selector) {
    var trf = selector.find("tbody:first tr:first")[0];
    selector.find("tbody:first").empty().append(trf);
}

function getGridColumnIndexByName(grid, columnName) {
    var cm = grid.jqGrid('getGridParam', 'colModel');
    for (var i = 0, l = cm.length; i < l; i++) {
        if (cm[i].name === columnName) {
            return i;
        }
    }
    return -1;
}

function setGridHeight(selector, height) {
    selector.jqGrid("setGridHeight", height);
}