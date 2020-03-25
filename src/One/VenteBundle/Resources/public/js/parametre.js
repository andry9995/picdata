$(document).ready(function () {

    $(document).on('change', '#client', function () {
       loadParametre();
    });

    $(document).on('change', '#dossier', function () {
        loadParametre();
    });
});

function loadParametre() {
    $.ajax({
       url: Routing.generate('one_parametre_show'),
       data: {
           dossierId: $('#dossier').val(),
           clientId: $('#client').val()
       },
       success: function (response) {
           $('#parmetre-container').html(response);
           setSwitch();
           ready_inspinia();
       }
    });
}

function saveParamNotification() {
    var form = $('#notification-form');
    var dossierId = $('#dossier').val();

    $.ajax({
        url: Routing.generate('one_notification_save', {dossierId: dossierId}),
        data: form.serialize(),
        type: 'POST',
        success: function (response) {
            if (response['action'] === 'add') {
                if (response['type'] === 'success')
                    show_info('Succès', 'Ajout effectué', response['type']);
                else if (response['type'] === 'error')
                    show_info('Erreur', 'Ajout non effectué', response['type']);

            }
            //Si édition
            else if (response['action'] === 'edit') {
                if (response['type'] === 'success')
                    show_info('Succès', 'Modification sauvegardée', response['type']);
                else if (response['type'] === 'error')
                    show_info('Erreur', 'Modification non sauvegardée', response['type']);
            }
        }

    });
}

function setSwitch(){
    $('.switch').each(function () {
        var id = $(this).attr('id');
        var elem = document.querySelector('#'+id);
        new Switchery(elem, { color: '#1AB394' });
    });
}

function setEnable(el, all) {
    var checked = false;
    if($(el).is(':checked'))
        checked = true;
    var schedules = $(el).parent().find('.schedule');
    if(all === true){
        schedules = $(el).closest('.col-sm-12').find('.schedule');
        var checkboxes = $(el).closest('.col-sm-12').find('.notification-details input[type=checkbox]');
        checkboxes.each(function(e) {
            if (!checked) {
                if ($(this).is(':checked')) {
                    // $(this).removeAttr('checked');
                    $(this).trigger('click');
                }
                $(this).attr('disabled', true);
            }
            else {
                $(this).removeAttr('disabled');
            }
        });
       schedules.each(function () {
            var child = $(this).find(">:first-child");
            if (!checked) {
                child.attr('disabled', true);
            }
        });
    }

    else {
        var notificationChecked = false;

        if($('#notification').is(':checked')){
            notificationChecked = true;
        }

        if(!notificationChecked){
            $(el).trigger('click');
        }

        schedules.each(function () {
            var child = $(this).find(">:first-child");
            if (child.is(':disabled')) {
                if (checked && notificationChecked) {
                    child.removeAttr('disabled');
                }
            }
            else {
                if (!checked) {
                    child.attr('disabled', true);
                }
            }
        });
    }
}


