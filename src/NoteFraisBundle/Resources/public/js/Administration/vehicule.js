$(document).ready(function(){

    $(document).on('click', '.table-vehicule td, .btn-vehicule-add', function(){

        var vehicule_id = $(this).closest('tr').attr('data-id');

        if(typeof vehicule_id=== 'undefined'){
            vehicule_id = 0;
        }

        showVehiculeModal(vehicule_id, true);

    });

    $(document).on('click', '#js_save_vehicule', function(){
        var id = $('#js_vehicule_edit').attr('data-id');
        saveVehicule(id);
    });


});


function reloadVehiculeTable(){
    $.ajax({
        url: Routing.generate('note_frais_table_vehicule'),
        data: { dossierId: $('#dossier').val() },
        type: 'POST',
        success: function(data){
            $('.vehicule-table').html(data);
            $('.footable').footable();
        }
    });
}

function reloadVehiculeCombo(){
    $.ajax({
        url: Routing.generate('note_frais_combo_vehicule'),
        data:{dossierId: $('#dossier').val() },
        type: 'POST',
        success: function(data){
            $('#js_depense_fk_vehicule').html(data);
            $("#js_depense_fk_vehicule").trigger("chosen:updated");
        }
    })
}

function saveVehicule(id){

    var marque = $('#js_marque').val();
    var modele = $('#js_modele').val();
    var immatricule = $('#js_immatricule').val();
    var typeVehicule = $('#js_type_vehicule').val();
    var puissanceFicsal = $('#js_puissance_fiscal').val();
    var typeRemboursement = $('#js_type_remboursement').val();
    var carburant = $('#js_carburant_vehicule').val();

    $.ajax({
        url: Routing.generate('note_frais_admin_vehicule_edit', {json: 1}),
        data: {
            dossierId: $('#dossier').val(),
            vehiculeId: id,
            marque: marque,
            modele: modele,
            immatricule: immatricule,
            typeRemboursement: typeRemboursement,
            typeVehicule: typeVehicule,
            puissanceFiscal: puissanceFicsal,
            carburant: carburant
        },
        type: 'POST',
        success: function (data) {

            var fromAdmin = $('#js_vehicule_edit').attr('from-admin');

            if(fromAdmin ==  1) {

                reloadVehiculeTable();

            }
            else{
                reloadVehiculeCombo();
            }


            $('#js_vehicule_edit').modal('hide');

        }
    });
}

function showVehiculeModal(vehicule_id, fromAdministration){
    $.ajax({
        url: Routing.generate('note_frais_admin_vehicule_edit'),
        data: {
            dossierId: $('#dossier').val(),
            vehiculeId: vehicule_id
        },
        type: 'POST',
        async: true,
        dataType: 'html',
        success: function (data) {

            setModalWidth(window.innerWidth);

            $('#js_vehicule_form').html(data);

            $('#js_vehicule_edit').attr('data-id', vehicule_id);

            var fromAdmin = 0;
            if(fromAdministration){
                fromAdmin = 1;
            }

            $('#js_vehicule_edit').attr('from-admin', fromAdmin);

            $('#js_vehicule_edit').modal('show');

            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
        }
    });
}

