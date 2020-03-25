$(function() {
    var changeCount = 0;
    HTMLTextAreaElement.prototype.insertAtCaret = function (text) {
        text = text || '';
        if (document.selection) {
            // IE
            this.focus();
            var sel = document.selection.createRange();
            sel.text = text;
        } else if (this.selectionStart || this.selectionStart === 0) {
            // Others
            var startPos = this.selectionStart;
            var endPos = this.selectionEnd;
            this.value = this.value.substring(0, startPos) +
                text +
                this.value.substring(endPos, this.value.length);
            this.selectionStart = startPos + text.length;
            this.selectionEnd = startPos + text.length;
        } else {
            this.value += text;
        }
    };
    var window_height = window.innerHeight,

        config_general_container = $('#config-general'),
        config_general_site = $('#site-config-general'),
        config_general_client = $('#client-config-general'),
        tableau_config_general = $('#js_config_general'),
        lastsel_config_general;
    var now = new Date();
    config_general_container.height(window_height - 100);
    var config_general_grid_width = $('#config_general_width').width(),
        config_general_grid_height = config_general_container.height() - 60;

    tableau_config_general.jqGrid({
        datatype: 'local',
        loadonce: true,
        sortable: false,
        height: config_general_grid_height,
        autowidth: true,
        shrinkToFit: true,
        viewrecords: true,
        hidegrid: false,
        rownumbers: true,
        rownumWidth: 35,
        pager: '#pager_tableau',
        colNames: [
            'Dossiers',
            'Statut',
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
                name: 'config-general-connecte', index: 'config-general-connecte', editable: false, sortable: true, title: false, width: 140, classes: 'js-config-general-dconnecte', align: 'center'
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
            if (id && id !== lastsel_config_general) {
                tableau_config_general.restoreRow(lastsel_config_general);
                lastsel_config_general = id;
            }
            tableau_config_general.editRow(id, true);
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
            filtre += '</div>';

            tableau_config_general.closest('.ui-jqgrid').find('.ui-jqgrid-title').parent().css('display', 'block');
            if (tableau_config_general.closest('.ui-jqgrid').find('#filtre-status').length === 0) {
                tableau_config_general.closest('.ui-jqgrid').find('.ui-jqgrid-title').after(filtre);
            }

            filterByDossierStatus();

             tableau_config_general.find('.user-list-details').qtip({
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

            tableau_config_general.find('.sb-list-details').qtip({
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

    $(document).on('change', 'input[name="filtre-status-choice"]', function(event) {
       event.preventDefault();
       filterByDossierStatus();
    });

    setTimeout(function() {
        updateGridSizeConfigGeneral();
    }, 500);

    $('#tab-param-rappel').on('click', function() {
        updateGridSizeConfigGeneral();
    });

    function filterByDossierStatus() {
        var value = $(document).find('input:radio[name="filtre-status-choice"]:checked').val();
        if (value !== '0') {
            tableau_config_general.jqGrid("setGridParam", {
                postData: {
                    filters: JSON.stringify({
                        groupOp: "AND",
                        rules: [
                            {field: "config-general-statut-code", op: "eq", data: value}
                        ]
                    })
                },
                search: true
            }).trigger("reloadGrid", [{ page: 1, loadonce: true}]);
        } else {
            tableau_config_general.jqGrid("setGridParam", {
                search: false
            }).trigger("reloadGrid", [{ page: 1, loadonce: true}]);
        }
    }

    function updateGridSizeConfigGeneral() {
        setTimeout(function() {
            window_height = window.innerHeight;
            config_general_container.height(window_height - 100);
            config_general_grid_height = config_general_container.height() - 60;
            tableau_config_general.jqGrid("setGridWidth", $("#config-general").width() - 50);
            tableau_config_general.jqGrid("setGridHeight", config_general_grid_height);
        }, 0);
    }

    $(window).bind('resize',function () {
        updateGridSizeConfigGeneral();
    });

    function getSites(client_selector, site_selector, grid_selector) {
        var client = client_selector.val();
        site_selector.empty();
        if (grid_selector instanceof Array) {
            $.each(grid_selector, function(index, item) {
                clearGrid(item);
            });
        } else {
            clearGrid(grid_selector);
        }

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

                getDossiers(client_selector, site_selector, grid_selector);
            }
        });
    }

    function getDossiers(client_selector, site_selector, grid_selector) {
        var client = client_selector.val();
        var site = site_selector.val();
        if (grid_selector instanceof Array) {
            $.each(grid_selector, function(index, item) {
                clearGrid(item);
            });
        } else {
            clearGrid(grid_selector);
        }

        var url = Routing.generate('app_dossiers_tmp', {client: client, site: site, conteneur: 1});
        $.ajax({
            url: url,
            type: 'GET',
            data: {},
            success: function (data) {
                data = $.parseJSON(data);
                remplirGrid(data);
            }
        })
    }

    function remplirGrid(data) {
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
                        'SB' +
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
                    'config-general-bi' : sbs,
                    'config-general-connecte': users,
                    'config-general-mail': renvoi_mail,
                    'config-general-notif': notif,
                    'config-general-statut-code': status_code,
                    'config-general-statut-debut': debut
                });
            });
        }

        var grid_config_general = $('#js_config_general');
        grid_config_general.jqGrid("clearGridData");
        grid_config_general.jqGrid("setGridParam", {
            datatype: 'local',
            loadonce: true,
            data: data_suspendre
        }).trigger("reloadGrid", [{ page: 1, loadonce: true}]);
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
            }
        });
    });

    $(document).on('click', '.btn_save_config_notif_mail', function(eveent) {
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
    })
});