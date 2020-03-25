/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

function sendDocument(type, id) {
    var form = $('#email-form');
    var recipient = form.find('#recipient');
    var sender = form.find('#sender');
    var subject = form.find('#subject');
    var message = form.find('#message');
    if (validateField(recipient)) {
        $.ajax({
            url: Routing.generate('one_email_document', {'type': type, 'id': id}),
            type: 'POST',
            dataType: 'html',
            data: {
                'recipient': recipient.val(),
                'sender': sender.val(),
                'subject': subject.val(),
                'message': message.val(),
                'dossierId': $('#dossier').val()
            },
            success: function(response) {
                show_info('Succès', 'Votre devis a été envoyé', response['type']);
                recipient.val('');
                message.val('');
            }
        });
    }
}

function showEmailAction() {
    $('.send-action').removeClass('hidden');
    $('.all-action').addClass('hidden');
}

function showAllAction() {
    $('.all-action').removeClass('hidden');
    $('.send-action').addClass('hidden');
}


function showNewMail(tiersId) {
    $.ajax({
        url: Routing.generate('one_email_new', {'tiersId': tiersId}),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#primary-modal').find('.modal-content').html(response);
            openModal();
        }
    });
}


function sendMail(tiersId) {
    var form = $('#email-form');
    var recipient = form.find('#recipient');
    var sender = form.find('#sender');
    var subject = form.find('#subject');
    var message = form.find('#message');
    if (validateField(recipient) && validateField(message)) {
        $.ajax({
            url: Routing.generate('one_email_send'),
            type: 'POST',
            dataType: 'html',
            data: {
                'recipient': recipient.val(),
                'sender': sender.val(),
                'subject': subject.val(),
                'message': message.val(),
                'tiersId': tiersId
            },
            success: function(response) {
                show_info('Succès', 'Votre mail a été envoyé', response['type']);
                closeModal();
            }
        });
    }
}