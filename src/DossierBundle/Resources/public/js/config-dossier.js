var window_height = window.innerHeight,
        config_client = $('#client-config'),
        config_site = $('#site-config'),
        tableau_config_dossier = $('#table_config_2'),
        now = new Date(),
        last_table_dossier_select,
        config_client_grid_height = $(window).height() - 80;
        index_ui_modal = 0;
$(function() {
	$(document).on('click', '.js-config-do-action', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var _this = $(this);
        setTimeout(function() {
			var rowId = _this.attr('data-select-row');
	        $('#table_config_2').jqGrid('saveRow', rowId, {
	            "aftersavefunc": function() {
	                reloadGridDossier();
	            }
	        });
	    }, 100);
        
    });

    $(document).on('click', '.js-caract-config-dossier', function () {
        index_ui_modal++;
        var id_t = 'js_table_caract_'+ index_ui_modal,
            new_table = '<table id="'+ id_t +'"></table>',
            options = { modal: false, resizable: true,title: 'Caracteristique' },
            rowId = $(this).closest('tr').attr('id');
        var formJuridique = $('#table_config_2').jqGrid('getCell',rowId,'config-do-jur');
        var dataClo = $('#table_config_2').jqGrid('getCell',rowId,'config-do-clo');
        var activite = $('#table_config_2').jqGrid('getCell',rowId,'config-do-act');
        var tvaReg = $('#table_config_2').jqGrid('getCell',rowId,'config-do-treg');
        var tvaGenerat = $('#table_config_2').jqGrid('getCell',rowId,'config-do-tgenr');
        var tvaPai = $('#table_config_2').jqGrid('getCell',rowId,'config-do-tpaye');
        var prestation = $('#table_config_2').jqGrid('getCell',rowId,'config-do-prest');
        var regFiscal = $('#table_config_2').jqGrid('getCell',rowId,'config-do-fisc');
        //modal_ui(options, new_table, false, 0.8, 0.6);
        show_modal(new_table,'Caracteristique',undefined,'modal-lg');
        var table_dossier_caract = $('#'+id_t);
        var rowDataCaract = [{ tb_form_jur: formJuridique, tb_date_clo: dataClo, tb_activite: activite, tb_reg_fisc: regFiscal, tb_tva_reg: tvaReg, tb_tva_generat: tvaGenerat, tb_tva_pai: tvaPai, tb_prestation: prestation }];
        var colNameModel = ['Forme juridique', 'Date clôture', 'Activité', 'Régime fiscal', 'Tva régime', 'Tva fait générateur', 'Tva paiement', 'Prestation'];
        var ColModelImage = [
                {
                    name: 'tb_form_jur',
                    index: 'tb_form_jur',
                    align: 'center',
                    editable: false,
                    sortable: true,
                    width: 40,
                    classes: 'js-tb-form-jur'
                },
                {
                    name: 'tb_date_clo',
                    index: 'tb_date_clo',
                    align: 'center',
                    editable: false,
                    sortable: true,
                    width: 40,
                    sorttype: 'date',
                    classes: 'js-tb-date-clo'
                },
                {
                    name: 'tb_activite',
                    index: 'tb_activite',
                    align: 'center',
                    editable: false,
                    sortable: true,
                    width: 40,
                    sorttype: 'date',
                    classes: 'js-tb-activite'
                },
                {
                    name: 'tb_reg_fisc',
                    index: 'tb_reg_fisc',
                    align: 'center',
                    editable: false,
                    sortable: true,
                    width: 40,
                    sorttype: 'date',
                    classes: 'js-tb-reg-fisc'
                },
                {
                    name: 'tb_tva_reg',
                    index: 'tb_tva_reg',
                    align: 'left',
                    width: 40,
                    editable: false,
                    sortable: true,
                    classes: 'js-tb-tva-reg'
                },
                {
                    name: 'tb_tva_generat',
                    index: 'tb_tva_generat',
                    align: 'left',
                    width: 40,
                    editable: false,
                    sortable: true,
                    classes: 'js-tb-tva-generat'
                },
                {
                    name: 'tb_tva_pai',
                    index: 'tb_tva_pai',
                    align: 'left',
                    width: 80,
                    editable: false,
                    sortable: true,
                    classes: 'js-tb-tva-pai'
                },
                {
                    name: 'tb_prestation',
                    index: 'tb_prestation',
                    align: 'left',
                    width: 40,
                    editable: false,
                    sortable: true,
                    classes: 'js-tb-prestation'
                }
            ];
        table_dossier_caract.jqGrid({
            datatype: 'local',
            height: 300,
            autowidth: true,
            rownumbers: true,
            viewrecords: true,
            hidegrid: false,
            shrinkToFit: true,
            loadonce: true,
            altRows: true,
            sortable: true,
            colNames: colNameModel,
            colModel: ColModelImage,
            loadComplete: function (data) {
                
            },
            ajaxRowOptions: {async: true}
        });

        for(var i=0;i<=rowDataCaract.length;i++){
            table_dossier_caract.jqGrid('addRowData',i+1,rowDataCaract[i]);
        }
        //resize_tab_image();
    });

    $(document).on('click', '.js-user-config-dossier', function () {
        index_ui_modal++;
        var id_t = 'js_table_user_'+ index_ui_modal,
            new_table = '<table id="'+ id_t +'"></table>',
            options = { modal: false, resizable: true,title: 'Utilisateur' },
            rowId = $(this).closest('tr').attr('id');
        var nom = $('#table_config_2').jqGrid('getCell',rowId,'config-do-nom');
        var prenom = $('#table_config_2').jqGrid('getCell',rowId,'config-do-prnm');
        var statut = $('#table_config_2').jqGrid('getCell',rowId,'config-do-satutUser');
        var role = $('#table_config_2').jqGrid('getCell',rowId,'config-do-role');
        var tel = $('#table_config_2').jqGrid('getCell',rowId,'config-do-tel');
        var mail1 = $('#table_config_2').jqGrid('getCell',rowId,'config-do-mail1');
        var mail2 = $('#table_config_2').jqGrid('getCell',rowId,'config-do-mail2');
        //modal_ui(options, new_table, false, 0.8, 0.6);
        show_modal(new_table,'Utilisateur',undefined,'modal-lg');
        var table_dossier_user = $('#'+id_t);
        var rowDataUser = [{ tb_nom: nom, tb_prenom: prenom, tb_statut: statut, tb_role: role, tb_tel: tel, tb_mail1: mail1, tb_mail2: mail2 }];
        var colNameModelUser = ['Nom', 'Prenom', 'Statut', 'Role', 'Téléphone', 'Mail1', 'Mail2'];
        var ColModelImageUser = [
                {
                    name: 'tb_nom',
                    index: 'tb_nom',
                    align: 'center',
                    editable: false,
                    sortable: true,
                    width: 40,
                    classes: 'js-tb-nom'
                },
                {
                    name: 'tb_prenom',
                    index: 'tb_prenom',
                    align: 'center',
                    editable: false,
                    sortable: true,
                    width: 40,
                    classes: 'js-tb-prenom'
                },
                {
                    name: 'tb_statut',
                    index: 'tb_statut',
                    align: 'center',
                    editable: false,
                    sortable: true,
                    width: 40,
                    sorttype: 'date',
                    classes: 'js-tb-statut'
                },
                {
                    name: 'tb_role',
                    index: 'tb_role',
                    align: 'center',
                    editable: false,
                    sortable: true,
                    width: 40,
                    sorttype: 'date',
                    classes: 'js-tb-role'
                },
                {
                    name: 'tb_tel',
                    index: 'tb_tel',
                    align: 'left',
                    width: 40,
                    editable: false,
                    sortable: true,
                    classes: 'js-tb-tel'
                },
                {
                    name: 'tb_mail1',
                    index: 'tb_mail1',
                    align: 'left',
                    width: 40,
                    editable: false,
                    sortable: true,
                    classes: 'js-tb-mail1'
                },
                {
                    name: 'tb_mail2',
                    index: 'tb_mail2',
                    align: 'left',
                    width: 80,
                    editable: false,
                    sortable: true,
                    classes: 'js-tb-mail2'
                }
            ];
        table_dossier_user.jqGrid({
            datatype: 'local',
            height: 300,
            autowidth: true,
            rownumbers: true,
            viewrecords: true,
            hidegrid: false,
            shrinkToFit: true,
            loadonce: true,
            altRows: true,
            sortable: true,
            colNames: colNameModelUser,
            colModel: ColModelImageUser,
            loadComplete: function (data) {
                
            },
            ajaxRowOptions: {async: true}
        });

        for(var i=0;i<=rowDataUser.length;i++){
            table_dossier_user.jqGrid('addRowData',i+1,rowDataUser[i]);
        }
        //resize_tab_image();
    });

    $(document).on('change', '#envoi-connexion-dossier', function() {
        var field = 'EnvoiConnexion',
            value = $(this).prop('checked') ? 1 : 0;
        editParamAllConfigDossier(field, value);
    });

    $(document).on('change', '#envoi-creation-dossier', function() {
        var field = 'EnvoiCreation',
            value = $(this).prop('checked') ? 1 : 0;
        editParamAllConfigDossier(field, value);
    });

    $(document).on('change', '#envoi-image-dossier', function() {
        var field = 'EnvoiImage',
            value = $(this).prop('checked') ? 1 : 0;
        editParamAllConfigDossier(field, value);
    });

    $(document).on('change', '#envoi-relimage-dossier', function() {
        var field = 'EnvoiRelImage',
            value = $(this).prop('checked') ? 1 : 0;
        editParamAllConfigDossier(field, value);
    });

    $(document).on('change', '#envoi-relpm-dossier', function() {
        var field = 'EnvoiRelPm',
            value = $(this).prop('checked') ? 1 : 0;
        editParamAllConfigDossier(field, value);
    });

    $(document).on('change', '#envoi-relbanque-dossier', function() {
        var field = 'EnvoiRelBq',
            value = $(this).prop('checked') ? 1 : 0;
        editParamAllConfigDossier(field, value);
    });
});

function editParamAllConfigDossier(field, value) {
    swal({
        title: 'Attention',
        text: "Voulez-vous modifier le paramètre pour tous les clients ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1ab394',
        cancelButtonColor: '#f8ac59',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                var client = $('#client-config').val(),
                    site = $('#site-config').val(),
                    url = Routing.generate('dossier_config_param_edit_all_envoi_mail'),
                    formData = new FormData();
                formData.append('client', client);
                formData.append('site', site);
                formData.append('field', field);
                formData.append('value', value);

                fetch(url, {
                    method: 'POST',
                    credentials: 'include',
                    body: formData
                }).then(function (response) {
                    return response.json();
                }).then(function (data) {
                    resolve();
                    reloadGridDossier();
                }).catch(function (error) {
                    show_info('', 'Une erreur est survenue. Merci de réessayer.', 'error');
                });
            })
        }
    });
}

function setCheckAllEnvoiMailDossier() {
    setCheckHeaderMailDossier($(document).find('#envoi-connexion-dossier'), '.js-config-do-conex');
    setCheckHeaderMailDossier($(document).find('#envoi-creation-dossier'), '.js-config-do-creat');
    setCheckHeaderMailDossier($(document).find('#envoi-image-dossier'), '.js-config-do-envoimg');
    setCheckHeaderMailDossier($(document).find('#envoi-relimage-dossier'), '.js-config-do-relimg');
    setCheckHeaderMailDossier($(document).find('#envoi-relbanque-dossier'), '.js-config-do-relbq');
    setCheckHeaderMailDossier($(document).find('#envoi-relpm-dossier'), '.js-config-do-relpm');
}

function setCheckHeaderMailDossier(headerCheckbox, checkClass) {
    var value = true;
    $(document).find(checkClass).each(function (index, item) {
        value = $(item).html();
        if(value === 'Non' || value == 'Manuel'){
            value = false;
        }
        /*if ($(item).prop('checked') === false) {
            value = false;
            headerCheckbox.prop('checked', value);
            return 0;
        }*/
    });
    headerCheckbox.prop('checked', value);
}

function set_table_config_dossier(tableau_config_dossier, config_client_grid_height, last_table_select) {
 	tableau_config_dossier.jqGrid({
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
        caption: ' ',
        editurl: Routing.generate('dossier_admin_config_dossier_edit'),
        colNames: [
            'Dossier',
            'Statut', 
            'Date création',
            'Site',
            'Caracteristique',
            'Utilisateur',
            'Forme juridique',
            'Date clôture',
            'Activité', 
            'Regime fiscal', 
            'Tva régime', 
            'Tva fait générateur', 
            'Tva paiement', 
            'Prestation', 
            'Banque importee',
            'Nom',
            'Prenom',
            'Statut client final',
            'Role',
            'Téléphone',
            'Mail 1',
            'Mail 2',
            'Connexion<br><input type="checkbox" id="envoi-connexion-dossier" onclick="checkHeaderClick(event)">',
            'Création<br><input type="checkbox" id="envoi-creation-dossier" onclick="checkHeaderClick(event)">',
            'Envoi images<br><input type="checkbox" id="envoi-image-dossier" onclick="checkHeaderClick(event)">',
            'Relance image<br><input type="checkbox" id="envoi-relimage-dossier" onclick="checkHeaderClick(event)">',
            'Relance banque<br><input type="checkbox" id="envoi-relbanque-dossier" onclick="checkHeaderClick(event)">',
            'Relance pm<br><input type="checkbox" id="envoi-relpm-dossier" onclick="checkHeaderClick(event)">',
            'Action'
        ],
        colModel: [
            {
                name: 'config-do-nomdo', index: 'config-do-nomdo', sortable: true, width: 100, classes: 'js-config-do-nomdo'
            },
            {
                name: 'config-do-stat', index: 'config-do-stat', sortable: true, width: 20, classes: 'js-config-do-stat'
            },
            {
                name: 'config-do-datecreat', index: 'config-do-datecreat', sortable: true, width: 80, classes: 'js-config-do-datecreat', align: 'center'
            },
            {
                name: 'config-do-site', index: 'config-do-site', sortable: true, width: 80, classes: 'js-config-do-site'
            },
            {
                name: 'config-do-caract', index: 'config-do-caract', classes: 'js-config-do-caract', editable: false,
                editoptions: {defaultValue: ''},
                width: 50,
                fixed: true,
                align: 'center'
            },
            {
                name: 'config-do-user', index: 'config-do-user', classes: 'js-config-do-user', editable: false,
                editoptions: {defaultValue: ''},
                width: 50,
                fixed: true,
                align: 'center'
            },
            {
                name: 'config-do-jur', index: 'config-do-jur', hidden: true, classes: 'js-config-do-jur'
            },
            {
                name: 'config-do-clo', index: 'config-do-fonct', hidden: true, classes: 'js-config-do-fonct'
            },
            {
                name: 'config-do-act', index: 'config-do-act',hidden: true, classes: 'js-config-do-act'
            },
            {
                name: 'config-do-fisc', index: 'config-do-fisc', hidden: true, classes: 'js-config-do-fisc'
            },
            {
                name: 'config-do-treg', index: 'config-do-treg', hidden: true, classes: 'js-config-do-treg'
            },
            {
                name: 'config-do-tgenr', index: 'config-do-tgenr', hidden: true, classes: 'js-config-do-tgenr'
            },
            {
                name: 'config-do-tpaye', index: 'config-do-tpaye', hidden: true, classes: 'js-config-do-tpaye'
            },
            {
                name: 'config-do-prest', index: 'config-do-prest', hidden: true, classes: 'js-config-do-prest'
            },
            {
                name: 'config-do-bnq', index: 'config-do-bnq', hidden: true, classes: 'js-config-do-bnq'
            },
            {
                name: 'config-do-nom', index: 'config-do-nom', hidden: true, classes: 'js-config-do-nom'
            },
            {
                name: 'config-do-prnm', index: 'config-do-prnm', hidden: true, classes: 'js-config-do-prnm'
            },
            {
                name: 'config-do-satutUser', index: 'config-do-satutUser', hidden: true, classes: 'js-config-do-satutUser'
            },
            {
                name: 'config-do-role', index: 'config-do-role', hidden: true, classes: 'js-config-do-role'
            },
            {
                name: 'config-do-tel', index: 'config-do-tel', hidden: true, classes: 'js-config-do-tel'
            },
            {
                name: 'config-do-mail1', index: 'config-do-mail1', hidden: true, classes: 'js-config-do-mail1'
            },
            {
                name: 'config-do-mail2', index: 'config-do-mail2', hidden: true, classes: 'js-config-do-mail2'
            },
            {
                name: 'config-do-conex', index: 'config-do-conex', sortable: true, width: 40, editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                }, classes: 'js-config-do-conex'
            },
            {
                name: 'config-do-creat', index: 'config-do-creat', sortable: true, width: 40, editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                }, classes: 'js-config-do-creat'
            },
            {
                name: 'config-do-envoimg', index: 'config-do-envoimg', sortable: true, width: 40, editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                }, classes: 'js-config-do-envoimg'
            },
            {
                name: 'config-do-relimg', index: 'config-do-relimg', sortable: true, width: 40, editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                }, classes: 'js-config-do-relimg'
            },
            {
                name: 'config-do-relbq', index: 'config-do-relbq', sortable: true, width: 40, editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('dossier_config_manuelAuto', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                }, classes: 'js-config-do-relbq'
            },
            {
                name: 'config-do-relpm', index: 'config-do-relpm', sortable: true, width: 40, editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('dossier_config_manuelAuto', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                }, classes: 'js-config-do-relpm'
            },
            {
                name: 'config-do-action', index: 'config-do-action', editable: false,
                editoptions: {defaultValue: ''},
                width: 50,
                fixed: true,
                align: 'center',
                classes: 'js-config-do-action'
            }
        ],
        ajaxRowOptions: { async: true },
        onSelectRow: function (id) {
            if (id && id !== last_table_select) {
                tableau_config_dossier.restoreRow(last_table_select);
                last_table_select = id;
            }
            tableau_config_dossier.editRow(id, true);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);
            target.closest('td').attr('data-select-row', rowid)

            return !item_action;
        },
        loadComplete: function() {
            $('.ui-jqgrid-titlebar').hide();

            tableau_config_dossier.jqGrid('destroyGroupHeader');

            tableau_config_dossier.jqGrid('setGroupHeaders', {
                useColSpanStyle: true,
                groupHeaders: [
                    {startColumnName: 'config-do-nomdo', numberOfColumns: 17, titleText: 'Identification Dossier'},
                    {startColumnName: 'config-do-conex', numberOfColumns: 6, titleText: 'Notification'}
                ]
            });
            setCheckAllEnvoiMailDossier();
        },

        ajaxRowOptions: {async: true},

        reloadGridOptions: {fromServer: true}
    });
} 

function reloadGridDossier() {
	var client = $('#client-config').val(),
        site = $('#site-config').val(),
        tableau = $('#table_config_2');
    tableau.jqGrid('setGridParam', {
        url: Routing.generate('dossier_admin_config_dossier', {client: client, site: site}),
        datatype: 'json'
    }).trigger('reloadGrid', [{page: 1, current: true}]);
}