/**
 * Created by TEFY on 20/03/2017.
 */
$(function () {
    var user_search = document.getElementById('user-search');
    var window_height = window.innerHeight;
    var tab_container = $('#tab-container');
    var acces_role = $('#acces-role');
    var acces_user = $('#acces-user');
    var role_list = $('#role-list');
    var menu_list_role = $('#menu-list-role');
    var menu_list_user = $('#menu-list-user');
    var user_list = $('#user-list');
    var client_user = $('#client-user');
    var selected_user = null;
    var item_selected_user = null;

    tab_container.height(window_height - 150);
    role_list.height(tab_container.height() - 100);
    user_list.height(tab_container.height() - 170);
    menu_list_role.height(role_list.height());
    menu_list_user.height(user_list.height() + 35);

    getClientUsers(user_list, client_user);

    //Charger liste menu complet
    $.ajax({
        url: Routing.generate('menu_liste_complet'),
        type: 'GET',
        success: function(data) {
            menu_list_role.html(data);
            menu_list_user.html(data);
            setTimeout(function() {
                /* Activer Nestable List */
                menu_list_role.nestable({
                    group: 1
                }).nestable('collapseAll');
                menu_list_user.nestable({
                    group: 1
                }).nestable('collapseAll');
            }, 1000);
        }
    });


    /* Séléction rôle */
    $(document).on('click', '#role-list .list-group-item', function (event) {
        event.preventDefault();
        $(this)
            .closest('.list-group')
            .find('.list-group-item')
            .removeClass('active');
        $(this).addClass('active');
        menu_list_role.find('.menu-select').prop('checked', false);
        menu_list_role.find('.type-access').bootstrapToggle('off');
        menu_list_role.removeClass('hidden');

        var role = $(this).attr('data-id');

        $.ajax({
            url: Routing.generate('user_menu_par_role', {role: role}),
            type: 'GET',
            data: {},
            success: function (data) {
                data = $.parseJSON(data);
                setMenuSettings(data, menu_list_role);
            }
        });
    });

    /* Ouvrir tout / Réduire tout - liste menus - role */
    $(document).on('click', '.btn-collapse-list-menu', function (event) {
        event.preventDefault();
        var target = $(this).attr('data-target');
        var action = $(this).attr('data-action');
        if (action === 'expand-all') {
            $(target).nestable('expandAll');
        } else {
            $(target).nestable('collapseAll');
        }
    });

    /* Cocher / Décocher un menu - Changer les childs - role */
    $(document).on('change', '.menu-select', function () {
        var checkbox = $(this);
        var state = checkbox.prop('checked');
        var level = checkbox.attr('data-level');

        /* MAJ descendant */
        checkbox.closest('.dd-item')
            .find('.menu-select')
            .prop('checked', state);

        /* MAJ ascendant  */
        if (state === true) {
            if (level === '1') {
                //Pas de parent
            } else if (level === '2') {
                //On cocher parent N+1
                checkbox.closest('.dd-list')
                    .closest('.dd-item')
                    .find('.menu-select[data-level="1"]')
                    .prop('checked', state);
            } else if (level === '3') {
                //On cocher prent N+1 et N+2
                checkbox.closest('.dd-list')
                    .closest('.dd-item')
                    .find('.menu-select[data-level="2"]')
                    .prop('checked', state);
                checkbox.closest('.dd-list')
                    .closest('.dd-item')
                    .find('.menu-select[data-level="2"]')
                    .closest('.dd-list')
                    .closest('.dd-item')
                    .find('.menu-select[data-level="1"]')
                    .prop('checked', state);
            }
        }

    });

    /* Enregistrer Menus par Rôle */
    $(document).on('click', '#btn-save-menu-role', function (event) {
        event.preventDefault();
        if (role_list.find('.list-group-item.active').length > 0) {
            var role = role_list.find('.list-group-item.active')
                .attr('data-id');
            var menus = [];
            menu_list_role.find('.menu-select').each(function (index, item) {
                var state = $(item).prop('checked');
                if (state === true) {
                    var check_parent = $(item).closest('.dd-handle');
                    var type_access = check_parent.find('.type-access');
                    menus.push({
                        menu: $(item).attr('data-menu-id'),
                        can_edit: type_access.prop('checked') === true ? 1 : 0
                    });
                }
            });
            // console.log(menus);
            $.ajax({
                url: Routing.generate('user_menu_par_role_edit', {role: role}),
                type: 'POST',
                data: {
                    menus: menus
                },
                success: function (data) {
                    data = $.parseJSON(data);
                    if (data.erreur === false) {
                        show_info("", "Paramètres enregistrés.", "success");
                        menu_list_role.find('.menu-select').prop('checked', false);
                        menu_list_role.find('.type-access').bootstrapToggle('off');
                        setMenuSettings(data.menus, menu_list_role);
                    } else {
                        show_info("", data.erreur_text, "error");
                    }
                }
            });
        } else {
            show_info("", "Séléctionner un rôle.", "warning");
        }
    });

    /* Liste utilisateur d'un client */
    $(document).on('change', '#client-user', function (event) {
        event.preventDefault();
        getClientUsers(user_list, client_user);
    });

    /* Séléctionner un utilisateur */
    $(document).on('click', '#user-list .list-group-item', function (event) {
        event.preventDefault();

        $(this)
            .closest('.list-group')
            .find('.list-group-item')
            .removeClass('active');
        $(this).addClass('active');
        menu_list_user.find('.menu-select').prop('checked', false);
        menu_list_user.find('.type-access').bootstrapToggle('off');
        menu_list_user.removeClass('hidden');

        var user_id = $(this).attr('data-id');
        item_selected_user = $(this);
        selected_user = user_id;

        $.ajax({
            url: Routing.generate('user_menu_par_user', {user: user_id}),
            type: 'GET',
            data: {},
            success: function (data) {
                data = $.parseJSON(data);
                console.log(data);
                setMenuSettings(data, menu_list_user);
            }
        });
    });

    /* Enregistrer Menus par Utilisateur */
    $(document).on('click', '#btn-save-menu-user', function (event) {
        event.preventDefault();
        saveUserMenus(0);
    });

    /* Utiliser paramètre dans rôle */
    $(document).on('click', '#btn-override-menu-user', function (event) {
        event.preventDefault();
        saveUserMenus(1);
    });

    /** Chercher un utilisateur */
    user_search.addEventListener('keyup', makeDebounce(function(e) {
        var search_text = accent_fold(e.target.value).toLowerCase();
        $('#user-list').find('.list-group-item').each(function(index, item) {
            var item_text = accent_fold($(item).text()).toLowerCase();
            if (item_text.indexOf(search_text) >= 0) {
                $(item).removeClass('hidden');
            } else {
                $(item).addClass('hidden');
            }
        });
    }, 500));

    function saveUserMenus(use_default) {
        if (typeof use_default === 'undefined') {
            use_default = 0;
        }
        if (user_list.find('.list-group-item.active').length > 0) {
            var user = user_list.find('.list-group-item.active')
                .attr('data-id');
            var menus = [];
            menu_list_user.find('.menu-select').each(function (index, item) {
                var state = $(item).prop('checked');
                if (state === true) {
                    var check_parent = $(item).closest('.dd-handle');
                    var type_access = check_parent.find('.type-access');
                    menus.push({
                        menu: $(item).attr('data-menu-id'),
                        can_edit: type_access.prop('checked') === true ? 1 : 0
                    });
                }
            });

            $.ajax({
                url: Routing.generate('user_menu_par_user_edit', {user: user, default: use_default}),
                type: 'POST',
                data: {
                    menus: menus
                },
                success: function (data) {
                    data = $.parseJSON(data);
                    if (data.erreur === false) {
                        show_info("", "Paramètres enregistrés.", "success");
                        menu_list_user.find('.menu-select').prop('checked', false);
                        menu_list_user.find('.type-access').bootstrapToggle('off');
                        setMenuSettings(data.menus, menu_list_user);
                    } else {
                        show_info("", data.erreur_text, "error");
                    }
                }
            });
        } else {
            show_info("", "Séléctionner un rôle.", "warning");
        }
    }
});
