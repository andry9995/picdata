/**
 * Created by TEFY on 03/03/2017.
 */
$(function () {

    /* Activer chosen */
    $('.chosen-select').chosen({
        width: '100%',
        search_contains: true
    });

    /* Activer js-switch */
    // var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    // elems.forEach(function (html) {
    //
    // });

    var switchery_actif = new Switchery(document.getElementById('user-actif'), {
        size: 'small',
        color: '#18a689'
    });

    var switchery_demo = new Switchery(document.getElementById('user-show-demo'), {
        size: 'small',
        color: '#18a689'
    });

    var user_search = document.getElementById('user-search');

    /* liste des clients pour afficher les utilisateurs correspondants */
    var client_user = $('#client-user');

    /* Utilisateur séléctionné */
    var selected_user;
    var item_selected_user;

    var window_height = window.innerHeight;
    var user_list = $('#user-list');
    var user_container = $('#user-container');
    var user_actif = $('#user-actif');
    var user_show_demo = $('#user-show-demo');

    var user_role = $('#user-role');
    var selected_role = user_role.val();
    var selected_role_text = user_role.find('option:selected').text();
    var role_group = user_role.find('option:selected').attr('data-type');
    var client = $('#client');
    var client_multi = $('#client-multi');
    var all_client_group = $('#client-group');
    var client_group = client.closest('.form-group');
    var client_multi_group = client_multi.closest('.form-group');

    var site_group = $('#site-group');
    var dossier_group = $('#dossier-group');
    var site = $('#site');
    var dossier = $('#dossier');

    var register_form = $('#user-register-form');
    var register_form2 = $('#user-register-form2');
    var user_form = $('#user-form');

    user_container.height(window_height - 180);
    user_list.height(user_container.height() - 120);
    user_form.height(user_container.height() - 80);

    /* Afficher utilisateur du premier client dans la liste */
    user_form.addClass('hidden');
    getClientUsers(user_list, client_user);

    /* Séléctionner un utilisateur */
    $(document).on('click', '#user-list .list-group-item', function (event) {
        event.preventDefault();
        $(document).find('.form-group').removeClass('has-error');
        $('#register_update_nom').val('');
        $('#register_update_prenom').val('');
        $('#register_update_email').val('');
        $('#register_update_societe').val('');
        $('#register_update_telephone').val('');
        $('#register_update_skype').val('');

        register_form.find('.form-group')
            .removeClass('has-error')
            .find('.help-block')
            .remove();
        register_form2.find('.form-group')
            .removeClass('has-error')
            .find('.help-block')
            .remove();

        $(this)
            .closest('.list-group')
            .find('.list-group-item')
            .removeClass('active');
        $(this).addClass('active');
        var user_id = $(this).attr('data-id');
        item_selected_user = $(this);
        $.ajax({
            url: Routing.generate('user_roles_and_acces', {'user': user_id}),
            type: 'GET',
            success: function (response) {
                response = $.parseJSON(response);
                var data = response.utilisateur;
                data.currentUserType = response.currentUserType;
                setUserRoles(true, data);
                selected_user = user_id;
            }
        });
    });

    /* Séléction client */
    $(document).on('change', '#client', function (event) {
        event.preventDefault();
        role_group = user_role.find('option:selected').attr('data-type');
        if (role_group === '4') {
            getSites(client, site);
        }
        if (role_group === '5' || role_group === '6') {
            getDossiers(client, dossier);
        }
    });

    /* Liste utilisateur d'un client */
    $(document).on('change', '#client-user', function (event) {
        event.preventDefault();
        $('#user-form').addClass('hidden');
        clear_filter();
        getClientUsers(user_list, client_user);
    });

    function clear_filter() {
        $('#filter-status').find('option[value=""]').attr('selected','selected')
        $('#filter-status').val('').change()
        $('#filter-type').find('option[value=""]').attr('selected','selected')
        $('#filter-type').val('').change()
        $('#user-search').val('');            

    }

    /* Selection rôle utilisateur */
    $(document).on('change', '#user-role', function () {
        setUserRoles(false);
    });

    /* Enregistrer modif infos et role utilisteur */
    $(document).on('click', '#btn-save-role-user', function (event) {
        event.preventDefault();
        if (typeof selected_user === 'undefined') {
            show_info("", "Séléctionner un utilisateur.", "warning");
            return;
        }

        if ($('#user-type').val() === '') {
            show_info('','Choisir le type d\'utilisateur',"error");
            return;
        }

        var form_data = new FormData(register_form[0]);
        form_data.append('user_actif', $('#user-actif').prop('checked') === true ? 1 : 0);
        form_data.append('user_show_demo', $('#user-show-demo').prop('checked') === true ? 1 : 0);
        form_data.append('user_role', $('#user-role').val());
        form_data.append('client', client.val());
        form_data.append('client_multi', client_multi.chosen().val());
        form_data.append('sites', site.chosen().val());
        form_data.append('dossiers', dossier.chosen().val());
        form_data.append('user_type', $('#user-type').val());

        $.ajax({
            url: Routing.generate('user_roles_and_acces_edit', {'user': selected_user}),
            type: 'POST',
            data: form_data,
            processData: false,
            contentType: false,
            success: function (data) {
                data = $.parseJSON(data);
                register_form.find('.form-group')
                    .removeClass('has-error')
                    .find('.help-block')
                    .remove();
                register_form2.find('.form-group')
                    .removeClass('has-error')
                    .find('.help-block')
                    .remove();
                if (data.is_form_valid === false) {
                    for (var key in data.error_data) {
                        $(register_form.find('[name*="' + key + '"]')[0]).after('<span class="help-block"><ul class="list-unstyled"><li>' + data.error_data[key] + '</li></ul></span>');
                        $(register_form.find('[name*="' + key + '"]')[0]).closest('.form-group').addClass('has-error');

                        $(register_form2.find('[name*="' + key + '"]')[0]).after('<span class="help-block"><ul class="list-unstyled"><li>' + data.error_data[key] + '</li></ul></span>');
                        $(register_form2.find('[name*="' + key + '"]')[0]).closest('.form-group').addClass('has-error');
                    }
                    show_info("", "Erreur lors de l'enregistrement", "error");
                } else {
                    var utilisateur = data.utilisateur;
                    if (typeof utilisateur !== 'undefined' && utilisateur != null) {
                        utilisateur.currentUserType = data.currentUserType;
                        setUserRoles(true, utilisateur);
                        var role_libelle = utilisateur.societe == null ? utilisateur.accesUtilisateur.libelle : utilisateur.societe.toUpperCase() + ' ('+ utilisateur.accesUtilisateur.libelle + ')';
                        var user_status = '<i class="fa fa-check-circle-o text-primary" title="Cet utilisateur est actif"></i>';
                        if (utilisateur.supprimer == 1) {
                            user_status = '<i class="fa fa-times-circle text-danger" title="Cet utilisateur est desactivé"></i>';
                        }
                        var user_html = '<span class="pull-right user-role">' + role_libelle + '</span>';
                        user_html += user_status + ' ' + utilisateur.nomComplet;

                        item_selected_user
                            .attr('data-id', utilisateur.idCrypter)
                            .html(user_html);

                    }
                    $('.form-group').removeClass('has-error');
                    show_info("", "Mise à jour enregistrée.", "success");
                }
            }
        })
    });

    /** Renvoi mail création compte */
    $(document).on('click', '#btn-resend-mail-creation', function(event) {
       event.preventDefault();
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
                url: Routing.generate('user_resend_mail_creation', {user: selected_user}),
                type: 'POST',
                success: function() {
                    show_info('', "Email envoyé.");
                }
            });
        });
    });

    // Filtre type
    $(document).on('change', '#filter-type', function (event) {
        var type = $(this).val();

        var list = $('#user-list');

        var status = $('#filter-status').val();

        if (type === '') {
            if ($('#filter-status').val() === '') {
                list.find('.list-group-item').each(function(index, item) {
                    $(item).removeClass('hidden');
                    $(item).removeClass('f-type');
                    // $(item).removeClass('f-status');
                })
            } else {
                list.find('.list-group-item').each(function(index, item) {
                   if ($(item).hasClass('f-status') == true) {
                        $(item).removeClass('hidden');
                        $(item).removeClass('f-type');
                    } else {
                        var user_status = $(item).data('status');
                        if (status == user_status) {
                            $(item).removeClass('hidden');
                            $(item).addClass('f-status');
                        }
                    }
                })
            }

        } else{
            list.find('.list-group-item').each(function(index, item) {
                var user_type = $(item).data('type');

                if ($('#filter-status').val() === '') {
                    if (user_type == type) {
                        $(item).removeClass('hidden');
                        $(item).addClass('f-type');
                        $(item).removeClass('f-status');
                    } else{
                        $(item).addClass('hidden');
                        $(item).removeClass('f-type');
                        $(item).removeClass('f-status');
                    }
                } else {
                    if ($(item).hasClass('f-status') == true) {
                        if (user_type == type) {
                            $(item).removeClass('hidden');
                            $(item).addClass('f-type');
                        } else{
                            $(item).addClass('hidden');
                            $(item).removeClass('f-type');
                        }
                    } else {
                        if (user_type != type) {
                            $(item).removeClass('f-type');
                        } else {
                            $(item).addClass('f-type');
                        }

                        var user_status = $(item).data('status');

                        if (user_status == status) {
                            $(item).addClass('f-status')
                        } else {
                            $(item).removeClass('f-status');
                        }
                    }
                }

            })
        }

        $('#user-search').val('');            
    });

    // Filtre statut
    $(document).on('change', '#filter-status', function (event) {
        var status = $(this).val();

        var list = $('#user-list');

        var type = $('#filter-type').val();

        if (status === '') {
            if ($('#filter-type').val() === '') {
                list.find('.list-group-item').each(function(index, item) {
                    $(item).removeClass('hidden');
                    $(item).removeClass('f-status');
                    // $(item).removeClass('f-type');
                })
            } else {
                list.find('.list-group-item').each(function(index, item) {
                   if ($(item).hasClass('f-type') == true) {
                        $(item).removeClass('hidden');
                        $(item).removeClass('f-status');
                    } else {
                        var user_type = $(item).data('type');

                        if (user_type == type) {
                            $(item).removeClass('hidden');
                            $(item).addClass('f-type');
                        }
                    }
                })
            }

        } else{

            list.find('.list-group-item').each(function(index, item) {
                var user_status = $(item).data('status');

                if ($('#filter-type').val() === '') {
                    if (user_status == status) {
                        $(item).removeClass('hidden');
                        $(item).removeClass('f-type');
                        $(item).addClass('f-status');
                    } else{
                        $(item).addClass('hidden');
                        $(item).removeClass('f-status');
                    }
                } else {
                    if ($(item).hasClass('f-type') == true) {
                        if (user_status == status) {
                            $(item).removeClass('hidden');
                            $(item).addClass('f-status');
                        } else{
                            $(item).addClass('hidden');
                            $(item).removeClass('f-status');
                        }
                    } else{
                        if (user_status != status) {
                            $(item).removeClass('f-status');
                        } else {
                            $(item).addClass('f-status');
                        }

                        var user_type = $(item).data('type');

                        if (type == user_type) {
                            $(item).addClass('f-type');
                        } else {
                            $(item).removeClass('f-type');
                        }
                    }
                }

            })
        }

        $('#user-search').val('');            
    });

    function similar_text(search_text, item_text) {
         
         var in_item = item_text.indexOf(search_text);
         if (in_item >= 0) {
             return true;
         }

         // var split = item_text.split(" ");
         // for (var i = 0; i < split.length; i++) {
         //     var sub = split[i];
         //     var in_search = search_text.indexOf(sub);
         //     if (in_search >= 0) {
         //          return true;
         //      } 
         // }

         return false;
    }

    /** Chercher un utilisateur */
    user_search.addEventListener('keyup', makeDebounce(function(e) {
        var search_text = accent_fold(e.target.value).toLowerCase();
        $('#user-list').find('.list-group-item').each(function(index, item) {
           var item_text = accent_fold($(item).text()).toLowerCase();

           if ($('#filter-status').val() === '') {
               if ($('#filter-type').val() === '') {
                   if (similar_text(search_text,item_text) == true) {
                       $(item).removeClass('hidden');
                   } else {
                       $(item).addClass('hidden');
                   }
               } else {
                   if ($(item).hasClass('f-type') == true) {
                       if (similar_text(search_text,item_text) == true) {
                           $(item).removeClass('hidden');
                       } else {
                           $(item).addClass('hidden');
                       }
                   }
               }
           } else{
               if ($(item).hasClass('f-status') == true) {
                   if ($('#filter-type').val() === '') {
                       if (similar_text(search_text,item_text) == true) {
                           $(item).removeClass('hidden');
                       } else {
                           $(item).addClass('hidden');
                       }
                   } else {
                       if ($(item).hasClass('f-type') == true) {
                           if (similar_text(search_text,item_text) == true) {
                               $(item).removeClass('hidden');
                           } else {
                               $(item).addClass('hidden');
                           }
                       }
                   }

               }
           }

           
        });
    }, 500));

    function setUserRoles(load_user, data) {
        var user_form = $('#user-form');
        if (load_user && typeof data === 'undefined')
            return;
        user_form.removeClass('hidden');
        if (load_user) {
            if(data.currentUserType && data.currentUserType > 2) {
                user_form.find('input, select, button').attr('disabled', 'disabled');
                switchery_actif.disable();
                switchery_demo.disable();
            } else {
                user_form.find('input, select, button').removeAttr('disabled');
                switchery_actif.enable();
                switchery_demo.enable();
            }
            /* Utilisateur actif ou inactif */
            if (data.supprimer == 1) {
                //Inactif
                if (user_actif.prop('checked') === true) {
                    user_actif.parent().find(".switchery").trigger("click");
                }
            } else {
                //Actif
                if (user_actif.prop('checked') === false) {
                    user_actif.parent().find(".switchery").trigger("click");
                }
            }
            /* Utilisateur demo */
            if (data.showDossierDemo == 1) {
                /* Demo */
                if (user_show_demo.prop('checked') === false) {
                    user_show_demo.parent().find(".switchery").trigger("click");
                }
            } else {
                /* Not Demo */
                if (user_show_demo.prop('checked') === true) {
                    user_show_demo.parent().find(".switchery").trigger("click");
                }
            }


            $('#register_update_nom').val(data.nom);
            $('#register_update_prenom').val(data.prenom);
            $('#register_update_email').val(data.email);
            $('#register_update_societe').val(data.societe);
            $('#register_update_telephone').val(data.tel);
            $('#register_update_skype').val(data.skype);

            /* Role de l'utilisateur */
            user_role.val(data.accesUtilisateur.id);

            /* Client auquel l'utilisateur appartient s'il existe */
            if (typeof data.client !== 'undefined' && data.client != null) {
                client.val(data.client.id);
            }
        }

        register_form.find('.form-group')
            .removeClass('has-error')
            .find('.help-block')
            .remove();
        register_form2.find('.form-group')
            .removeClass('has-error')
            .find('.help-block')
            .remove();

        selected_role = user_role.val();
        selected_role_text = user_role.find('option:selected').text();
        role_group = user_role.find('option:selected').attr('data-type');
        if (selected_role !== '') {
            all_client_group.removeClass('hidden');
        } else {
            all_client_group.addClass('hidden');
        }
        site_group.addClass('hidden');
        dossier_group.addClass('hidden');
        site.empty();
        site.trigger("chosen:updated");
        dossier.empty();
        dossier.trigger("chosen:updated");
        client_multi
            .val('')
            .trigger("chosen:updated");
        client_multi_group.addClass('hidden');
        client_group.removeClass('hidden');

        if (role_group === '2') {
            /* ROLE SCRIPTURA */
            client_group.addClass('hidden');
            client_multi_group.removeClass('hidden');
            if (load_user) {
                var user_clients = [];
                if (typeof data.clients !== 'undefined' && data.clients != null) {
                    $.each(data.clients, function (index, item) {
                        if (typeof item.client !== 'undefined' && item.client != null) {
                            user_clients.push(item.client.id);
                        }
                    });
                }
                client_multi.val(user_clients)
                    .trigger("chosen:updated");
            }
        } else if (role_group === '3') {
            /* ROLE CLIENT */

        } else if (role_group === '4') {
            /* ROLE SITE */
            site_group.removeClass('hidden');
            getSites(client, site, function () {
                if (load_user) {
                    var user_sites = [];
                    if (typeof data.sites !== 'undefined' && data.sites != null) {
                        $.each(data.sites, function (index, item) {
                            if (typeof item.site !== 'undefined' && item.site != null) {
                                user_sites.push(item.site.id);
                            }
                        });
                    }
                    site.val(user_sites)
                        .trigger("chosen:updated");
                }
            });
        } else if (role_group === '5' || role_group === '6') {
            /* ROLE DOSSIER ET CLIENT FINAL */
            dossier_group.removeClass('hidden');
            getDossiers(client, dossier, function () {
                if (load_user) {
                    var user_dossiers = [];
                    if (typeof data.dossiers !== 'undefined' && data.dossiers != null) {
                        $.each(data.dossiers, function (index, item) {
                            if (typeof item.dossier !== 'undefined' && item.dossier != null) {
                                user_dossiers.push(item.dossier.id);
                            }
                        });
                    }
                    dossier.val(user_dossiers)
                        .trigger("chosen:updated");
                }
            });
        }
    }
});
