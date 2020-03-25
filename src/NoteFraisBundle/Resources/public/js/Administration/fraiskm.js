$(document).ready(function(){
    $(document).on('change', '#js_exercice, #js_type_vehicule', function(){
        var exercice = $('#js_exercice').val();
        var typeVehicule = $('#js_type_vehicule').val();

        if(exercice !== '' && typeVehicule !== ''){
            chargerContenuFraisKmTable(exercice, typeVehicule);
        }

    });
});

function chargerContenuFraisKmTable(annee, typeVehicule) {
    $.ajax({
        url: Routing.generate('note_frais_admin_frais_km_table'),
        type: 'POST',
        data: {annee: annee, typeVehicule:typeVehicule},
        dataType: 'html',
        success: function (data) {
            $('.table-frais-km').html(data);
        }
    })
}