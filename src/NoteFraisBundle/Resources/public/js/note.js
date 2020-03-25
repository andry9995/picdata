$(document).ready(function() {
    $(document).on('click', '.btn-note-add', function () {
        showNoteModal(0);
    });

    $(document).on('click', '#js_save_note', function(){
        var id = $('#js_note_edit').attr('data-id');

        if(id == undefined || id == '0' ){
            id = 0;
        }

        saveNote(id);
    });

    $(document).on('click', '.action-note-details', function() {

        var note_id = $(this).closest('tr').attr('data-id');

        if(typeof note_id === 'undefined'){
            note_id = $(this).closest('.ibox ').find('.ibox-title').attr('data-id');
        }

        chargeContenuNdf($('#dossier').val(), note_id, 'depense');

    });


    $(document).on('click', '.action-note-editer', function(){
        var note_id = $(this).closest('tr').attr('data-id');

        if(typeof  note_id == 'undefined'){
            note_id = $(this).closest('.ibox ').find('.ibox-title').attr('data-id');
        }

        showNoteModal(note_id);
    });


    $(document).on('change', '#tab-note .note-check', function(){
       // setNoteAction();
    });


    //Periode annee
    $(document).on('click', '.table-periode-annee th', function () {
        var active = false;

        if($(this).hasClass('active')){
            active = true;
        }

        $('.table-periode-annee th').each(function(){
           if($(this).hasClass('active')){
               $(this).removeClass('active');
           }
        });

        if(!active){
            $(this).addClass('active');
        }

    });

    // Periode mois
    $(document).on('click', '.table-periode td', function(){

        var debutActive = 0;
        var finActive = parseInt($(this).attr('data-value'));

        $('.table-periode td').each(function(){
           if($(this).hasClass('active')){
               debutActive = parseInt($(this).attr('data-value'));
               return false;
           }
        });


        var active = false;
        var dataValue = $(this).attr('data-value');
        if($(this).hasClass('active')){
            active = true;
        }

        if(!active) {

            var diff = finActive - debutActive;

            if(diff > 0 && debutActive !== 0){
                for(var i = 0; i<=diff; i++){
                    $('.table-periode td').each(function () {
                        if(parseInt($(this).attr('data-value')) === debutActive+i){
                            if(!$(this).hasClass('active')){
                                $(this).addClass('active');
                            }
                        }
                    });
                }
            }
            else{
                if(diff > 0 && debutActive === 0){
                    $(this).addClass('active');
                }
            }

        }

        else{
            $(this).removeClass('active');
            if (dataValue !== 12) {
                $('.table-periode td').each(function () {
                    if (parseInt($(this).attr('data-value')) > dataValue) {
                        if ($(this).hasClass('active')) {
                            $(this).removeClass('active');
                        }
                    }
                });
            }
        }

    })
});



function reloadNoteTable(){
    $.ajax({
            url: Routing.generate('note_frais_table_note', {json: 3}),
        type: 'POST',
        data:{
            dossierId: $('#dossier').val()
        },
        success: function(data){
            $('.note-table-contenu').html(data);
            $('.footable').footable();
            setTableViewByWidth($(window).width());

        }
    });
}

function saveNote(noteId) {

    var dossierId = $('#dossier').val();

    var libelle = $('#js_note_libelle').val();
    var description = $('#js_note_description').val();


    var mois = [];
    var annee = null;
    var utilisateur = $('#js_note_utilisateur').val();

    $('.table-periode-annee th').each(function () {
       if($(this).hasClass('active')){
           annee = parseInt($(this).attr('data-value'));
       }
    });

    $('.table-periode td').each(function () {
       if($(this).hasClass('active')){
           mois.push(parseInt($(this).attr('data-value')));
       }
    });

    $.ajax({
        url: Routing.generate('note_frais_note_edit', {json: 1}),
        type: 'POST',
        data: {
            dossierId: dossierId,
            noteId: noteId,
            libelle: libelle,
            description: description,
            annee: annee,
            mois: mois,
            utilisateur: utilisateur
        },
        success: function (data) {

            reloadNoteTable();

            $('#js_note_edit').modal('hide');
        }
    });
}

function setNoteAction(){

    var trouveCheck = false;
    $('#tab-note .table-note .note-check').each(function(){

        var checked = $(this).find('input').is(":checked");

        if(checked){
            trouveCheck = true;
            return false;
        }
    });


    var inputGroup = $('#tab-note .btn-note-action').closest('.input-group');
    if(trouveCheck){
        if(inputGroup.hasClass('hidden')){
            inputGroup.removeClass('hidden');
        }
    }
    else{
        if(!inputGroup.hasClass('hidden')) {
            inputGroup.addClass('hidden');
        }
    }
}


function showNoteModal(note_id){
    $.ajax({
        url: Routing.generate('note_frais_note_edit'),
        data:{
            noteId:note_id,
            dossierId: $('#dossier').val()
        },
        type: 'POST',
        success: function(data){
            setModalWidth(window.innerWidth);

            $('#js_note_form').html(data);
            $('#js_note_edit').modal('show');
            $('#js_note_edit').attr('data-id', note_id);
        }
    });
}