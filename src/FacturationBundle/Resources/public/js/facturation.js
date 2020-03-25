/**
 * Created by TEFY on 18/01/2017.
 */
$(function() {

    setTimeout(function() {
        $('#client').trigger('change');
    }, 500);

    var facturation_grid = $('#js_facturation');
    var window_height = window.innerHeight;
    var code_prestations = [];

    $('#facturation .panel-body').height(window_height - 180);

    var current_year = moment().format('YYYY');
    $('#exercice')
        .find('option[selected]')
        .remove();
    $('#exercice').val(current_year);

    $(document).on('change', '#client, #exercice', function(event) {
       event.preventDefault();
       getMoisSaisiClient();
    });

    //Afficher facturation client
    $(document).on('click', '#btn-facturation', function(event) {
       event.preventDefault();
       LoadFacture(false);
    });

    //Recalculer prestation et afficher
    $(document).on('click', '#btn-recalculer', function(event) {
       event.preventDefault();
        LoadFacture(true);
    });

    //Export Excel
    $(document).on('click', '#btn-export-excel', function(event) {
        event.preventDefault();
        ExporterExcel();
    });

    function getMoisSaisiClient() {
        var client_id = $('#client').val();
        var exercice = $('#exercice').val();
        $('#select-saisi-fini').empty();

        $.ajax({
            url: Routing.generate('fact_saisie_mois_saisi_client', {client: client_id, exercice: exercice}),
            type: 'GET',
            success: function(data) {
                data = $.parseJSON(data);

                var option = '';
                $.each(data, function(index, item) {
                    var mois_text = moment(item.mois_saisi)
                        .format('MMMM-YYYY')
                        .toUpperCase();
                    var mois_value = moment(item.mois_saisi)
                        .format('MM-YYYY');
                    option += '<option value="' + mois_value + '">' + mois_text + '</option>';
                });
                $('#select-saisi-fini').html(option);
            }
        });
    }

    function LoadFacture(recalculer) {
        if (typeof recalculer === 'undefined') {
            recalculer = false;
        }
        var client_id = $('#client').val();
        var exercice = $('#exercice').val();
        var mois = $('#select-saisi-fini').val();
        var annee = $('#annee').val();
        facturation_grid.jqGrid('GridUnload');
        facturation_grid = $('#js_facturation');

        $.ajax({
            url: Routing.generate('fact_facturation_finale_liste', {client: client_id, mois: mois, exercice: exercice, annee_tarif: annee, recalculer: recalculer ? 1 : 0}),
            type: 'GET',
            data: {},
            success: function(data) {
                data = $.parseJSON(data);
                var rowData = data.rows_data;
                code_prestations = data.code_prestations;

                // console.log(data);
                facturation_grid.jqGrid({
                    datatype: 'local',
                    data: rowData,
                    sortable: false,
                    height: (window_height - 310),
                    shrinkToFit: false,
                    viewrecords: true,
                    footerrow: true,
                    rowNum: 1000,
                    rowList: [1000, 2000, 5000],
                    rownumbers: true,
                    rownumWidth: 35,
                    pager: '#pager_facturation',
                    hidegrid: false,
                    colNames: data.col_names,
                    colModel: data.col_models,
                    loadComplete: function() {
                        facturation_grid.jqGrid("setGridWidth", facturation_grid.closest(".panel-body").width());
                        facturation_grid.jqGrid("footerData", "set", {
                            'fact_cloture': "TOTAL"
                        }, true);
                        var total_220 = 0;
                        var total_prix = 0;
                        $.each(data.col_with_total, function(index, item) {
                            var col_name = 'fact_' + item;
                            var col_total = facturation_grid.jqGrid('getCol', col_name, true, "sum");
                            var obj = '[{"' + col_name + '": "' + parseFloat(col_total).toFixed(2) + '"}]';
                            var footer_obj = $.parseJSON(obj);

                            facturation_grid.jqGrid("footerData", "set", footer_obj[0]);

                            if (col_name === 'fact_220') {
                                total_220 = parseFloat(col_total).toFixed(2);
                            }
                            if (col_name === 'fact_total') {
                                total_prix = parseFloat(col_total).toFixed(2);
                            }
                        });
                        if (total_220 !== 0) {
                            facturation_grid.jqGrid("footerData", "set", {
                                'fact_pu_ligne': parseFloat(total_prix / total_220)
                            });
                        }

                        $('#nb-ligne-client').text(numeral(data.nb_ligne_client).format('0,0'));
                    }
                });
            }
        });
    }

    function ExporterExcel() {
        var client_id = $('#client').val();
        var exercice = $('#exercice').val();
        var mois = $('#select-saisi-fini').val();
        var annee = $('#annee').val();
        var colNames = facturation_grid.jqGrid("getGridParam", "colNames");
        $('#colNames').val(JSON.stringify(colNames));
        var colModel = facturation_grid.jqGrid("getGridParam", "colModel");
        $('#colModel').val(JSON.stringify(colModel));
        var rowData = facturation_grid.jqGrid("getGridParam", "data");
        $('#rowData').val(JSON.stringify(rowData));
        var footerData = facturation_grid.jqGrid("footerData");
        $('#footerData').val(JSON.stringify(footerData));

        $('#codePrestation').val(JSON.stringify(code_prestations));

        var url_export = Routing.generate('fact_facturation_finale_export', { client: client_id, mois: mois, exercice: exercice, annee_tarif: annee});
        $('#form-export')
            .attr('action', url_export)
            .submit();
    }
});
