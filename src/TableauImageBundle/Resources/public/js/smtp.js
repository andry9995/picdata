$(function() {
    var window_height = window.innerHeight;
    var notification_container = $('#notification').find('.panel-body'),
        form_smtp = $('#form-smtp'),
        client_smtp = $('#client-smtp'),
        param_smtp_smtp = $('#param_smtp_smtp'),
        param_smtp_port = $('#param_smtp_port'),
        param_smtp_login = $('#param_smtp_login'),
        param_smtp_password_first = $('#param_smtp_password_first'),
        param_smtp_password_second = $('#param_smtp_password_second'),
        param_smtp_certificate = $('#param_smtp_certificate'),
        param_smtp_copie = $('#param_smtp_copie'),
        smtp_missing = $('#param-smtp-missing');
    notification_container.height(window_height - 170).css("overflow-y", "auto");

    client_smtp.on('change', function() {
        var client = $(this).val(),
            url = Routing.generate('param_smtp_client', {client: client});
        resetSmtpForm();
        form_smtp.find('.form-group')
            .removeClass('has-error')
            .find('.help-block')
            .remove();
        fetch(url, {
            method: 'GET',
            credentials: 'include'
        }).then(function(response) {
            if (response.ok) {
                return response.json();
            } else {
                if (response.status === 404) {
                    show_info('Erreur', "Client introuvable.", 'error');
                } else {
                    show_info('Erreur', "Une erreur serveur a été survenue.", 'error');
                }
            }
        }).then(function(data) {
           if (typeof data === 'object' || data.hasOwnProperty('id')) {
               param_smtp_smtp.val(data.smtp);
               param_smtp_port.val(data.port);
               param_smtp_login.val(data.login);
               param_smtp_password_first.val(data.password);
               param_smtp_password_second.val(data.password);
               param_smtp_certificate.val(data.certificate);
               param_smtp_copie.val(data.copie);
               smtp_missing.addClass('hidden');
           } else {
               show_info('', "Les paramètres SMTP ne sont pas encore configurés.", 'warning');
               smtp_missing.removeClass('hidden');
           }
        }).catch(function(error) {
            show_info('Erreur', "Une erreur a été survenue.", 'error');
        });
    });

    $('form').on('submit', function(event) {
        if($(this).attr('id') !== 'form-export') {
            event.preventDefault();
        }
    });

    client_smtp.trigger('change');

    $('#btn-save-smtp').on('click', function(event) {
        event.preventDefault();
        var client = client_smtp.val();
        var form = document.getElementById('form-smtp');
        var formData = new FormData(form);
        var url = Routing.generate('param_smtp_client_update', {client: client});
        form_smtp.find('.form-group')
            .removeClass('has-error')
            .find('.help-block')
            .remove();
        fetch(url, {
            method: 'POST',
            credentials: 'include',
            body: formData
        }).then(function(response) {
            if (response.ok) {
                return response.json();
            } else {
                show_info('Erreur', "Une erreur a été survenue.", 'error');
            }
        }).then(function(data) {
            if (data.is_form_valid === false) {
                for (var key in data.error_data) {
                    if (data.error_data.hasOwnProperty(key)) {
                        form_smtp.find('#param_smtp_' + key).after('<span class="help-block"><ul class="list-unstyled"><li>' + data.error_data[key] + '</li></ul></span>');
                        form_smtp.find('#param_smtp_' + key).closest('.form-group').addClass('has-error');
                        if (data.error_data[key].hasOwnProperty('first')) {
                            form_smtp.find('#param_smtp_password_first').after('<span class="help-block"><ul class="list-unstyled"><li>' + data.error_data[key].first + '</li></ul></span>');
                            form_smtp.find('#param_smtp_password_first').closest('.form-group').addClass('has-error');
                        }
                        if (data.error_data[key].hasOwnProperty('second')) {
                            form_smtp.find('#param_smtp_password_second').after('<span class="help-block"><ul class="list-unstyled"><li>' + data.error_data[key].second + '</li></ul></span>');
                            form_smtp.find('#param_smtp_password_second').closest('.form-group').addClass('has-error');
                        }
                    }
                }
                smtp_missing.removeClass('hidden');
                show_info('', "L'enregistrement a échoué. Merci de corriger les erreurs affichées.", 'error');
            } else {
                show_info('', "Paramètres enregistrées.", 'success');
                smtp_missing.addClass('hidden');
            }
        }).catch(function() {
            show_info('Erreur', "Une erreur a été survenue.", 'error');
        });
    });

    function resetSmtpForm() {
        param_smtp_smtp.val('');
        param_smtp_port.val('');
        param_smtp_login.val('');
        param_smtp_password_first.val('');
        param_smtp_password_second.val('');
        param_smtp_certificate.val('');
    }
});