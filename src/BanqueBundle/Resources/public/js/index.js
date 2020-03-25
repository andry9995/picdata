var selectBanque = 0;
var releveGrid = $('#js_controle_releve_liste');
var banqueGrid = $('#js_banque_liste');

$(document).ready(function(){

    var clickFiltre = 0;

    charger_site_consultation();

    $(document).on('change', '#client', function () {
        releveGrid.jqGrid('clearGridData');
        banqueGrid.jqGrid('clearGridData');

        $('#js_num_compte_hidden').val("").attr('data-id',$('#js_zero_boost').val());

        selectBanque = 0;

    });

    $(document).on('change','#site',function () {
        charger_dossier_consultation();

        releveGrid.jqGrid('clearGridData');
        banqueGrid.jqGrid('clearGridData');

        selectBanque = 0;

        $('#js_num_compte_hidden').val("").attr('data-id',$('#js_zero_boost').val());
    });

    $(document).on('change', '#dossier', function () {

        charger_banque($('#dossier').val());

        selectBanque = 0;

        $('#js_num_compte_hidden').val("").attr('data-id',$('#js_zero_boost').val());

        var id = getIdContenu($(this));

        switch (id){
            case 'js_gestion_banque':

                if(isDossierSelected()) {
                    goGestionBanque();
                }
                break;
        }
    });

    $(document).on('change', '#js_exercice', function () {
        releveGrid.jqGrid('clearGridData');

        if(selectBanque == 0){
            verifierMultipleNumCompte();
        }
    });

    $(document).on('change', '#js_banque', function(){
        releveGrid.jqGrid('clearGridData');

        releveGrid.jqGrid('setGridParam',{
            footerrow: false
        });

        $('#js_num_compte_hidden').val("").attr('data-id',$('#js_zero_boost').val());
        selectBanque = 0;

        verifierMultipleNumCompte();

    });

    $(document).on('click', '.go-banque', function(){

        var id = getIdContenu($(this));

        // var isDossierExerciceSelect = isDossierExerciceSelected();
        // var isDossierSelect = isDossierSelected();

        switch (id){
            case 'js_pilote':

                if(isDossierExerciceSelected() == true) {
                    goPilote();
                }
                break;

            case 'js_gestion_banque':

                if(isDossierSelected() == true){
                    goGestionBanque();
                }

                break;
            case 'js_controle_releve':
                if(isDossierExerciceSelected() == true){

                    var idClient = $('#client').val(),
                        idSite = $('#site').val(),
                        idDossier = $('#dossier').val(),
                        idBanque = $('#js_banque').val(),
                        exercice = $('#js_exercice').val(),
                        numCompte = $('#js_num_compte_hidden').val();


                    goControleReleve(idClient,idSite,idDossier,idBanque,exercice,numCompte);
                }
                break;

            case 'js_releve_banque':
                if(isDossierExerciceSelected() == true){
                    charger_analyse();
                }
                break;

        }



    });

    $(document).on('click', '#btn-select-num-compte', function(){
        $('#js_num_compte_hidden').val($('#js_num_compte').val()).attr('data-id',$('#js_num_compte option:selected').attr('data-id'));
        $('#num-compte-modal').modal('hide');

        selectBanque = 1;
    });

    $(document).on('click', '#js_banque',function () {
        clickFiltre++;
        if (clickFiltre == 2) {
            $(this).change();
            clickFiltre = 0;
        }
    });

    $(document).on('click', '.navbar-minimalize', function () {
        setTimeout(function () {
            setTableauWidth();
        }, 1000);
    });

    $(window).on('resize', function() {
        setTableauWidth();

        setChartHeight();
    });

    $(document).on('click','.js_show_image_',function(){
        show_image_pop_up($(this).closest('tr').find('.js_id_image').text());
    });

    $(document).on('click','.js_show_image_soeur',function(){
        show_image_pop_up($(this).closest('tr').find('.js_id_image_soeur').text());
    });

    $(document).on('click','.js_show_image_a_affecter',function(){
        show_image_a_affecter($(this));
    });

    $(document).on('click','.js_show_details',function(){
        show_details_releve($(this));
    });

});


function getIdContenu(input){
    var parent = input.closest(".row");
    var next = parent.next();
    var id = next.find(".scroller").attr('id');

    return id;
}

function isDossierSelected(){
    var res = true;

    if( $('#dossier option:selected').text().trim() == '' ||
        $('#dossier option:selected').text().trim().toUpperCase() == 'TOUS' ||
        $('#dossier').length <= 0
    ){
        show_info('NOTICE','CHOISIR UN DOSSIER','error');
        res = false;
    }
    return res;
}

function isDossierExerciceSelected(){
    var res = true;

    if( $('#dossier option:selected').text().trim() == '' ||
        $('#dossier option:selected').text().trim().toUpperCase() == 'TOUS' ||
        $('#dossier').length <= 0 ||
        parseInt($('#js_exercice').val()) == 0
    ){
        show_info('NOTICE','CHOISIR UN DOSSIER ET UN EXERCICE','error');
        res = false;
    }
    return res;
}

function verifierMultipleNumCompte () {
    var idBanque = $('#js_banque').val();
    var idDossier = $('#dossier').val();
    $.ajax({
        data: {
            dossierId: idDossier,
            banqueId: idBanque
        },
        url: Routing.generate('banque_releve_num_compte'),
        type: 'POST',
        async: true,
        dataType: 'html',
        success: function (data) {
            var res = JSON.parse(data);
            if(res.length > 1){
                $('#num-compte-modal').modal('show');
                $('#js_num_compte').children().remove().end().append('<option value="">Tous</option>');

                $.each(res, function (index,value) {
                    $('<option>').val(value.compte).text(value.compte).attr('data-id',value.id).appendTo('#js_num_compte');
                });
            }
        }
    })
}


function setChartHeight(){

    $('#lineChart').css({height: '300px'});

}



