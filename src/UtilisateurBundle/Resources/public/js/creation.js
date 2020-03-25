/**
 * Created by TEFY on 09/02/2017.
 */
$(function () {
    /* Activer chosen */
    $('.chosen-select').chosen({
        width: '100%',
        search_contains: true
    });

    /* Activer js-switch */
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function(html) {
        var switchery = new Switchery(html, {
            size: 'small',
            color: '#18a689'
        });
    });

    /* Height box-user-content */
    var window_height = window.innerHeight;
    $('#box-user-content').height(window_height - 100);

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

    /* Selection rôle utilisateur */
    $(document).on('change', '#user-role', function () {
        selected_role = user_role.val();
        selected_role_text = user_role.find('option:selected').text();
        role_group = user_role.find('option:selected').attr('data-type');
        if (selected_role != '') {
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
        if (role_group == '2') {
            /* ROLE SCRIPTURA */
            client_group.addClass('hidden');
            client_multi_group.removeClass('hidden');
        } else if (role_group == '3') {
            /* ROLE CLIENT */

        } else if (role_group == '4') {
            /* ROLE SITE */
            site_group.removeClass('hidden');
            getSites(client, site);
        } else if (role_group == '5' || role_group == '6') {
            /* ROLE DOSSIER ET CLIENT FINAL */
            dossier_group.removeClass('hidden');
            getDossiers(client, dossier);
        }
    });

    /* Séléction client */
    $(document).on('change', '#client', function (event) {
        event.preventDefault();
        role_group = user_role.find('option:selected').attr('data-type');
        if (role_group == '4') {
            getSites(client, site);
        }
        if (role_group == '5' || role_group == '6') {
            getDossiers(client, dossier);
        }
    });

    //Enregistrer nouvel utilisateur
    $(document).on('click', '#btn-save-user', function (event) {
        event.preventDefault();
        var alert = $('#user-register-alert');
        var register_form = $('#user-register-form');
        var register_form2 = $('#user-register-form2');
        var form_data = new FormData(register_form[0]);
        form_data.append('user_actif', $('#user-actif').prop('checked') == true ? 1 : 0);
        form_data.append('user_show_demo', $('#user-show-demo').prop('checked') == true ? 1 : 0);
        form_data.append('user_role', $('#user-role').val());
        form_data.append('client', client.val());
        form_data.append('client_multi', client_multi.chosen().val());
        form_data.append('site', site.chosen().val());
        form_data.append('dossier', dossier.chosen().val());
        form_data.append('user_type', $('#user-type').val());
        

        alert.addClass('hidden');
        $.ajax({
            url: Routing.generate('user_register_add'),
            type: 'POST',
            data: form_data,
            processData: false,
            contentType: false,
            success: function (res) {
                res = $.parseJSON(res);
                register_form.find('.form-group')
                    .removeClass('has-error')
                    .find('.help-block')
                    .remove();
                register_form2.find('.form-group')
                    .removeClass('has-error')
                    .find('.help-block')
                    .remove();

                if (res.is_form_valid == false) {
                    alert
                        .removeClass('hidden alert-success')
                        .addClass('alert-danger')
                        .find('.alert-message')
                        .text("Impossible d'enregistrer l'utilisateur. Merci de corriger le(s) erreur(s) suivante(s).");
                    for (var key in res.error_data) {
                        $(register_form.find('[name*="' + key + '"]')[0]).after('<span class="help-block"><ul class="list-unstyled"><li>' + res.error_data[key] + '</li></ul></span>');
                        $(register_form.find('[name*="' + key + '"]')[0]).closest('.form-group').addClass('has-error');

                        $(register_form2.find('[name*="' + key + '"]')[0]).after('<span class="help-block"><ul class="list-unstyled"><li>' + res.error_data[key] + '</li></ul></span>');
                        $(register_form2.find('[name*="' + key + '"]')[0]).closest('.form-group').addClass('has-error');
                    }
                } else {
                    alert
                        .removeClass('hidden alert-danger')
                        .addClass('alert-success')
                        .find('.alert-message')
                        .html(res.message);
                    clearChamps();
                }
            }
        });
    });

    function clearChamps() {
        $('#register_nom').val('');
        $('#register_prenom').val('');
        $('#register_email').val('');
        $('#register_telephone').val('');
        $('#register_skype').val('');
        $('#user-role').val('');
        $('#register_societe').val('');

        client_multi.val('').trigger('chosen:updated');
        site.val('').trigger('chosen:updated');
        dossier.val('').trigger('chosen:updated');

        site_group.addClass('hidden');
        dossier_group.addClass('hidden');
        client_multi_group.addClass('hidden');
        client_group.addClass('hidden');

        $('#user-show-demo').prop('checked', false);
    }
});


