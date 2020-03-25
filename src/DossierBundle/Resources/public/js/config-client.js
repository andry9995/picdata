var window_height = window.innerHeight,
        config_client = $('#client-config'),
        config_site = $('#site-config'),
        tableau_config_client = $('#table_config_1'),
        last_table_select,
        now = new Date(),
        config_client_grid_height = $(window).height() - 80;
$(function() {
    $(document).on('click', '.js-save-config-client', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var _this = $(this);
        setTimeout(function() {
            var rowId = _this.parent().attr('data-select-row');
            $('#table_config_1').jqGrid('saveRow', rowId, {
                "aftersavefunc": function(rowid, response, options) {
                    if(response.responseText == 0)
                        show_info('', 'L\'adresse mail existe déjà', 'error');
                    reloadGridClient();
                }
            });
        }, 100);
    });

     $(document).on('click', '#btn-add-client', function (event) {
        if(canAddRow($('#table_config_1'))) {
            event.preventDefault();
            $('#table_config_1').jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
            $("#" + "new_row", "#js_infoCarac_responsableDossier_liste").effect("highlight", 20000);
        }

    });
});

function canAddRow(jqGrid) {
    var canAdd = true;
    var rows = jqGrid.find('tr');

    rows.each(function () {
        if ($(this).attr('id') == 'new_row') {
            canAdd = false;
        }
    });
    return canAdd;
}

function set_table_config_client(tableau_config_client, config_client_grid_height, last_table_select) {
 	tableau_config_client.jqGrid({
        datatype: 'local',
        loadonce: true,
        sortable: true,
        height: config_client_grid_height,
        autowidth: true,
        shrinkToFit: true,
        viewrecords: true,
        hidegrid: true,
        rownumbers: true,
        rownumWidth: 35,
        editable: true,
        editurl: Routing.generate('dossier_config_cli_stat_edit', {json: 1, client: $('#client-config').val()}),
        caption: '',
        colNames: [
            'Nom',
            'Prenom',
            'Site',
            'Statut',
            'Fonction client final',
            'Téléphone', 
            'Mail 1',
            'Mail 2',
            'Action'
        ],
        colModel: [
            {
                name: 'config-cli-nom', index: 'config-cli-nom', sortable: true, width: 40, editable: true,
                editoptions: {defaultValue: ''},
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid }, classes: 'js-config-cli-nom'
            },
            {
                name: 'config-cli-prnm', index: 'config-cli-prnm', sortable: true, width: 30, editable: true,
                editoptions: {defaultValue: ''},
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid }, classes: 'js-config-cli-prnm'
            },
            {
                name: 'config-cli-site', index: 'config-cli-site', sortable: true, width: 30, editable: true,
                editoptions: {defaultValue: $("#site-config option:selected").html().trim()},
                 edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('dossier_config_cli_site_edit', {client: $('#client-config').val()}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid }, classes: 'js-config-cli-site'
            },
            {
                name: 'config-cli-stat', index: 'config-cli-stat', sortable: true, width: 40, editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('dossier_config_cli_stat_edit', {json: 0, client: null}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                }, 
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid }, classes: 'js-config-cli-stat'
            },
            {
                name: 'config-cli-role', index: 'config-cli-role', sortable: true, width: 40, editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('dossier_config_cli_role_edit'),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid }, classes: 'js-config-cli-role'
            },
            {
                name: 'config-cli-tel', index: 'config-cli-tel', sortable: true, width: 20, editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-config-cli-tel'
            },
            {
                name: 'config-cli-mail1', index: 'config-cli-mail1', sortable: true, width: 20, editable: true,
                editoptions: {defaultValue: ''},
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid }, classes: 'js-config-cli-mail1'
            },
            {
                name: 'config-cli-mail2', index: 'config-cli-mail2', sortable: true, width: 20, editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-config-cli-mail2'
            },
            {
                name: 'config-cli-action', index: 'config-cli-action', editable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-config-client" title="Enregistrer"></i>'},
                width: 50,
                fixed: true,
                align: 'center',
                classes: 'js-config-cli-action'
            }
        ],
        ajaxRowOptions: { async: true },
        onSelectRow: function (id) {
            if (id && id !== last_table_select) {
                tableau_config_client.restoreRow(last_table_select);
                last_table_select = id;
            }
            tableau_config_client.editRow(id, true);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);
            target.closest('td').attr('data-select-row', rowid);
            return !item_action;
        },
        aftersavefunc: function (rowid) {
            reloadGridClient();
        },
        loadComplete: function() {
            tableau_config_client.closest('.ui-jqgrid').find('.ui-jqgrid-title').parent().css('display', 'block');
            tableau_config_client.closest('.ui-jqgrid').find('.ui-jqgrid-title').prev().css('display', 'none');
            if($("#btn-add-client").length == 0) {
                tableau_config_client.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-client" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }
        }
    });
} 

function reloadGridClient() {
	var client = $('#client-config').val(),
        site = $('#site-config').val(),
        tableau_rappel_pm = $('#table_config_1');
    tableau_rappel_pm.jqGrid('setGridParam', {
        url: Routing.generate('dossier_admin_config_cabinet', {client: client, site: site}),
        datatype: 'json'
    }).trigger('reloadGrid', [{page: 1, current: true}]);
}

function verifier_champ_obligatoire_jqgrid(posdata, colName) {
    var message = "";
    if (posdata != '')
        return [true, ""];

    if (posdata == '')
        message = "Le champ " + colName + " est obligatoire";

    setTimeout(function () {
        $("#info_dialog").hide();
    }, 10);

    show_info_client('INFORMATION', message, 'warning');

    return [false, ""];

}

function show_info_client(titre, message, type) {
    type = typeof type === 'undefined' ? 'success' : type;
    setTimeout(function () {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            showMethod: 'slideDown',
            timeOut: 5000
        };
        if (type == 'success') toastr.success(message, titre);
        if (type == 'warning'){

            toastr.options = {
                closeButton: true,
                "positionClass": "toast-top-center",
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 5000
            };


            toastr.warning(message, titre);
        }
        if (type == 'error') toastr.error(message, titre);
        if (type == 'info') toastr.info(message, titre);
    }, 500);
}