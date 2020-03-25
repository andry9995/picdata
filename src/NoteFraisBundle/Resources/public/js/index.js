/**
 * Created by INFO on 11/01/2018.
 */

var config = {
    '.chosen-select'           : {},
    '.chosen-select-deselect'  : {allow_single_deselect:true},
    '.chosen-select-no-single' : {disable_search_threshold:10},
    '.chosen-select-no-results': {no_results_text:'Compte non trouvé'},
    '.chosen-select-width'     : {width:"95%"}
};

$(document).ready(function(){

    var width = $(window).width();
    setTableViewByWidth(width);

    $(document).on('change', '#dossier', function(){
       chargeContenuNdf($(this).val(),'','note_frais');
    });


    $(document).on('click', '.ndf-navigation .widget', function(){
        var dossierId = $('#dossier').val();
        var page = $(this).attr('data-page');

        chargeContenuNdf(dossierId, '', page);
    });


    $(document).on('click', '.view-bloc, .view-table', function(){
        var viewTable = false;
        if($(this).hasClass('view-table')){
            viewTable = true;
        }
        setTableView(viewTable);
    });


    var selectedClass = 'widget style1 yellow-bg';

    if($('#tab-note').length > 0){
        $('.note-widget').attr('class', selectedClass);
    }

    if($('#tab-depense').length > 0){
        $('.depense-widget').attr('class', selectedClass);
    }

    if($('#tab-parametre').length > 0){
        $('.parametre-widget').attr('class', selectedClass);
    }

    if($('#tab-ik').length > 0){
        $('.ik-widget').attr('class', selectedClass);
    }

    if($('#tab-image').length > 0){
        $('.image-widget').attr('class', selectedClass);
    }

    if($('#tab-vehicule').length > 0){
        $('.vehicule-widget').attr('class', selectedClass);
    }

    if($('#tab-affaire').length > 0){
        $('.affaire-widget').attr('class', selectedClass);
    }

    if($('#tab-categorie').length > 0){
        $('.categorie-widget').attr('class', selectedClass);
    }

    if($('#tab-contact').length > 0){
        $('.contact-widget').attr('class', selectedClass);
    }

});

var lastWidth = $(window).width();
$(window).resize(function() {

    var width = $(window).width();

    if(lastWidth != width){
        setTableViewByWidth(width);
        lastWidth = width;
    }

    setModalWidth(width);

});

function setTableView(tableau){

    var table = $('.table-list');
    var tableBloc = $('.table-bloc');

    if(tableau){

        if(table.hasClass('hidden')){
            table.removeClass('hidden');
        }

        if(!tableBloc.hasClass('hidden')){
            tableBloc.addClass('hidden');
        }
    }
    else{

        if(!table.hasClass('hidden')){
            table.addClass('hidden');
        }

        if(tableBloc.hasClass('hidden')){
            tableBloc.removeClass('hidden');
        }
    }
}

function setTableViewByWidth(width){

    if(width < 992){
        setTableView(false);
    }
    else{
        setTableView(true);
    }
}

function setModalWidth(windowsWidth){


    var modalLg = $('.modal-dialog').hasClass('modal-lg');

    if(windowsWidth < 992){
        if(modalLg){
            $('.modal-dialog').removeClass('modal-lg');
        }
    }
    else {
        if (!modalLg) {
            $('.modal-dialog').addClass('modal-lg');
        }
    }

    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }

}

function chargeContenuNdf(dossierId, noteId,page){
    if(dossierId !== 0){
        var lien = '';

        switch(page){
            case 'dashboard':
                lien = Routing.generate('note_frais_note');
                break;
            case 'note_frais':
                lien = Routing.generate('note_frais_note');
                break;
            case 'depense':
                lien = Routing.generate('note_frais_depense');
                break;
            case 'envoi':
                lien = Routing.generate('note_frais_image');
                break;
            case 'administration':
                lien = Routing.generate('note_frais_administration');
                break;
            default:
                lien = Routing.generate('note_frais_note');
                break;
        }

        $('.ndf-navigation').removeClass('hidden');

        $.ajax({
            url: lien,
            type: 'POST',
            data:{
                dossierId: dossierId,
                noteId:noteId
            },
            success: function(data){
                $('.ndf-contenu').html(data);

                if(page === 'envoi'){
                    initPieceFileInput('js_ndf_envoi', 0);
                }
                else{
                    $('.footable').footable();
                }

                if(page === 'depense'){
                    setDepenseTotal();
                }
            }
        });

        $('.ndf-navigation .widget').switchClass('yellow-bg', 'blue-lg');

        $('.ndf-navigation .widget').each(function(){

            if($(this).attr('data-page') === page){
                $(this).switchClass('blue-lg', 'yellow-bg');
            }
        });
    }
    else{

        if($('.ndf-navigation').hasClass('hidden')) {
            $('.ndf-navigation').addClass('hidden');
        }
        $('.ndf-contenu').html('');
    }
}