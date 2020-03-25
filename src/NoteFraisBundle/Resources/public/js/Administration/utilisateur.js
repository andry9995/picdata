$(document).ready(function(){

    $(document).on('change', '.utilisateur-check', function(){
        setActionUtilisateurItems();
    });

    $(document).on('click', '.action-utilisateur-activer, .action-utilisateur-desactiver', function(){

        var act = $(this).attr('class');
        var actVal = 0;

        if(act == 'action-utilisateur-activer'){
            actVal = 1;
        }

        activeDesactiveUtilisateurRows(actVal);

    });

    $(document).on('click', '.table-utilisateur td, .btn-utilisateur-add', function(){

        if($(this).hasClass('utilisateur-check')){
            return;
        }

        if($(this).parent().parent().is('tfoot')){
            return;
        }

        if($(this).parent().hasClass('categorie')){
            return;
        }

        var utilisateurId = $(this).closest('tr').attr('data-id');

        if(typeof utilisateurId=== 'undefined'){
            utilisateurId = 0;
        }

        $.ajax({
            url: Routing.generate('note_frais_admin_utilisateur_edit'),
            data: {
                dossierId: $('#dossier').val(),
                utilisateurId: utilisateurId
            },
            type: 'POST',
            async: true,
            dataType: 'html',
            success: function (data) {

                setModalWidth(window.innerWidth);
                $('#js_utilisateur_form').html(data);

                $('#js_utilisateur_edit').attr('data-id', utilisateurId);

                $('#js_utilisateur_edit').modal('show');

            }
        });

    });

    $(document).on('click', '#js_save_utilsateur', function(){
        var id = $('#js_utilisateur_edit').attr('data-id');
        saveutilisateur(id);
    });

});


function activeDesactiveUtilisateurRows(type) {

    $('.table-utilisateur tbody tr').each(function () {

        var checked = $(this).find('.utilisateur-check input').is(":checked");
        var td = $(this).find('.utilisateur-status');

        if (checked) {

            var id = $(this).attr('data-id');

            $.ajax({
                url: Routing.generate('note_frais_admin_utilisateur_status'),
                type: 'POST',
                data: {utilisateurId: id, status: type},
                success: function () {
                    setStatusText(type, td);
                }
            });
        }
    });

}


function reloadUtilisateurTable(){
    $.ajax({
        url: Routing.generate('note_frais_utilisateur'),
        data:{ dossierId: $('#dossier').val() },
        type: 'POST',
        success: function(data){
            $('.utilisateur-table').html(data);
            $('.footable').footable();
        }
    });
}


function saveutilisateur(id){
    var nom = $('#js_utilisateur_nom').val();
    var prenom = $('#js_utilisateur_prenom').val();
    var mail = $('#js_utilisateur_mail').val();
    var matricule = $('#js_tva_rec').val();
    var administrateur = $('#js_utilisateur_admin').is(":checked");
    var status = $('#js_utilisateur_status').is(":checked");

    $.ajax({
        url: Routing.generate('note_frais_admin_utilisateur_edit', {json: 1}),
        type: 'POST',
        async: true,
        data: {
            dossierId: $('#dossier').val(),
            utilisateurId: id,
            nom: nom,
            prenom: prenom,
            mail: mail,
            matricule: matricule,
            administrateur: administrateur,
            status: status
        },

        success: function (data) {

            reloadUtilisateurTable();

            $('#js_utilisateur_edit').modal('hide');
        }
    });
}


function setActionUtilisateurItems(){

    var trouveActivee = false;
    var trouveDesactivee = false;

    $('.table-utilisateur tbody tr').each(function(){

        var checked = $(this).find('.utilisateur-check input').is(":checked");
        var status = $(this).find('.utilisateur-status span').attr('data-status');

        if(checked){
            if(parseInt(status) === 0){
                trouveDesactivee = true;
            }

            if(parseInt(status) === 1){
                trouveActivee = true;
            }
        }

        if(trouveDesactivee && trouveActivee){
            return false;
        }
    });

    var actionDropDownMenu = $('.btn-utilisateur-action').closest('.input-group-btn').find('.dropdown-menu');
    actionDropDownMenu.html('');

    if(trouveDesactivee){
        actionDropDownMenu.append('<li><a href="#" class="action-utilisateur-activer" >Activer</a></li>');
    }

    if(trouveActivee){
        actionDropDownMenu.append('<li><a href="#" class="action-utilisateur-desactiver">Désactiver</a></li>');
    }

}

