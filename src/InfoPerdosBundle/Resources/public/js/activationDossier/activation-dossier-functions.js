/**
 * Created by TEFY on 16/05/2017.
 */
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
    console.log(data);
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
                // status = '<span class="label label-warning" data-status="4" style="display: inline-block;width: 100%;height:100%;">En création</span>';
                status = 4;
            } else {
                // status = '<span class="label label-info" data-status="1" style="display: inline-block;width: 100%;height:100%;">Actif</span>';
                status = 1;
                var debut = item.statusDebut !== null ? item.statusDebut : '';
                var site = typeof item.site !== 'undefined' ? item.site : '';
                if (item.status === 2) {
                    // status = '<span class="label label-default" data-status="2" style="display: inline-block;width: 100%;height:100%;">Suspendu à partir de ' + debut + '</span>';
                    status = 2;
                } else if (item.status === 3) {
                    // status = '<span class="label label-danger" data-status="3" style="display: inline-block;width: 100%;height:100%;">Radié à partir de ' + debut + '</span>';
                    status = 3;
                }
            }
            var users = '';
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

            data_suspendre.push({
                'id': item.id,
                'sus-site': site,
                'sus-dossier': item.nom,
                'sus-sb' : sbs,
                'sus-user': users,
                'sus-stop': stop_saisie_date,
                'sus-statut-code': status_code,
                'sus-statut': status,
                'sus-statut-debut': debut,
                'sus-action': '<i class="fa fa-edit fa-lg pointer"></i>'
            });
        });
    }

    var grid_suspendre_dossier = $('#js_suspendre_dossier');
    grid_suspendre_dossier.jqGrid("clearGridData");
    grid_suspendre_dossier.jqGrid("setGridParam", {
        datatype: 'local',
        loadonce: true,
        data: data_suspendre
    }).trigger("reloadGrid", [{ page: 1, loadonce: true}]);
    filterByDossierStatus();
}

function filterByDossierStatus() {
    var grid_suspendre_dossier = $('#js_suspendre_dossier');
    var value = $(document).find('input:radio[name="filtre-status-choice"]:checked').val();
    var stopSaisie = $(document).find('#filtre-status-stop-saisie').prop('checked');
    if (stopSaisie) {
        if (value !== '0') {
            grid_suspendre_dossier.jqGrid("setGridParam", {
                postData: {
                    filters: JSON.stringify({
                        groupOp: "AND",
                        rules: [
                            {field: "sus-statut-code", op: "eq", data: value},
                            {field: "sus-stop", op: "ne", data: null},
                            {field: "sus-stop", op: "ne", data: ''}
                        ]
                    })
                },
                search: true
            }).trigger("reloadGrid", {page: 1});
        } else {
            grid_suspendre_dossier.jqGrid("setGridParam", {
                postData: {
                    filters: JSON.stringify({
                        groupOp: "AND",
                        rules: [
                            {field: "sus-stop", op: "ne", data: null},
                            {field: "sus-stop", op: "ne", data: ''}
                        ]
                    })
                },
                search: true
            }).trigger("reloadGrid", {page: 1});
        }
    } else {
        if (value !== '0') {
            grid_suspendre_dossier.jqGrid("setGridParam", {
                postData: {
                    filters: JSON.stringify({
                        groupOp: "AND",
                        rules: [
                            {field: "sus-statut-code", op: "eq", data: value}
                        ]
                    })
                },
                search: true
            }).trigger("reloadGrid", {page: 1});
        } else {
            grid_suspendre_dossier.jqGrid("setGridParam", {
                search: false
            }).trigger("reloadGrid", {page: 1});
        }
    }
}

function clearGrid(selector) {
    var trf = selector.find("tbody:first tr:first")[0];
    selector.find("tbody:first").empty().append(trf);
}

function updateGridSize() {
    var window_height = window.innerHeight;
    var grid_suspendre_dossier = $('#js_suspendre_dossier');

    setTimeout(function() {
        grid_suspendre_dossier.jqGrid("setGridWidth", grid_suspendre_dossier.closest("div.row").width() - 15);
        grid_suspendre_dossier.jqGrid("setGridHeight", window_height - 250);
    }, 600);
}

function statusAttr(rowId, val, rawObject, cm, rdata) {
    var background = '#23c6c8',
        color = '#fff',
        status = rawObject['sus-statut-code'];

    if (status == 2 || status == 4) {
        background = '#f8ac59';
    } else if (status == 3) {
        background = '#ed5565';
    }
    return 'style="background:' + background + ';color:' + color + ';" data-status="' + status + '"';
}

function statusFormatter(cellvalue, options, rowObject) {
    var texte = 'Actif';
    if (cellvalue == '2') {
        texte = 'Suspendu à partir de ' + rowObject['sus-statut-debut'];
    } else if (cellvalue == '3') {
        texte = 'Radié à partir de ' + rowObject['sus-statut-debut']
    } else if (cellvalue == '4') {
        texte = 'En création';
    }
    return texte;
}