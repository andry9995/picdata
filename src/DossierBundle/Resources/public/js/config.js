/**
 * Created by DINOH on 04/12/2019.
 */
 var all_datas = [];
$(document).ready(function(){
    var c_el = $('#client-config');
    var s_el = $('#site-config');
	c_el.on('change', function(event) {
        event.preventDefault();
        getSitesConfig(c_el, s_el);
    });

	setTimeout(function() {
        c_el.trigger('change');
    }, 10);

    s_el.on('change', function(event) {
       event.preventDefault();
       go();
    });

	$(document).on('click','.config_tab_li',function(){
        go();
    });

    $(document).on('change', '#filtre-status-stop-saisie', function(event) {
        event.preventDefault();
        var tab = $('#table_config_0');
        filterByDossierStatus(tab);
    });

    $(document).on('click', '.event-resend-mail-creation', function(event) {
        event.preventDefault();
        var userId = $(this).attr('data-id');
        swal({
            title: 'Voulez-vous envoyer de nouveau le mail de création de compte ?',
            text: "L'utilisateur doit changer sont mot de passe à la première connexion.",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#18a689',
            cancelButtonColor: '#f8ac59',
            confirmButtonText: 'Oui, envoyer!',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then(function () {
            $.ajax({
                url: Routing.generate('dossier_admin_renvoi_mail_creation'),
                data: {user: userId},
                type: 'POST',
                success: function() {
                    show_info('', "Email envoyé.");
                }
            });
        });
    });

    $(document).on('change', 'input:radio[name="filtre-status-choice"]', function(event) {
        var tab = $('#table_config_0');
        filterByDossierStatus(tab);
    });

    $(document).on('click', '.config-event-notif', function(event) {
        event.preventDefault();
        var rowId = $(this).closest('tr').attr('id');

        $.ajax({
            url: Routing.generate('dossier_admin_get_config_notif', {id : rowId}),
            type: 'GET',
            success: function(data) {
                var animated = 'bounceInRight',
                    titre = '<span>Configurations Notifications</span>';
                show_modal(data,titre, animated);
                $('.modal-content').css('width', '724px');
            }
        });
    });

    $(document).on('click', '.btn_save_config_notif_mail', function(event) {
        event.preventDefault();
        var notifCheck = [];
        var notif = [];
        $('#list-notif').find('.notif-item').each(function (index, item) {
            var state = $(item).prop('checked');
            if (state === true) {
                notifCheck.push({
                    item: $(item).attr('data-id'),
                });
            }else{
                notif.push({
                    item: $(item).attr('data-id'),
                });
            }
        });
        $.ajax({
            url: Routing.generate('dossier_admin_set_config_notif'),
            type: 'POST',
            data: {
                dossierId: $(this).attr('data-id'),
                notifCheck: notifCheck,
                notif: notif
            },
            success: function (data) {
                show_info("", "Paramètres enregistrés.", "success");
            }
        });
    });

    $(document).on('click', '.js-config-general-modifier', function(event){
        event.preventDefault();
        var rowId = $(this).closest('tr').attr('id'),
            dossier = $('#table_config_0').jqGrid('getCell',rowId,'config-general-dossier'),
            status_code = $('#table_config_0').jqGrid('getCell',rowId,'config-general-statut-code'),
            status_debut = $('#table_config_0').jqGrid('getCell',rowId,'config-general-statut-debut'),
            stop_saisie_date = $('#table_config_0').jqGrid('getCell',rowId,'config-general-stop');

        $('#status-dossier-nom').text('Dossier : ' + dossier);
        $('#status-debut').val(status_debut);
        $('#dossier-id').val(rowId);
        $('#dossier-status-modal').find('input[name="status-value"][value="' + status_code + '"]')
            .click();
        if (stop_saisie_date.length === 10 ) {
            $('#check-stop-saisie').prop('checked', true);
            $('#stop-saisie-date').removeAttr('disabled')
                .val(stop_saisie_date);
        } else {
            $('#check-stop-saisie').prop('checked', false);
            $('#stop-saisie-date').attr('disabled', '')
                .val('');
        }

        $('#dossier-status-modal').modal('show');
    });

    //Activer stop sasie datepicker
    $(document).on('change', '#check-stop-saisie', function() {
       if ($(this).prop('checked') === true) {
           $('#stop-saisie-date').removeAttr('disabled')
               .focus();
       } else {
           $('#stop-saisie-date').val('')
               .attr('disabled', '')
       }
    });

    //Afficher/Cacher debut-status
    $(document).on('change', 'input[name="status-value"]', function(event) {
        event.preventDefault();
        var id = $(this).attr('id');
        if (id === 'status-actif') {
            $('#status-debut-container').addClass('hidden');
        } else {
            $('#status-debut-container').removeClass('hidden');
        }
    });

    $('#status-debut').datepicker({
        format:'yyyy',
        language: 'fr',
        autoclose:true,
        clearBtn: true,
        viewMode: "years",
        minViewMode: "years",
        startView: 'decade',
        minView: 'decade',
        viewSelect: 'decade'
    });

    $('#stop-saisie-date').datepicker({
        format:'dd/mm/yyyy',
        language: 'fr',
        autoclose:true,
        clearBtn: true
    });

    //Enregistrer status dossier
    $(document).on('click', '#btn-save-status', function(event) {
        event.preventDefault();
        var status = $(document).find('input:radio[name="status-value"]:checked').val(),
            status_debut = $('#status-debut').val().trim(),
            dossier_id = $('#dossier-id').val(),
            stop_saisie = $('#check-stop-saisie').prop('checked') ? 1 : 0,
            stop_saisie_date = '';

        if($('#stop-saisie-date').length > 0) {
            stop_saisie_date = $('#stop-saisie-date').val().trim()
        }

        if (status !== '1' && status_debut === '') {
            show_info('', "Séléctionner l'année de début.", 'warning');
        }else if(stop_saisie === 1 && stop_saisie_date === '') {
            show_info('', "Renseigner la date de stop de la saisie", 'warning');
            setTimeout(function() {
                $('#stop-saisie-date').focus();
            }, 1000);
        }else{
            $.ajax({
                url: Routing.generate('info_perdos_activation_dossier_edit', {dossier: dossier_id}),
                type: 'POST',
                data: {
                    status: status,
                    status_debut: status_debut,
                    stop_saisie_date: stop_saisie_date
                },
                success: function() {
                    $('#dossier-status-modal').modal('hide');
                    go();
                }
            })
        }
    });
});

function getSitesConfig(client_selector, site_selector) {
	var client = client_selector.val();
        site_selector.empty();

    $.ajax({
        url: Routing.generate('app_sites', {'conteneur': 1, 'client': client}),
        type: 'GET',
        data: {},
        success: function (data) {
            data = $.parseJSON(data);
            var tous = '<option value="0">Tous</option>';

            var single = false;
            site_selector.closest('.form-group')
                .find('.label.label-warning')
                .text(data.length.toString());

            if (data.length <= 1) {
                site_selector.attr('disabled', 'disabled');
                single = true;
            } else {
                site_selector.removeAttr('disabled');
                site_selector.html(tous);
            }

            var options = '';
            if (data instanceof Array) {
                $.each(data, function (index, item) {
                    if (single) {
                        options += '<option value="' + item.idCrypter + '" selected>' + item.nom + '</option>';
                    } else {
                        options += '<option value="' + item.idCrypter + '">' + item.nom + '</option>';
                    }
                });
                site_selector.append(options);
            } else {
                return 0;
            }
            go();
        }
    });
}

function go() {
	var tab_element = null;
    $('#id_config_tabs').find('.tab-content').find('.tab-pane').each(function(){
        if ($(this).hasClass('active')) tab_element = $(this);
    });
    var type = parseInt(tab_element.attr('data-type'));

    var li_active = $('#id_config_tabs').find('.nav').find('.config_tab_li.active');
    li_active.find('.cl_nb').addClass('hidden').text('');

    vider_table();
    var c_el = $('#client-config');
    var s_el = $('#site-config');
    var table = '<table id="table_config_'+type+'"></table>';
    tab_element.find('.panel-body').html(table);
    var table_selected = $('#table_config_'+type),
        h = $(window).height() - 80,
        last_table_select;
    if(type == 1){
        set_table_config_client(table_selected, h, last_table_select);
        reloadGridClient();
    }else if(type == 2){
        set_table_config_dossier(table_selected, h, last_table_select);
        reloadGridDossier();
    }else if(type == 3){
        set_table_rappel_img(table_selected, h, last_table_select);
        reloadGridRappelImage();
    }else if(type == 4){
        set_table_pm(table_selected, h, last_table_select);
        reloadGridRappelPm();
    }else if(type == 5){
        set_table_autres_pm(table_selected, h, last_table_select);
        reloadGridAutresPm();
    }else{
        $.ajax({
            data: {
                exercice: $('#exercice').val(),
                type: type,
                client: c_el.val(),
                site: s_el.val()
            },
            url: Routing.generate('dossier_admin_config'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                data = $.parseJSON(data);
                set_table(data, tab_element);
            }
        });
    }
}

function set_table(data, tab_element) {
	var type = parseInt(tab_element.attr('data-type')),
        table_selected = $('#table_config_'+type),
        w = table_selected.parent().width(),
        h = $(window).height() - 80,
        editurl = 'index.php',
        last_table_select;
    if(type == 0){
    	table_selected.jqGrid({
	        datatype: 'local',
	        loadonce: true,
	        sortable: false,
	        height: h,
	        autowidth: true,
	        shrinkToFit: true,
	        viewrecords: true,
	        hidegrid: false,
	        rownumbers: true,
	        rownumWidth: 35,
	        colNames: [
	            'Dossiers',
                'Statut',
                'Stop Saisie',
	            'Modifier',
	            'Connecté',
	            'BI',
	            'Envoi mail création',
	            'Notification',
	            'Code statut',
	            'Debut statut'
	        ],
	        caption: '',
	        colModel: [
	            {
	                name: 'config-general-dossier', index: 'config-general-dossier', editable: false, sortable: true, width: 180, classes: 'js-config-general-dossier'
	            },
	            {
	                name: 'config-general-dossier-status', index: 'config-general-dossier-status', editable: false, sortable: true, width: 140, classes: 'js-config-general-dossier-status', align: 'center',
	                cellattr: statusAttr, formatter: statusFormatter
	            },
                {
                    name: 'config-general-stop', index: 'config-general-stop', editable: false, sortable: false, title: false, width: 90, classes: 'js-config-general-stop', align: 'center'
                },
                {
                    name: 'config-general-modifier', index: 'config-general-modifier', editable: false, sortable: false, title: false, width: 90, classes: 'js-config-general-modifier', align: 'center'
                },
                {
                    name: 'config-general-connecte', index: 'config-general-connecte', editable: false, sortable: true, title: false, width: 140, classes: 'js-config-general-connecte', align: 'center'
                },
	            {
	                name: 'config-general-bi', index: 'config-general-bi', editable: false, sortable: true, title: false, width: 30, align: 'center', classes: 'js-config-general-bi'
	            },
	            {
	                name: 'config-general-mail', index: 'config-general-mail', editable: false, sortable: true, title: false, width: 50,align: 'center', classes: 'js-config-general-mail'
	            },
	            {
	                name: 'config-general-notif', index: 'config-general-notif', editable: false, sortable: true, width: 35, align: 'center', classes: 'js-config-general-notif'
	            },
	            {
	                name: 'config-general-statut-code', index: 'config-general-statut-code', hidden: true, classes: 'js-config-general-statut-code'
	            },
	            {
	                name: 'config-general-statut-debut', index: 'config-general-statut-debut', hidden: true, classes: 'js-config-general-statut-debut'
	            }
	        ],
	        ajaxRowOptions: { async: true },
	        onSelectRow: function (id) {
	            if (id && id !== last_table_select) {
	                table_selected.restoreRow(last_table_select);
	                last_table_select = id;
	            }
	            table_selected.editRow(id, true);
	        },
	        loadComplete: function() {
	            var filtre = '<div id="filtre-status" style="text-align: center;;padding-top: 12px;">';
	            filtre += '<div class="radio radio-inline">';
	            filtre += '<input type="radio" id="filtre-status-tous" value="0" name="filtre-status-choice" checked="">';
	            filtre += '<label for="filtre-status-tous">Tous</label>';
	            filtre += '</div>';
	            filtre += '<div class="radio radio-inline">';
	            filtre += '<input type="radio" id="filtre-status-actif" value="1" name="filtre-status-choice">';
	            filtre += '<label for="filtre-status-actif">Actifs</label>';
	            filtre += '</div>';
	            filtre += '<div class="radio radio-inline">';
	            filtre += '<input type="radio" id="filtre-status-suspendu" value="2" name="filtre-status-choice">';
	            filtre += '<label for="filtre-status-suspendu">Suspendus</label>';
	            filtre += '</div>';
	            filtre += '<div class="radio radio-inline">';
	            filtre += '<input type="radio" id="filtre-status-radie" value="3" name="filtre-status-choice">';
	            filtre += '<label for="filtre-status-radie">Radiés</label>';
	            filtre += '</div>';
	            filtre += '<div class="radio radio-inline">';
	            filtre += '<input type="radio" id="filtre-status-en-creation" value="4" name="filtre-status-choice">';
	            filtre += '<label for="filtre-status-en-creation">En création</label>';
                filtre += '</div>';
                filtre += '<div class="checkbox checkbox-inline" style="margin-left:15px;">';
                filtre += '<input type="checkbox" id="filtre-status-stop-saisie">';
                filtre += '<label for="filtre-status-stop-saisie">Stop Saisie</label>';
                filtre += '</div>';
                filtre += '</div>';

	            table_selected.closest('.ui-jqgrid').find('.ui-jqgrid-title').parent().css('display', 'block');
	            if (table_selected.closest('.ui-jqgrid').find('#filtre-status').length === 0) {
	                table_selected.closest('.ui-jqgrid').find('.ui-jqgrid-title').after(filtre);
	            }

	            filterByDossierStatus(table_selected);

	            table_selected.find('.user-list-details').qtip({
	                content: {
	                    text: function() {
	                        return $(this).find('.user-list-content').html();
	                    }
	                },
	                position: { my: 'bottom center', at: 'top center' },
	                style: {
	                    classes: 'qtip-dark qtip-shadow',
	                    tip: {
	                        corner: true
	                    }
	                }
	            });

	            table_selected.find('.sb-list-details').qtip({
	                content: {
	                    text: function() {
	                        return $(this).find('.sb-list-content').html();
	                    }
	                },
	                position: { my: 'bottom center', at: 'top center' },
	                style: {
	                    classes: 'qtip-dark qtip-shadow',
	                    tip: {
	                        corner: true
	                    }
	                }
	            });
	        }
	    });

		setTimeout(function() {
			remplirGrid(data, table_selected);
	    }, 100);
    }
}

function remplirGridRappelPm(data, table_selected) {
	table_selected.jqGrid("clearGridData");
    table_selected.jqGrid("setGridParam", {
        datatype: 'json',
        loadonce: true,
        data: data
    }).trigger("reloadGrid", [{ page: 1, loadonce: true}]);
}

function statusAttr(rowId, val, rawObject, cm, rdata) {
    var background = '#23c6c8',
        color = '#fff',
        status = rawObject['config-general-statut-code'];

    if (status == 2 || status == 4) {
        background = '#f8ac59';
    } else if (status == 3) {
        background = '#ed5565';
    }
    return 'style="background:' + background + ';color:' + color + ';" data-status="' + status + '"';
}

function filterByDossierStatus(table_selected) {
    var value = $(document).find('input:radio[name="filtre-status-choice"]:checked').val();
    var stopSaisie = $(document).find('#filtre-status-stop-saisie').prop('checked');
    if (stopSaisie) {
        if (value !== '0') {
            table_selected.jqGrid("setGridParam", {
                postData: {
                    filters: JSON.stringify({
                        groupOp: "AND",
                        rules: [
                            {field: "config-general-statut-code", op: "eq", data: value},
                            {field: "config-general-stop", op: "ne", data: null},
                            {field: "config-general-stop", op: "ne", data: ''}
                        ]
                    })
                },
                search: true
            }).trigger("reloadGrid", {page: 1});
        } else {
            table_selected.jqGrid("setGridParam", {
                postData: {
                    filters: JSON.stringify({
                        groupOp: "AND",
                        rules: [
                            {field: "config-general-stop", op: "ne", data: null},
                            {field: "config-general-stop", op: "ne", data: ''}
                        ]
                    })
                },
                search: true
            }).trigger("reloadGrid", {page: 1});
        }
    } else {
        if (value !== '0') {
            table_selected.jqGrid("setGridParam", {
                postData: {
                    filters: JSON.stringify({
                        groupOp: "AND",
                        rules: [
                            {field: "config-general-statut-code", op: "eq", data: value}
                        ]
                    })
                },
                search: true
            }).trigger("reloadGrid", {page: 1});
        } else {
            table_selected.jqGrid("setGridParam", {
                search: false
            }).trigger("reloadGrid", {page: 1});
        }
    }
}

function statusFormatter(cellvalue, options, rowObject) {
    var texte = 'Actif';
    if (cellvalue == '2') {
        texte = 'Suspendu à partir de ' + rowObject['config-general-statut-debut'];
    } else if (cellvalue == '3') {
        texte = 'Radié à partir de ' + rowObject['config-general-statut-debut']
    } else if (cellvalue == '4') {
        texte = 'En création';
    }
    return texte;
}

function remplirGrid(data, table_selected) {
	data = $.parseJSON(data);
    var data_suspendre = [];
    if (data instanceof Array) {
        $.each(data, function (index, item) {
            var stop_saisie_date = null;
            if (item.dateStopSaisie !== null && item.dateStopSaisie !== '') {
                stop_saisie_date = item.dateStopSaisie;
            }
            var status_code = item.status;
            var status = '';
            if (item.active === 0) {
                status_code = 4;
                status = 4;
            } else {
                status = 1;
                var debut = item.statusDebut !== null ? item.statusDebut : '';
                var site = typeof item.site !== 'undefined' ? item.site : '';
                if (item.status === 2) {
                    status = 2;
                } else if (item.status === 3) {
                    status = 3;
                }
            }
            var users = '';
            var renvoi_mail = '';
            var user_id = '';
            if (item.users instanceof Array && item.users.length > 0) {
                var list = '<div style="display: none" class="user-list-content">';
                var user_actif = false;
                var user_logedd_in = true;
                item.users.forEach(function(currentValue, currentIndex) {
                    if (currentValue.actif === 1) {
                        user_actif = true;
                    }
                    var status = 'Actif';
                    if (currentValue.last_login === "") {
                        user_logedd_in = false;
                        status = 'Créé';
                    }
                    if (currentValue.actif !== 1) {
                        status = 'Bloqué';
                    }

                    user_id = (user_id == '') ? currentValue.user_id : (user_id + ', ' + currentValue.user_id);

                    list += '<strong>Utilisateur:</strong> ' + currentValue.user + '<br>';
                    list += '<strong>Email:</strong> ' + currentValue.email + '<br>';
                    list += '<strong>Dernière connexion:</strong> ' + currentValue.last_login + '<br>';
                    list += '<strong>Statut:</strong> ' + status;
                    if (currentIndex < item.users.length - 1) {
                        list += '<hr>';
                    }
                });
                list += '</div>';
                var color = '#23c6c8';
                if (!user_logedd_in) {
                    color = '#f8ac59';
                }
                if (!user_actif) {
                    color = '#ed5565';
                }
                renvoi_mail = '<span class="event-resend-mail-creation pointer" data-id="'+user_id+'">' +
                    '<i class="fa fa-retweet fa-lg" style="color:' + color + ';"></i>' +
                    list +
                    '</span>';
                users = '<span class="user-list-details">' +
                    '<i class="fa fa-user fa-lg" style="color:' + color + ';"></i>' +
                    list +
                    '</span>';
            }


            var sbs = '';
            if (item.sbs instanceof Array && item.sbs.length > 0) {
                var listSb = '<div style="display: none" class="sb-list-content">';

                item.sbs.forEach(function(currentValue, currentIndex) {
                    listSb += '<strong>Banque:</strong> ' + currentValue.banque + '<br>';
                    listSb += '<strong>Num Compte:</strong> ' + currentValue.numcompte + '<br>';

                    if (currentIndex < item.sbs.length - 1) {
                        listSb += '<hr>';
                    }
                });
                listSb += '</div>';

                sbs = '<span class="sb-list-details">' +
                    'BI' +
                    listSb +
                    '</span>';
            }

            var notif = '<span class="config-event-notif pointer">' +
                    '<i class="fa fa-info fa-lg"></i>' +
                    '</span>';

            data_suspendre.push({
                'id': item.id,
                'config-general-dossier': item.nom,
                'config-general-dossier-status': status,
                'config-general-modifier': '<i class="fa fa-edit fa-lg pointer"></i>',
                'config-general-connecte': users,
                'config-general-bi' : sbs,
                'config-general-mail': renvoi_mail,
                'config-general-notif': notif,
                'config-general-statut-code': status_code,
                'config-general-statut-debut': debut,
                'config-general-stop' : stop_saisie_date
            });
        });
    }

    table_selected.jqGrid("clearGridData");
    table_selected.jqGrid("setGridParam", {
        datatype: 'local',
        loadonce: true,
        data: data_suspendre
    }).trigger("reloadGrid", [{ page: 1, loadonce: true}]);
}

function vider_table()
{
    var tab_element = null;
    $('#id_config_tabs').find('.tab-content').find('.tab-pane').each(function(){
        if ($(this).hasClass('active')) tab_element = $(this);
    });
}
