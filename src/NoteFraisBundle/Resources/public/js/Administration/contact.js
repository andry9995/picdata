$(document).ready(function(){
    $(document).on('click', '.table-contact td, .btn-contact-add', function(){

        var contact_id = $(this).closest('tr').attr('data-id');

        if(typeof contact_id=== 'undefined'){
            contact_id = 0;
        }

        showContactModal(contact_id, true);

    });

    $(document).on('click', '#js_save_contact', function(){
        var id = $('#js_contact_edit').attr('data-id');
        saveContact(id);
    });

});

function reloadContactTable(){
    $.ajax({
        url: Routing.generate('note_frais_table_contact'),
        data: { dossierId: $('#dossier').val() },
        type: 'POST',
        success: function(data){
            $('.contact-table').html(data);
            $('.footable').footable();
        }
    });
}

function saveContact(id){
    var nom = $('#js_contact_nom').val();
    var prenom = $('#js_contact_prenom').val();
    var telephone = $('#js_contact_telephone').val();
    var mail = $('#js_contact_mail').val();
    var fonction = $('#js_contact_fonction').val();
    var contactType = $('#js_contact_type').val();

    $.ajax({
        url: Routing.generate('note_frais_admin_contact_edit', {json: 1}),
        type: 'POST',
        data: {
            dossierId: $('#dossier').val(),
            contactId: id,
            nom: nom,
            prenom: prenom,
            telephone: telephone,
            mail: mail,
            fonction: fonction,
            contactType: contactType
        },

        success: function (data) {
            var fromAdmin = $('#js_contact_edit').attr('from-admin');


            $('#js_contact_edit').modal('hide');
            if(fromAdmin ==  1) {
                reloadContactTable();
            }
            else{

            }
        }
    });

}

function showContactModal(contact_id, fromAdministration){
    $.ajax({

        url: Routing.generate('note_frais_admin_contact_edit'),
        data: {
            dossierId: $('#dossier').val(),
            contactId: contact_id
        },
        type: 'POST',
        async: true,
        dataType: 'html',
        success: function (data) {

            setModalWidth(window.innerWidth);

            $('#js_contact_form').html(data);

            $('#js_contact_edit').attr('data-id', contact_id    );

            var fromAdmin = 0;
            if(fromAdministration){
                fromAdmin = 1;
            }

            $('#js_contact_edit').attr('from-admin', fromAdmin);

            $('#js_contact_edit').modal('show');

            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
        }
    });
}
