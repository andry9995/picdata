/**
 * Created by TEFY on 09/03/2017.
 */
function getDossiers(client_selector, dossier_selector, callback) {
    if (client_selector.val() !== '') {
        $.ajax({
            url: Routing.generate('app_dossiers_client', {
                'client': client_selector.val(),
                'json': 1,
                'tous': 1,
                'crypter': 0
            }),
            type: 'GET',
            success: function (data) {
                data = $.parseJSON(data);
                var options = '';
                $.each(data, function (index, item) {
                    options += '<option value="' + item.id + '">' + item.nom + '</option>';
                });
                dossier_selector.html(options);
                dossier_selector.trigger("chosen:updated");

                /* Executer fonction après AJAX */
                if (typeof callback === 'function') {
                    callback();
                }
            }
        });
    }
}

function getSites(client_selector, site_selector, callback) {
    if (client_selector.val() !== '') {
        $.ajax({
            url: Routing.generate('app_all_sites_client', {'client': client_selector.val(), 'crypter': 0}),
            type: 'GET',
            success: function (data) {
                data = $.parseJSON(data);
                var options = '';
                $.each(data, function (index, item) {
                    options += '<option value="' + item.id + '">' + item.nom + '</option>';
                });
                site_selector.html(options);
                site_selector.trigger("chosen:updated");

                /* Executer fonction après AJAX */
                if (typeof callback === 'function') {
                    callback();
                }
            }
        });
    }
}

function getClientUsers(container, client_user) {
    container
        .find('.list-group')
        .empty();
    if (client_user.val() !== "") {
        $.ajax({
            url: Routing.generate('client_users_list', {'client': client_user.val()}),
            type: 'GET',
            success: function (data) {
                data = $.parseJSON(data);

                var items = "";
                $.each(data, function (index, item) {
                    var user_status = '<i class="fa fa-check-circle-o text-primary" title="Cet utilisateur est actif"></i>';
                    var role_libelle = item[0].societe == null ? item[0].accesUtilisateur.libelle : item[0].societe.toUpperCase() + ' ('+ item[0].accesUtilisateur.libelle + ')';
                    if (item[0].supprimer == 1) {
                        user_status = '<i class="fa fa-times-circle text-danger" title="Cet utilisateur est desactivé"></i>';
                    }
                    items += '<li data-type="'+ item[0].type +'" data-status="'+ item[0].supprimer +'" data-id="' + item[0].idCrypter + '" class="list-group-item">';
                    items += '<span class="pull-right user-role">' + role_libelle + '</span>';
                    items += user_status + ' ' + item.nomComplet;
                    items += '</li>';
                });
                container
                    .find('.list-group')
                    .html(items);
            }
        });
    }
}

function setMenuSettings(data, parent) {
    if (typeof data === 'undefined') {
        return;
    }
    $.each(data, function(index, item) {
        if (typeof item.menu !== 'undefined' && item.menu !== null) {
            var search = parent.find('.menu-select[data-menu-id="' + item.menu.id + '"]');
            if (search.length > 0) {
                search.prop('checked', true);
                var check_parent = search.closest('.dd-handle');
                var type_access = check_parent.find('.type-access');
                if (item.canEdit === true) {
                    type_access.bootstrapToggle('on');
                } else {
                    type_access.bootstrapToggle('off');
                }
            }
        }
    });
}