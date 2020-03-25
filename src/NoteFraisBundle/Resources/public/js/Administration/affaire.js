$(document).ready(function(){

    $(document).on('click', '.table-affaire td, .btn-affaire-add, .affaire-box', function(){

        var affaire_id = $(this).closest('tr').attr('data-id');
        if(typeof affaire_id=== 'undefined'){

            affaire_id = $(this).find('.ibox-title').attr('data-id');
            if(typeof  affaire_id === 'undefined') {
                affaire_id = 0;
            }
        }

        showAffaireModal(affaire_id, true);

    });

    $(document).on('click', '#js_save_affaire', function(){
        var id = $('#js_affaire_edit').attr('data-id');
        saveAffaire(id);
    });


});

function reloadAffaireTable(){
    $.ajax({
        url: Routing.generate('note_frais_table_affaire'),
        type: 'POST',
        data:{
            dossierId: $('#dossier').val()
        },
        success: function(data){
            $('.affaire-table').html(data);
            $('.footable').footable();
        }
    });
}

function saveAffaire(id){
    var libelle = $('#js_affaire_libelle').val();
    var reference = $('#js_affaire_ref').val();
    var nomClient = $('#js_affaire_client').val();
    var facturable = $('#js_affaire_facturable').is(':checked');
    var status = $('#js_affaire_status').is(':checked');
    var dateDeb = $('#js_affaire_date_deb').val();
    var dateFin = $('#js_affaire_date_fin').val();

    $.ajax({
        url: Routing.generate('note_frais_admin_affaire_edit', {json: 1}),
        type: 'POST',
        data: {
            dossierId: $('#dossier').val(),
            affaireId: id,
            libelle: libelle,
            reference: reference,
            nomClient: nomClient,
            facturable: facturable,
            status: status,
            dateDeb: dateDeb,
            dateFin: dateFin
        },

        success: function (data) {
            var fromAdmin = $('#js_affaire_edit').attr('from-admin');


            $('#js_affaire_edit').modal('hide');
            if(fromAdmin ==  1) {
                reloadAffaireTable();
            }
            else{

            }
        }
    });

}

function showAffaireModal(affaire_id, fromAdministration){
    $.ajax({

        data: {
            dossierId: $('#dossier').val(),
            affaireId: affaire_id
        },
        url: Routing.generate('note_frais_admin_affaire_edit'),
        type: 'POST',
        async: true,
        dataType: 'html',
        success: function (data) {

            $('#js_affaire_form').html(data);

            $('#js_affaire_edit').attr('data-id', affaire_id    );

            var fromAdmin = 0;
            if(fromAdministration){
                fromAdmin = 1;
            }

            setModalWidth(window.innerWidth);

            $('#js_affaire_edit').attr('from-admin', fromAdmin);

            $('#js_affaire_edit').modal('show');

            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }

            var elem_4 = document.querySelector('.js-switch_4');
            var switchery_4 = new Switchery(elem_4, { color: '#1AB394' });

            var elem_5 = document.querySelector('.js-switch_5');
            var switchery_5 = new Switchery(elem_5, { color: '#1AB394' });

            setDate();
        }
    });
}