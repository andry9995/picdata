$(document).ready(function () {

    var typeConsultation = 0;
    var exercice = 0;
    var idSite = 0;
    var idDossier = 0;
    var idClient = 0;

    var clickDateScan = 0;


    var clickFiltre = 0;

    setDate();
    setScrollerHeight();

    charger_site_consultation();

    $(window).on('resize', function(e) {

        setScrollerHeight();

        typeConsultation = $('#js_filtre_consultation').val();
        if (typeConsultation == 6) {
            setTableauWidth(false);
        }
        else {
            setTableauWidth(true);
            setTreeHeight();
        }


    });

    $('#client').closest('.form-group').find('label').find('span').remove();
    $('#client').closest('.form-group').find('label').html('<span>Client</span>');
    $('#client').closest('.form-group').find('label').css({'padding-top':'7px'});

    $('#client').closest('.form-group').find('label').toggleClass('col-lg-3 col-lg-2');
    $('#client').closest('.form-group').find('div').toggleClass('col-lg-9 col-lg-10');

    $(".filtre").resizable();

    $(".filtre").resize(function () {
        setTableauWidth(true);
    });

    setResponsiveJqGrid('js_piece_liste');

    $(document).on('click', '.navbar-minimalize', function () {

        var typeConsultation = $('#js_filtre_consultation').val();
        setTimeout(function () {
            if(typeConsultation == 6) {
                setTableauWidth(false);
            }
            else{
                setTableauWidth(true);
            }
        }, 1000);


    });

    $(document).on('change', '#js_filtre_consultation', function (e) {
        e.preventDefault();
        typeConsultation = $(this).val();

        var pieceGrid = $('#js_piece_liste'),
            filtreTree = $('#js_filtre_tree'),
            filtreDate = $('#js_filtre_date'),
            filtreAvancement = $('#js_filtre_avancement'),
            filtreNumPiece = $('#js_filtre_num_piece'),
            datatype = $(this).find('option:selected').attr('data-type')
        ;

        pieceGrid.jqGrid('GridUnload');
        $("label[for = js_debut_periode]").text("Période du");
        $('.filtre').show();

        filtreDate.attr('disabled', true);
        filtreDate.val('');

        switch (typeConsultation) {
            //Par categorie
            case '1':
                filtreTree.show();
                // $('#js_filtre_date').hide();
                filtreNumPiece.hide();
                filtreAvancement.hide();

                startCategorieSearch();

                break;

            //Par tiers
            case '2':

                filtreTree.show();
                // $('#js_filtre_date').hide();
                filtreNumPiece.hide();
                filtreAvancement.hide();

                startTiersSearch();

                break;

            case '3':
                break;

            //Par utilisateur
            case '4':
                filtreTree.show();
                // $('#js_filtre_date').hide();
                filtreNumPiece.hide();
                filtreAvancement.hide();

                startUtilisateurSearch();

                break;

            //Par numero pièces
            case '5':
                filtreTree.hide();
                // $('#js_filtre_date').hide();
                filtreNumPiece.show();
               filtreAvancement.hide();
                break;

            //Par date ou telechargement
            case '6':
            case '8':
                filtreTree.hide();
                // $('#js_filtre_date').show();
                filtreNumPiece.hide();
                filtreAvancement.hide();

                $('.filtre').hide();

   // $('#js_filtre_date').removeAttr('disabled');

                filtreTree.hide();
                filtreNumPiece.hide();
                filtreAvancement.hide();
                $('.filtre').hide();



                if(typeConsultation == '6'){
                    startDateSearch(false, datatype);
                }
                else{
                    startDateSearch(true);
                }

                break;

            //Tsy manao choix
            case '':
                filtreTree.hide();
                // $('#js_filtre_date').hide();
                filtreNumPiece.hide();
                filtreAvancement.hide();
                pieceGrid.jqGrid('GridUnload');
                break;

            //Par avancement
            case '7':
                filtreTree.hide();
                // $('#js_filtre_date').hide();
                filtreNumPiece.hide();
                filtreAvancement.show();
                break;

        }
        clickFiltre = -1;
    });

    $(document).on('change', '#js_filtre_date', function(e){

        $('#js_filtre_tree').hide();
        $('#js_filtre_num_piece').hide();
        $('#js_filtre_avancement').hide();
        $('.filtre').hide();


        var idSite = $('#site').val();
        var idClient = $('#client').val();

        if($('#js_filtre_date').val() != "") {


            $('#filtre-date-modal').modal('show');

            $.ajax({
                data: {
                    clientId: idClient,
                    siteId: idSite
                },

                url: Routing.generate('consultation_piece_recherche_date'),
                type: 'POST',
                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                beforeSend: function (jqXHR) {
                    jqXHR.overrideMimeType('text/html;charset=utf-8');
                },
                async: true,
                dataType: 'html',
                success: function (data) {

                    var res = JSON.parse(data);


                    $('#js_dossier_date').children().remove().end().append('<option ></option>');

                    var dossiers = res.dossiers;

                    $.each(dossiers, function (k, v) {
                        $('<option>').val(v.id).text(v.nom_dossier).appendTo('#js_dossier_date');
                    });


                    $('#js_categorie_date').children().remove().end().append('<option ></option>');

                    var categories = res.categories;

                    $.each(categories, function (k, v) {
                        // $('<option data-id="'+v.code+'">').val(v.id).text(v.libelle).appendTo('#js_categorie_date');
                        $('<option>').val(v.id).text(v.libelle).appendTo('#js_categorie_date');
                    });

                }

            });
        }




    });

    $(document).on('change', '#site', function () {

        var recherche = $('#js_filtre_consultation').val();

        startSearch(recherche);

    });

    $(document).on('change', '#js_exercice', function () {
        var recherche = $('#js_filtre_consultation').val();

        $('#js_filtre_exercice').val($(this).val());

        if(parseInt(recherche) !== 6 && parseInt(recherche) !== 8){
            startSearch(recherche);
        }

    });

    $(document).on('change', '#js_filtre_exercice', function () {
        $('#js_exercice').val($(this).val());
    });

    $(document).on('change', '#dossier', function () {
        var recherche = $('#js_filtre_consultation').val();

        startSearch(recherche);
    });

    $(document).on('change', '#client', function () {
        var recherche = $('#js_filtre_consultation').val();

        startSearch(recherche);

    });

    $(document).on('change', '#js_filtre_avancement', function () {
        var recherche = $('#js_filtre_consultation').val();
        if (recherche == 1) {
            startCategorieSearch();
        }
        else if (recherche == 2) {
            startTiersSearch();
        }
    });

    $(document).on('click', '#btn-download', function () {

    });

    $(document).on('click', '#btn-recherche-date', function() {

        setTableauWidth(false);

        var filtre_date = $('#js_filtre_d').val();

        idSite = $('#site').val();
        exercice = $('#js_filtre_exercice').val();
        idClient = $('#client').val();

        var dateDebut = $('#js_debut_periode').val();
        var dateFin = $('#js_fin_periode').val();


        var idDossier = $('#js_dossier_date').val();
        if(idDossier == ""){
            idDossier = -1;
        }

        var idCategorie = $('#js_categorie_date').val();
        if(idCategorie == ""){
            idCategorie = -1;
        }

        var download = $(this).attr('download');

        var isDownload = false;

        if(download == '1'){
            isDownload = true;
        }



        $('#js_piece_liste').jqGrid('GridUnload');

        //Date scan
        if (filtre_date == 1) {

            $("label[for = js_debut_periode]").text("Scan du");

            if ((dateDebut != '' && dateFin != '') || (dateDebut == '' && dateFin == '')) {

                if(idDossier != -1) {

                    switch (idCategorie) {
                        case 'CODE_CLIENT':
                        case 'CODE_FRNS':
                            showClientFournisseurGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, '', false, 1, true, isDownload);
                            break;

                        //Note de frais
                        case 'CODE_NDF':
                            showNoteFraisGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, '', false, 1, true, isDownload);
                            break;

                        //Banque
                        case 'CODE_BANQUE':
                            showBanqueGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, '', false, 1, true, isDownload);
                            break;

                        //Social & Fiscal
                        case 'CODE_SOC':
                        case 'CODE_FISC':
                            showFiscalSocialGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, '', false, 1, true, isDownload);
                            break;
                        //Contrat courrier & Gestion & Juridique
                        case 'CODE_COURRIER':
                        case 'CODE_ETATS_COMPTABLE':
                        case 'CODE_GESTION':
                        case 'CODE_JURIDIQUE':
                            showCEGJGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, '', false, 1, true, isDownload);
                            break;

                        default:

                            showCommunGrid(idClient,idSite,idDossier,-1,-1,-1,exercice,0,0,0,'',false,1,true, isDownload);
                            break;
                    }
                }
                else{
                    // showDateScanGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, 0, 0, '', false, 6);

                    showCommunGrid(idClient,idSite,idDossier, -1,-1,-1,exercice,0,0,0,'',false,6,true, isDownload);

                }
                $('#filtre-date-modal').modal('hide');

            }
        }

        //Date Pièce
        else if (filtre_date == 2) {

            $("label[for = js_debut_periode]").text("Période du");

            if ((dateDebut != '' && dateFin != '') || (dateDebut == '' && dateFin == '')) {

                if (idDossier != -1) {

                    switch (idCategorie) {
                        case 'CODE_CLIENT':
                        case 'CODE_FRNS':
                            showClientFournisseurGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, '', true, 1, false, isDownload);
                            break;

                        //Note de frais
                        case 'CODE_NDF':
                            showNoteFraisGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, '', true, 1, false, isDownload);
                            break;

                        //Banque
                        case 'CODE_BANQUE':
                            showBanqueGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, '', true, 1, false, isDownload);
                            break;

                        //Social & Fiscal
                        case 'CODE_SOC':
                        case 'CODE_FISC':
                            showFiscalSocialGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, '', true, 1, false, isDownload);
                            break;
                        //Contrat courrier & Gestion & Juridique
                        case 'CODE_COURRIER':
                        case 'CODE_ETATS_COMPTABLE':
                        case 'CODE_GESTION':
                        case 'CODE_JURIDIQUE':
                            showCEGJGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, '', true, 1, false, isDownload);
                            break;

                        default:

                            showCommunGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, 0, 0, '', true, 1, false, isDownload);
                            break;
                    }
                }
                else {
                    // showDateScanGrid(idClient, idSite, idDossier, idCategorie, -1, -1, exercice, 0, 0, 0, '', true, 1);

                    // showCommunGrid(idClient,idSite,idDossier,idCategorie,-1,-1,exercice,0,0,0,'',true,6,false);

                    showCommunGrid(idClient,idSite,idDossier,idCategorie,-1,-1,exercice,0,0,0,'',true,6,false, isDownload);
                }

                $('#filtre-date-modal').modal('hide');
            }


        }





    });

    $(document).on('click', '#btn-num-piece', function () {
        idSite = $('#site').val();
        exercice = $('#js_exercice').val();
        idDossier = $('#dossier').val();
        idClient = $('#client').val();
        var numPiece = $('#js_num_piece').val();
        $('#js_piece_liste').jqGrid('GridUnload');

        //1: Jerena aloha ny categorie an'ilay num pièce
        $.ajax({
            data: {idClient: idClient, siteId: idSite, dossierId: idDossier, numPiece: numPiece, exercice: exercice},
            url: Routing.generate('consultation_categorie_num_piece'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function (jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },

            success: function (data) {

                //2: Afficher-na ny tableau
                // var catId = parseInt(data);

                var catId = JSON.parse(data);
                switch (catId) {
                    //Client & fournisseur
                    case 'CODE_CLIENT':
                    case 'CODE_FRNS':
                        showClientFournisseurGrid(idClient, idSite, idDossier, catId, -1, -1, exercice, 0, numPiece, false, 5, false, false);
                        break;

                    //Note de frais
                    case 'CODE_NDF':
                        showNoteFraisGrid(idClient, idSite, idDossier, catId, -1, -1, exercice, 0, numPiece, false, 5, false, false);
                        break;

                    //Banque
                    case 'CODE_BANQUE':
                        showBanqueGrid(idClient, idSite, idDossier, catId, -1, -1, exercice, 0, numPiece, false, 5, false, false);
                        break;

                    //Social & Fiscal
                    case 'CODE_SOC':
                    case 'CODE_FISC':
                        showFiscalSocialGrid(idClient, idSite, idDossier, catId, -1, -1, exercice, 0, numPiece, false, 5, false, false);
                        break;
                    //Contrat courrier & Gestion & Juridique
                    case 'CODE_COURRIER':
                    case 'CODE_ETATS_COMPTABLE':
                    case 'CODE_GESTION':
                    case 'CODE_JURIDIQUE':
                        showCEGJGrid(idClient, idSite, idDossier, catId, -1, -1, exercice, 0, numPiece, false, 5, false, false);
                        break;

                    default:
                        showCommunGrid(idClient, idSite, idDossier, -1, -1, -1, exercice, 0, 0, 0, numPiece, false, 5, false, false);
                        break;

                }
            }
        });
    });

    $(document).on('click', '#btn-avancement', function () {
        idSite = $('#site').val();
        exercice = $('#js_exercice').val();
        idDossier = $('#dossier').val();
        idClient = $('#client').val();
        var avancement = $('#js_avancement').val();
        $('#js_piece_liste').jqGrid('GridUnload');

        showCommunGrid(idClient, idSite, idDossier, -1, -1, -1, exercice, 0, 0, avancement, '',false, 7, false, false);

        // showAvancementGrid(idClient, idSite, idDossier, -1, -1, -1, exercice, 0, 0, avancement, '',false, 7, false);
    });

    $(document).on('click', '#js_filtre_consultation',function () {
        clickFiltre++;
        if (clickFiltre == 2) {
            $(this).change();
            clickFiltre = 0;
        }
    });


    //Affichage pièces
    $(document).on('click', '.js-piece-image', function () {
        var lien = Routing.generate('consultation_piece_image');
        var lastsel_piece = $(this).closest('tr').attr('id');

        $.ajax({
            data: {image_id: lastsel_piece},
            url: lien,
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function (jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function (data) {
                test_security(data);

                var width = $('#modal-animated').width() - 50;
                var height = $('#modal-animated').height() - 100;

                var style = 'style= "width:'+width+'px; height:'+height+'px;"';

                var src = 'http://picdata.fr/picdataovh/' + data.trim();

                var contenu = '<embed src="' + src + '" width="100%" height="100%" id="js_embed"'+style+' />';

                var options = {modal: false, resizable: true, title: ''};
                modal_ui(options, contenu);
            }
        });
    });

    $(document).on('click', '.js-piece-details', function () {

        canShowReglePaiement = 1;

        var etape = $(this).closest('tr').find('.js-piece-etape').html();
        var lastsel_piece = $(this).closest('tr').attr('id');

        var height = $(window).height() * 0.95;
        var idClient = $('#client').val();
        var exercice = $('#js_exercice').val();

        $.ajax({
            data: {
                imageId: lastsel_piece,
                clientId: idClient,
                exercice: exercice,
                height: height,
                cr: 0
            },
            url: Routing.generate('consultation_piece_data_image'),
            type: 'POST',
            async: true,
            dataType: 'html',
            success: function (data) {
                var options = {modal: false, resizable: true, title: 'Détails Pièces'};
                modal_ui(options, data, undefined, 0.95, 0.85);

                $('.date').datepicker({
                    todayBtn: "linked",
                    keyboardNavigation: false,
                    forceParse: false,
                    calendarWeeks: true,
                    autoclose: true,
                    language: "fr"
                });

                initCEGJForm(parseInt($('#modal-ui').attr('data-id')));            }
        });

    });


    $(document).on('click','.ui-button', function () {
        canShowReglePaiement = 1;
    });


    //Regle paiement
    $(document).on('click', '.js_regle_paiement_date_le_active', function(){
        if ($(this).is(":checked")) {
            $('.js_regle_paiement_date_le').removeAttr('disabled');
        }
        else {
            $('.js_regle_paiement_date_le').prop('disabled', true);
            $('.js_regle_paiement_date_le').val("");
        }
    });


    $(document).on('change', '#js_dossier_date, #js_filtre_exercice, #js_filtre_d ', function(event){
        event.stopPropagation();

        var parentDateScan = $('#js_filtre_date_scan').closest('.col-sm-12'),
            typeRecherche = $('#js_filtre_consultation').val(),
            typeDate = $('#js_filtre_d').val();

        if(!parentDateScan.hasClass('hidden')){
            parentDateScan.addClass('hidden');
        }

        if(typeDate !== undefined){
            if(parseInt(typeDate) === 1){
                parentDateScan.removeClass('hidden');
            }
            else if(parseInt(typeDate) === 2)
                return;
        }

        if(parseInt(typeRecherche) === 6 || parseInt(typeRecherche) === 8){
            initDateScans();
        }
    });



    $(document).on('click', '#js_filtre_date_scan .date_scan_select', function(event){
        event.stopPropagation();



        var debutPeriode = $('#js_debut_periode'),
            finPeriode = $('#js_fin_periode'),
            dateScanSelected = $(this).text(),
            currentRang = $(this).attr('data-rang')
        ;

        if(clickDateScan < 2){
            if(clickDateScan === 0){
               debutPeriode.val(dateScanSelected);
               finPeriode.val(dateScanSelected);

               if(!$(this).hasClass('selected')){
                   $(this).addClass('selected');
               }
            }
            else{
                //jerena ny rang raha inferieur an'lay efa selectionné
                var selectedRang = -1;

                $('#js_filtre_date_scan span').each(function () {
                   if($(this).hasClass('selected')){
                       selectedRang = $(this).attr('data-rang');

                       return true;
                   }
                });

                if(parseInt(currentRang) > parseInt(selectedRang)){
                    finPeriode.val(dateScanSelected);
                }
                else{
                    finPeriode.val(debutPeriode.val());
                    debutPeriode.val(dateScanSelected);
                }


                if(!$(this).hasClass('selected')){
                    $(this).addClass('selected')
                }
            }
            clickDateScan++;
        }
        else{

            $('#js_filtre_date_scan span').each(function () {
                if($(this).hasClass('selected')){
                    selectedRang = $(this).removeClass('selected');
                }
            });

            clickDateScan = 1;
            debutPeriode.val(dateScanSelected);
            finPeriode.val('');

            if(!$(this).hasClass('selected')){
                $(this).addClass('selected')
            }


        }

    });

    $(document).on('change', '#js_dossier_date', function(event){

        event.stopPropagation();

        var categorieSelect = $('#js_categorie_date');
        if($(this).val() === ''){
            if(!categorieSelect.is(':disabled')){
                categorieSelect.attr('disabled', true);
            }
        }
        else{
            categorieSelect.val('');
            categorieSelect.attr('disabled', false);
        }

    });

});


/**
 * Tableau Banque
 * @param idClient
 * @param idSite
 * @param idDossier
 * @param idCategorie
 * @param idSouscategorie
 * @param idSoussouscategorie
 * @param exercice
 * @param utilisateurId
 * @param numPiece
 * @param periodeSearch
 * @param typeSearch
 * @param dateScanSearch
 * @param download
 */
function showBanqueGrid(idClient, idSite, idDossier,idCategorie, idSouscategorie, idSoussouscategorie, exercice,   utilisateurId, numPiece, periodeSearch, typeSearch, dateScanSearch, download) {
    var pieceGrid = $('#js_piece_liste');
    var lastsel_piece;

    var dateDebut = $('#js_debut_periode').val();
    var dateFin = $('#js_fin_periode').val();

    var height = $("#js_scroller_parent").height() - 105;

    var lien = "";
    //
    // var lien = Routing.generate('consultation_piece_categorie_grid');
    if(typeSearch == 5) {
        lien = Routing.generate('consultation_piece_num_piece');
    }else {
        lien = Routing.generate('consultation_piece_categorie_grid');
    }


    if(!download) {
        pieceGrid.jqGrid({
            url: lien,
            postData: {
                dossierId: idDossier,
                categorieId: idCategorie,
                souscategorieId: idSouscategorie,
                soussouscategorieId: idSoussouscategorie,
                exercice: exercice, clientId: idClient,
                siteId: idSite,
                utilisateurId: utilisateurId,
                typeSearch: typeSearch,
                numPiece: numPiece,
                periodeSearch: periodeSearch,
                dateScanSearch: dateScanSearch,
                dateDebut: dateDebut,
                dateFin: dateFin,
                download: download
            },
            mtype: 'POST',
            datatype: 'json',
            loadonce: true,
            sortable: true,
            autowidth: true,
            height: height,
            shrinkToFit: true,
            viewrecords: true,
            rownumbers: true,
            // rowNum: 1000,
            // rowList: [100, 200, 500],
            rownumWidth: 40,
            pager: '#js_piece_pager',
            caption: "",
            hidegrid: false,
            colNames: ['Dossier', 'Catégorie', 'Sous Catégorie', 'Avancement', 'Banque', 'Exercice', '', 'Pièce', '#Compte', 'Du', 'Au', 'Solde init', 'Solde final', 'Date Scan'],
            colModel: [
                {
                    name: 'pieceDossier', index: 'pieceDossier', editable: false,
                    width: 100,
                    fixed: true,
                    hidden: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-dossier'
                },
                {
                    name: 'pieceCategorie', index: 'pieceCategorie', editable: false,
                    width: 80,
                    fixed: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-categorie'

                },
                {
                    name: 'pieceSouscategorie', index: 'pieceSouscategorie', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 100,
                    fixed: true,
                    classes: 'js-piece-sscategorie'

                },
                {
                    name: 'pieceEtape', index: 'pieceEtape', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 100,
                    fixed: true,
                    classes: 'js-piece-etape'

                },
                {
                    name: 'pieceBanque', index: 'pieceBanque', editable: false,
                    width: 120,
                    fixed: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-banque'
                },
                {
                    name: 'pieceExercice', index: 'pieceExercice', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 65,
                    fixed: true,
                    classes: 'js-piece-exercice'
                },
                {
                    name: 'pieceDetails', index: 'pieceDetails', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 20,
                    fixed: true,
                    classes: 'js-piece-details'
                },
                {
                    name: 'piecePiece', index: 'piecePiece', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 90,
                    fixed: true,
                    classes: 'js-piece-piece'
                },
                {
                    name: 'pieceCompte', index: 'pieceCompte', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 90,
                    fixed: true,
                    classes: 'js-piece-compte'
                },
                {
                    name: 'pieceDu', index: 'pieceDu', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-piece-du',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                },
                {
                    name: 'pieceAu', index: 'pieceAu', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-piece-tva',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                },
                {
                    name: 'pieceSoldeInit', index: 'pieceSoldeInit', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: "right",
                    formatter: "number",
                    sorttype: "number",
                    classes: 'js-piece-solde-init'
                },
                {
                    name: 'pieceSoldeFinal', index: 'pieceSoldeFinal', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: "right",
                    formatter: "number",
                    sorttype: "number",
                    classes: 'js-piece-solde-final'
                },
                {
                    name: 'pieceDateScan', index: 'pieceDateScan', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-date-scan',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                }
            ],

            onSelectRow: function (id) {
                if (id && id !== lastsel_piece) {
                    pieceGrid.restoreRow(lastsel_piece);
                    lastsel_piece = id;
                }
                pieceGrid.editRow(id, false);
            },

            beforeSelectRow: function (rowid, e) {
                var target = $(e.target);
                var item_action = (target.closest('td').children('.icon-action').length > 0);
                return !item_action;
            },
            loadComplete: function (data) {
                $('.ui-jqgrid-titlebar').hide();

                var pieceGrid = $('#js_piece_liste');
                var sumSoldeInit = pieceGrid.jqGrid('getCol', 'pieceSoldeInit', false, 'sum');
                var sumSoldeFinal = pieceGrid.jqGrid('getCol', 'pieceSoldeFinal', false, 'sum');

                pieceGrid.jqGrid('footerData', 'set', {
                    pieceAu: 'Total',
                    pieceSoldeInit: parseFloat(sumSoldeInit).toFixed(2),
                    pieceSoldeFinal: parseFloat(sumSoldeFinal).toFixed(2)
                });

                // setTableauWidth(true);
                if (dateScanSearch == true || periodeSearch == true) {
                    setTableauWidth(false);
                }
                else {
                    setTableauWidth(true);
                }

                if (data.isExpert == -1) {
                    $("#js_piece_liste").jqGrid('hideCol', 'pieceEtape');
                }

                if (data.showDossier == 1) {
                    $('#js_piece_liste').jqGrid('showCol', 'pieceDossier');
                }


            },

            footerrow: true,
            userDataOnFooter: true,
            ajaxRowOptions: {async: true}

        });

    }
    else{

        initilializeDownloadForm(idDossier,idCategorie,idSouscategorie,idSoussouscategorie,exercice,idClient,idSite,utilisateurId,typeSearch,numPiece,periodeSearch,dateScanSearch,dateDebut,dateFin,download, lien)

    }
}

/**
 * Tableau catégorie
 * @param idClient
 * @param idSite
 * @param idDossier
 * @param idCategorie
 * @param idSouscategorie
 * @param idSoussouscategorie
 * @param exercice
 * @param utilisateurId
 * @param numPiece
 * @param periodeSearch
 * @param typeSearch
 * @param dateScanSearch
 * @param download
 */
function showClientFournisseurGrid(idClient, idSite, idDossier, idCategorie, idSouscategorie, idSoussouscategorie, exercice, utilisateurId, numPiece, periodeSearch, typeSearch, dateScanSearch, download) {
    var pieceGrid = $('#js_piece_liste');
    var lastsel_piece;

    var dateDebut = $('#js_debut_periode').val();
    var dateFin = $('#js_fin_periode').val();

    var height = $("#js_scroller_parent").height() - 105;

    var lien = '';

    if(typeSearch == 5){
        lien = Routing.generate('consultation_piece_num_piece');
    }else{
        lien = Routing.generate('consultation_piece_categorie_grid');
    }


    if(!download) {

        pieceGrid.jqGrid({
            url: lien,
            postData: {
                dossierId: idDossier,
                categorieId: idCategorie,
                souscategorieId: idSouscategorie,
                soussouscategorieId: idSoussouscategorie,
                exercice: exercice,
                clientId: idClient,
                siteId: idSite,
                utilisateurId: utilisateurId,
                typeSearch: typeSearch,
                numPiece: numPiece,
                periodeSearch: periodeSearch,
                dateScanSearch: dateScanSearch,
                dateDebut: dateDebut,
                dateFin: dateFin,
                download: download
            },
            mtype: 'POST',
            datatype: 'json',
            loadonce: true,
            sortable: true,
            autowidth: true,
            height: height,
            shrinkToFit: true,
            viewrecords: true,
            rownumbers: true,
            // rowNum: 1000,
            // rowList: [100, 200, 500],
            rownumWidth: 40,
            pager: '#js_piece_pager',
            caption: "",
            hidegrid: false,
            colNames: ['Dossier', 'Catégorie', 'SS Catégorie', 'Avancement', 'Raison Sociale', 'Exercice', '', 'Pièce', 'N° Facture', 'Date Facture', 'N° Chrono', 'HT', 'TVA', 'TTC', '#Tiers', '#Résultat', 'Echéance', 'Date Scan'],
            colModel: [
                {
                    name: 'pieceDossier', index: 'pieceDossier', editable: false,
                    width: 100,
                    fixed: true,
                    hidden: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-dossier'
                },
                {
                    name: 'piece-categorie', index: 'piece-categorie', editable: false,
                    width: 100,
                    fixed: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-categorie'
                },
                {
                    name: 'piece-sscategorie', index: 'piece-sscategorie', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 100,
                    fixed: true,
                    classes: 'js-piece-sscategorie'
                },
                {
                    name: 'pieceEtape', index: 'pieceEtape', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-piece-etape'
                },
                {
                    name: 'piece-raison-sociale', index: 'piece-raison-sociale', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 100,
                    fixed: true,
                    classes: 'js-piece-raison-sociale'
                },
                {
                    name: 'piece-exercice', index: 'piece-exercice', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 60,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-exercice'
                },
                {
                    name: 'piece-details', index: 'piece-details', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 20,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-details'
                },
                {
                    name: 'piece-piece', index: 'piece-piece', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 90,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-piece'
                },
                {
                    name: 'piece-num-fact', index: 'piece-num-fact', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 100,
                    fixed: true,
                    classes: 'js-piece-num-fact'
                },
                {
                    name: 'pieceDateFact', index: 'pieceDateFact', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-date-fact',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                },

                {
                    name: 'pieceNumChrono', index: 'pieceNumChrono', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 90,
                    fixed: true,
                    classes: 'js-piece-num-chrono'

                },

                {
                    // name: 'piece-ht', index: 'piece-ht', editable: false,
                    name: 'pieceHt', index: 'pieceHt', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-ht',
                    align: "right",
                    formatter: "number",
                    sorttype: "number"
                },
                {
                    // name: 'piece-tva', index: 'piece-tva', editable: false,
                    name: 'pieceTva', index: 'pieceTva', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-tva',
                    align: "right",
                    formatter: "number",
                    sorttype: "number"
                },
                {
                    // name: 'piece-ttc', index: 'piece-ttc', editable: false,
                    name: 'pieceTtc', index: 'pieceTtc', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-ttc',
                    align: "right",
                    formatter: "number",
                    sorttype: "number"
                },
                {
                    name: 'pieceTiers', index: 'pieceTiers', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-tiers'
                },
                {
                    name: 'piece-resultat', index: 'piece-resultat', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-resultat'
                },
                {
                    name: 'pieceEcheance', index: 'pieceEcheance', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: 'center',
                    classes: 'js-echeance',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                },
                {
                    name: 'piece-date-scan', index: 'piece-date-scan', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: 'center',
                    classes: 'js-date-scan',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                }
            ],

            loadComplete: function (data) {
                $('.ui-jqgrid-titlebar').hide();

                var pieceGrid = $('#js_piece_liste');
                var sumTva = pieceGrid.jqGrid('getCol', 'pieceTva', false, 'sum');
                var sumHt = pieceGrid.jqGrid('getCol', 'pieceHt', false, 'sum');
                var sumTtc = pieceGrid.jqGrid('getCol', 'pieceTtc', false, 'sum');
                pieceGrid.jqGrid('footerData', 'set', {
                    pieceDateFact: 'Total',
                    pieceHt: parseFloat(sumHt).toFixed(2),
                    pieceTva: parseFloat(sumTva).toFixed(2),
                    pieceTtc: parseFloat(sumTtc).toFixed(2)
                });


                if (dateScanSearch == true || periodeSearch == true) {
                    setTableauWidth(false);
                }
                else {
                    setTableauWidth(true);
                }

                if (data.isExpert == -1) {
                    $("#js_piece_liste").jqGrid('hideCol', 'pieceEtape');
                }

                if (data.showDossier == 1) {
                    $('#js_piece_liste').jqGrid('showCol', 'pieceDossier');
                }


            },

            footerrow: true,
            userDataOnFooter: true,
            ajaxRowOptions: {async: true}

        });
    }

    else{

        initilializeDownloadForm(idDossier,idCategorie,idSouscategorie,idSoussouscategorie,exercice,idClient,idSite,utilisateurId,typeSearch,numPiece,periodeSearch,dateScanSearch,dateDebut,dateFin,download, lien)

    }
}

/**
 * Tableau commun
 * @param idClient
 * @param idSite
 * @param idDossier
 * @param idCategorie
 * @param idSouscategorie
 * @param idSoussouscategorie
 * @param exercice
 * @param idTiers
 * @param idUtilisateur
 * @param avancement
 * @param numPiece
 * @param periodeSearch
 * @param typeSearch
 * @param dateScanSearch
 * @param download
 */
function showCommunGrid(idClient, idSite, idDossier, idCategorie, idSouscategorie, idSoussouscategorie, exercice, idTiers, idUtilisateur, avancement, numPiece,periodeSearch, typeSearch, dateScanSearch, download) {
    var pieceGrid = $('#js_piece_liste');
    var lastsel_piece;

    var dateDebut = $('#js_debut_periode').val();
    var dateFin = $('#js_fin_periode').val();

    var height = $("#js_scroller_parent").height() - 105;

    var lien = '';

    switch (typeSearch){
        //Recherche par categorie, par utilisateur, par date scan
        case 1:
        case 4:
            lien = Routing.generate('consultation_piece_categorie_grid');
            break;

        //Recherche par tiers
        case 2:
            lien = Routing.generate('consultation_piece_tiers_grid');
            break;

        //Recherche par numero pièce
        case 5:
            lien = Routing.generate('consultation_piece_num_piece');
            break;

        //Recherche par date Scan
        case 6:
            lien = Routing.generate('consultation_piece_date_scan');
            break;

        //Recherche par avancement
        case 7:
            lien = Routing.generate('consultation_piece_avancement');
            break;
    }

    if(!download) {

        pieceGrid.jqGrid({
            url: lien,
            postData: {
                clientId: idClient,
                siteId: idSite,
                dossierId: idDossier,
                categorieId: idCategorie,
                souscategorieId: idSouscategorie,
                soussouscategorieId: idSoussouscategorie,
                tiersId: idTiers,
                avancement: avancement,
                utilisateurId: idUtilisateur,
                typeSearch: typeSearch,
                exercice: exercice,
                periodeSearch: periodeSearch,
                dateScanSearch: dateScanSearch,
                numPiece: numPiece,
                dateDebut: dateDebut,
                dateFin: dateFin,
                download: download
            },
            mtype: 'POST',
            datatype: 'json',
            loadonce: true,
            sortable: true,
            autowidth: true,
            height: height,
            shrinkToFit: true,
            viewrecords: true,
            rownumbers: true,
            // rowNum: 1000,
            // rowList: [100, 200, 500, 1000],
            rownumWidth: 40,
            pager: '#js_piece_pager',
            caption: "",
            hidegrid: false,
            colNames: ['Dossier', 'Catégorie', 'SS Catégorie', 'Avancement', 'Tiers', 'Exercice', '', 'Pièce', 'HT', 'TVA', 'TTC', 'Date Pièce', 'Date Scan'],
            colModel: [
                {
                    name: 'pieceDossier', index: 'pieceDossier', editable: false,
                    width: 100,
                    fixed: true,
                    hidden: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-dossier'
                },
                {
                    name: 'pieceCategorie', index: 'pieceCategorie', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 160,
                    fixed: true,
                    classes: 'js-piece-categorie'

                },
                {
                    name: 'pieceSscategorie', index: 'pieceSscategorie', editable: false,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-sscategorie',
                    sorttype: 'text'

                },
                {
                    name: 'pieceEtape', index: 'piece-etape', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 90,
                    fixed: true,
                    classes: 'js-piece-etape',
                    sorttype: 'text'

                },
                {
                    name: 'pieceTiers', index: 'pieceTiers', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-tiers'
                },
                {
                    name: 'pieceExercice', index: 'pieceExercice', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 60,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-exercice'
                },
                {
                    name: 'pieceDetails', index: 'pieceDetails', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 20,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-details'
                },
                {
                    name: 'piecePiece', index: 'piecePiece', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 90,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-piece'
                },

                {
                    // name: 'piece-ht', index: 'piece-ht', editable: false,
                    name: 'pieceHt', index: 'pieceHt', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-ht',
                    align: "right",
                    formatter: "number",
                    sorttype: "number"
                },
                {
                    // name: 'piece-tva', index: 'piece-tva', editable: false,
                    name: 'pieceTva', index: 'pieceTva', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-tva',
                    align: "right",
                    formatter: "number",
                    sorttype: "number"
                },
                {
                    // name: 'piece-ttc', index: 'piece-ttc', editable: false,
                    name: 'pieceTtc', index: 'pieceTtc', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-ttc',
                    align: "right",
                    formatter: "number",
                    sorttype: "number"
                },

                {
                    // name: 'piece-date-fact', index: 'piece-date-fact', editable: false,
                    name: 'pieceDateFact', index: 'pieceDateFact', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-date-fact',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                },
                {
                    name: 'pieceDateScan', index: 'pieceDateScan', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: 'center',
                    classes: 'js-date-scan',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                }
            ],
            loadComplete: function (data) {
                $('.ui-jqgrid-titlebar').hide();

                var pieceGrid = $('#js_piece_liste');
                var sumTva = pieceGrid.jqGrid('getCol', 'pieceTva', false, 'sum');
                var sumHt = pieceGrid.jqGrid('getCol', 'pieceHt', false, 'sum');
                var sumTtc = pieceGrid.jqGrid('getCol', 'pieceTtc', false, 'sum');
                pieceGrid.jqGrid('footerData', 'set', {
                    piecePiece: 'Total',
                    pieceHt: parseFloat(sumHt).toFixed(2),
                    pieceTva: parseFloat(sumTva).toFixed(2),
                    pieceTtc: parseFloat(sumTtc).toFixed(2)
                });


                if (typeSearch == 6 || dateScanSearch == true || periodeSearch == true) {
                    setTableauWidth(false);
                }
                else {
                    setTableauWidth(true);
                }

                if (data.isExpert == -1) {
                    $("#js_piece_liste").jqGrid('hideCol', 'pieceEtape');
                }

                if (data.showDossier == 1) {
                    $('#js_piece_liste').jqGrid('showCol', 'pieceDossier');
                }

            },

            footerrow: true,
            userDataOnFooter: true,
            ajaxRowOptions: {async: true}

        });

    }
    else{


        initilializeDownloadForm(idDossier,idCategorie,idSouscategorie,idSoussouscategorie,exercice,idClient,idSite,utilisateurId,typeSearch,numPiece,periodeSearch,dateScanSearch,dateDebut,dateFin,download, lien)

    }

}

/**
 * Tableau Contrat, courrier, gestion, juridique
 * @param idClient,
 * @param idSite,
 * @param idDossier
 * @param idCategorie
 * @param idSouscategorie
 * @param idSoussouscategorie
 * @param exercice
 * @param idUtilisateur
 * @param numPiece
 * @param periodeSearch
 * @param typeSearch
 * @param dateScanSearch
 * @param download
 */
function showCEGJGrid(idClient, idSite, idDossier, idCategorie, idSouscategorie, idSoussouscategorie, exercice, idUtilisateur, numPiece, periodeSearch, typeSearch, dateScanSearch, download) {
    var pieceGrid = $('#js_piece_liste');

    var dateDebut = $('#js_debut_periode').val();
    var dateFin = $('#js_fin_periode').val();

    var height = $("#js_scroller_parent").height() - 105;

    var lien = '';
    if(typeSearch == 5){
        lien = Routing.generate('consultation_piece_num_piece');
    }else{
        lien = Routing.generate('consultation_piece_categorie_grid');
    }

    if(!download) {

        pieceGrid.jqGrid({
            url: lien,
            postData: {
                dossierId: idDossier,
                categorieId: idCategorie,
                souscategorieId: idSouscategorie,
                soussouscategorieId: idSoussouscategorie,
                exercice: exercice,
                utilisateurId: idUtilisateur,
                typeSearch: typeSearch,
                numPiece: numPiece,
                clientId: idClient,
                siteId: idSite,
                periodeSearch: periodeSearch,
                dateScanSearch: dateScanSearch,
                dateDebut: dateDebut,
                dateFin: dateFin,
                download: download
            },
            mtype: 'POST',
            datatype: 'json',
            loadonce: true,
            sortable: true,
            autowidth: true,
            height: height,
            shrinkToFit: true,
            viewrecords: true,
            rownumbers: true,
            // rowNum: 1000,
            // rowList: [100, 200, 500],
            rownumWidth: 40,
            pager: '#js_piece_pager',
            caption: "",
            hidegrid: false,
            colNames: ['Dossier', 'Catégorie', 'Sous Catégorie', 'Sous sous catégorie', 'Description', 'Entité concerné 1', 'Entité concerné 2','Avancement', 'Exercice', '', 'Pièce', 'Date Pièce', 'Date Scan'],
            colModel: [
                {
                    name: 'pieceDossier', index: 'pieceDossier', editable: false,
                    width: 100,
                    fixed: true,
                    hidden: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-dossier'
                },
                {
                    name: 'pieceCategorie', index: 'pieceCategorie', editable: false,
                    width: 160,
                    fixed: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-categorie'

                },
                {
                    name: 'pieceSouscategorie', index: 'pieceSouscategorie', editable: false,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-sscategorie'

                },
                {
                    name: 'pieceSousSouscategorie', index: 'pieceSousSouscategorie', editable: false,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-ssscategorie'

                },
                {
                    name: 'pieceDescription', index: 'pieceDescription', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-piece-description'
                },
                {
                    name: 'pieceEC1', index: 'pieceEC1', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-piece-ec-1'
                },
                {
                    name: 'pieceEC2', index: 'pieceEC2', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-piece-ec-2'
                },
                {
                    name: 'pieceEtape', index: 'pieceEtape', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 100,
                    fixed: true,
                    classes: 'js-piece-etape'

                },
                {
                    name: 'pieceExercice', index: 'pieceExercice', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 60,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-exercice'
                },
                {
                    name: 'pieceDetails', index: 'pieceDetails', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 20,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-details'
                },
                {
                    name: 'piecePiece', index: 'piecePiece', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 90,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-piece'
                },
                {
                    name: 'pieceDatePiece', index: 'pieceDatePiece', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-date-piece',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                },
                {
                    name: 'pieceDateScan', index: 'pieceDateScan', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: 'center',
                    classes: 'js-date-scan',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                }
            ],

            loadComplete: function (data) {
                $('.ui-jqgrid-titlebar').hide();
                // setTableauWidth(true);

                if (dateScanSearch == true || periodeSearch == true) {
                    setTableauWidth(false);
                }
                else {
                    setTableauWidth(true);
                }

                if (data.isExpert == -1) {
                    $("#js_piece_liste").jqGrid('hideCol', 'pieceEtape');
                }

                if (data.showDossier == 1) {
                    $('#js_piece_liste').jqGrid('showCol', 'pieceDossier');
                }

            },

            footerrow: true,
            userDataOnFooter: true,
            ajaxRowOptions: {async: true}

        });

    }
    else{
        initilializeDownloadForm(idDossier,idCategorie,idSouscategorie,idSoussouscategorie,exercice,idClient,idSite,utilisateurId,typeSearch,numPiece,periodeSearch,dateScanSearch,dateDebut,dateFin,download, lien)
    }
}


/**
 * Tableau Fiscal, social
 * @param idClient,
 * @param idSite,
 * @param idDossier
 * @param idCategorie
 * @param idSouscategorie
 * @param idSoussouscategorie
 * @param exercice
 * @param idUtilisateur
 * @param numPiece
 * @param periodeSearch
 * @param typeSearch
 * @param dateScanSearch
 * @param download
 */
function showFiscalSocialGrid(idClient, idSite,idDossier, idCategorie, idSouscategorie, idSoussouscategorie, exercice, idUtilisateur , numPiece, periodeSearch, typeSearch, dateScanSearch, download) {
    var pieceGrid = $('#js_piece_liste');

    var dateDebut = $('#js_debut_periode').val();
    var dateFin = $('#js_fin_periode').val();

    var height = $("#js_scroller_parent").height() - 105;

    var lien = '';
    if(typeSearch == 5) {
        lien = Routing.generate('consultation_piece_num_piece');
    }else {
        lien = Routing.generate('consultation_piece_categorie_grid');
    }

    if(!download) {
        pieceGrid.jqGrid({
            url: lien,
            postData: {
                clientId: idClient,
                siteId: idSite,
                dossierId: idDossier,
                categorieId: idCategorie,
                souscategorieId: idSouscategorie,
                soussouscategorieId: idSoussouscategorie,
                exercice: exercice,
                utilisateurId: idUtilisateur,
                typeSearch: typeSearch,
                numPiece: numPiece,
                periodeSearch: periodeSearch,
                dateScanSearch: dateScanSearch,
                dateDebut: dateDebut,
                dateFin: dateFin,
                donwload: download
            },
            mtype: 'POST',
            datatype: 'json',
            loadonce: true,
            sortable: true,
            autowidth: true,
            height: height,
            shrinkToFit: true,
            viewrecords: true,
            rownumbers: true,
            // rowNum: 1000,
            // rowList: [100, 200, 500, 1000],
            rownumWidth: 40,
            pager: '#js_piece_pager',
            caption: "",
            hidegrid: false,
            colNames: ['Dossier', 'Catégorie', 'Sous Catégorie', 'Avancement', 'Tiers', 'Exercice', '', 'Pièce', 'Date Pièce', 'Montant', 'Echéance', 'Date Scan'],
            colModel: [
                {
                    name: 'pieceDossier', index: 'pieceDossier', editable: false,
                    width: 100,
                    fixed: true,
                    hidden: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-dossier'
                },
                {
                    name: 'pieceCategorie', index: 'pieceCategorie', editable: false,
                    width: 100,
                    fixed: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-categorie'

                },
                {
                    name: 'pieceSouscategorie', index: 'pieceSouscategorie', editable: false,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-sscategorie'

                },
                {
                    name: 'pieceEtape', index: 'pieceEtape', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 100,
                    fixed: true,
                    classes: 'js-piece-etape'

                },
                {
                    name: 'pieceTiers', index: 'pieceTiers', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-tiers'
                },
                {
                    name: 'pieceExercice', index: 'pieceExercice', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 65,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-exercice'
                },
                {
                    name: 'pieceDetails', index: 'pieceDetails', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 20,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-details'
                },
                {
                    name: 'piecePiece', index: 'piecePiece', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 90,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-piece'
                },
                {
                    // name: 'piece-date-fact', index: 'piece-date-fact', editable: false,
                    name: 'pieceDatePiece', index: 'pieceDatePiece', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 90,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-date-piece',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                },
                {
                    // name: 'piece-date-fact', index: 'piece-date-fact', editable: false,
                    name: 'pieceMontant', index: 'pieceMontant', editable: false,
                    editoptions: {defaultValue: ''},
                    align: "right",
                    formatter: "number",
                    sorttype: "number",
                    classes: 'js-piece-montant'

                },
                {
                    name: 'pieceEcheance', index: 'pieceEcheance', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: 'center',
                    classes: 'js-echeance'
                },
                {
                    name: 'pieceDateScan', index: 'pieceDateScan', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: 'center',
                    classes: 'js-date-scan',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                }
            ],

            loadComplete: function (data) {
                $('.ui-jqgrid-titlebar').hide();

                var pieceGrid = $('#js_piece_liste');
                var sumMontant = pieceGrid.jqGrid('getCol', 'pieceMontant', false, 'sum');

                pieceGrid.jqGrid('footerData', 'set', {
                    pieceDatePiece: 'Total',
                    pieceMontant: parseFloat(sumMontant).toFixed(2)
                });

                // setTableauWidth(true);


                if (dateScanSearch == true || periodeSearch == true) {
                    setTableauWidth(false);
                }
                else {
                    setTableauWidth(true);
                }

                if (data.isExpert == -1) {
                    $("#js_piece_liste").jqGrid('hideCol', 'pieceEtape');
                }

                if (data.showDossier == 1) {
                    $('#js_piece_liste').jqGrid('showCol', 'pieceDossier');
                }
                // $("tr.jqgrow:odd").css("background", "#f6f6f6");
            },

            footerrow: true,
            userDataOnFooter: true,
            ajaxRowOptions: {async: true}

        });
    }
    else{
        initilializeDownloadForm(idDossier,idCategorie,idSouscategorie,idSoussouscategorie,exercice,idClient,idSite,utilisateurId,typeSearch,numPiece,periodeSearch,dateScanSearch,dateDebut,dateFin,download, lien)

    }

}

/**
 * Tableau Note de Frais
 * @param idClient,
 * @param idSite,
 * @param idDossier
 * @param idCategorie
 * @param idSouscategorie
 * @param idSoussouscategorie
 * @param exercice
 * @param idUtilisateur
 * @param numPiece
 * @param periodeSearch
 * @param typeSearch
 * @param dateScanSearch
 * @param download
 */
function showNoteFraisGrid(idClient, idSite, idDossier,idCategorie, idSouscategorie, idSoussouscategorie, exercice, idUtilisateur, numPiece, periodeSearch, typeSearch, dateScanSearch, download) {
    var pieceGrid = $('#js_piece_liste');

    var dateDebut = $('#js_debut_periode').val();
    var dateFin = $('#js_fin_periode').val();

    var height = $("#js_scroller_parent").height() - 105;

    var lien = '';
    if(typeSearch == 5){
        lien = Routing.generate('consultation_piece_num_piece');
    }else{
        lien = Routing.generate('consultation_piece_categorie_grid');
    }


    if(!download) {
        pieceGrid.jqGrid({
            url: lien,
            postData: {
                dossierId: idDossier,
                categorieId: idCategorie,
                souscategorieId: idSouscategorie,
                soussouscategorieId: idSoussouscategorie,
                exercice: exercice,
                utilisateurId: idUtilisateur,
                typeSearch: typeSearch,
                numPiece: numPiece,
                clientId: idClient,
                siteId: idSite,
                periodeSearch: periodeSearch,
                dateScanSearch: dateScanSearch,
                dateDebut: dateDebut,
                dateFin: dateFin,
                download: download
            },
            mtype: 'POST',
            datatype: 'json',
            loadonce: true,
            sortable: true,
            autowidth: true,
            height: height,
            shrinkToFit: true,
            viewrecords: true,
            rownumbers: true,
            // rowNum: 1000,
            // rowList: [100, 200, 500],
            rownumWidth: 40,
            pager: '#js_piece_pager',
            caption: "",
            hidegrid: false,
            colNames: ['Dossier', 'Catégorie', 'Sous Catégorie', 'Avancement', 'Tiers', 'Exercice', '', 'Pièce', 'Date Pièce', 'HT', 'TVA', 'TTC', '#Tiers', '#Résultat', 'Date Scan'],
            colModel: [
                {
                    name: 'pieceDossier', index: 'pieceDossier', editable: false,
                    width: 100,
                    fixed: true,
                    hidden: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-dossier'
                },
                {
                    name: 'pieceCategorie', index: 'pieceCategorie', editable: false,
                    width: 100,
                    fixed: true,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-categorie'

                },
                {
                    name: 'pieceSouscategorie', index: 'pieceSouscategorie', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 100,
                    fixed: true,
                    classes: 'js-piece-sscategorie'

                },
                {
                    name: 'pieceEtape', index: 'pieceEtape', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 100,
                    fixed: true,
                    classes: 'js-piece-etape'

                },
                {
                    name: 'pieceTiers', index: 'pieceTiers', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    classes: 'js-tiers'
                },
                {
                    name: 'pieceExercice', index: 'pieceExercice', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 60,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-exercice'
                },
                {
                    name: 'pieceDetails', index: 'pieceDetails', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 20,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-details'
                },
                {
                    name: 'piecePiece', index: 'piecePiece', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 90,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-piece'
                },

                {
                    name: 'pieceDatePiece', index: 'pieceDatePiece', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: 'center',
                    classes: 'js-piece-date-piece',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                },
                {
                    name: 'pieceHt', index: 'pieceHt', editable: false,
                    editoptions: {defaultValue: ''},
                    align: "right",
                    formatter: "number",
                    sorttype: "number",
                    classes: 'js-piece-ht'

                },
                {
                    name: 'pieceTva', index: 'pieceTva', editable: false,
                    editoptions: {defaultValue: ''},
                    align: "right",
                    formatter: "number",
                    sorttype: "number",
                    classes: 'js-piece-tva'

                },
                {
                    name: 'pieceTtc', index: 'pieceTtc', editable: false,
                    editoptions: {defaultValue: ''},
                    align: "right",
                    formatter: "number",
                    sorttype: "number",
                    classes: 'js-piece-ttc'

                },
                {
                    name: 'pieceCptTiers', index: 'pieceCptTiers', editable: false,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-cpt-tiers'

                },
                {
                    name: 'pieceCptResultat', index: 'pieceCptResultat', editable: false,
                    editoptions: {defaultValue: ''},
                    classes: 'js-piece-cpt-resultat'
                },
                {
                    name: 'pieceDateScan', index: 'pieceDateScan', editable: false,
                    editoptions: {defaultValue: ''},
                    width: 80,
                    fixed: true,
                    align: 'center',
                    classes: 'js-date-scan',
                    formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
                }
            ],

            loadComplete: function (data) {
                $('.ui-jqgrid-titlebar').hide();

                var pieceGrid = $('#js_piece_liste');
                var sumHt = pieceGrid.jqGrid('getCol', 'pieceHt', false, 'sum');
                var sumTva = pieceGrid.jqGrid('getCol', 'pieceTva', false, 'sum');
                var sumTtc = pieceGrid.jqGrid('getCol', 'pieceTtc', false, 'sum');


                pieceGrid.jqGrid('footerData', 'set', {
                    pieceDatePiece: 'Total',
                    pieceHt: parseFloat(sumHt).toFixed(2),
                    pieceTva: parseFloat(sumTva).toFixed(2),
                    pieceTtc: parseFloat(sumTtc).toFixed(2)
                });

                // setTableauWidth(true);


                if (dateScanSearch == true || periodeSearch == true) {
                    setTableauWidth(false);
                }
                else {
                    setTableauWidth(true);
                }

                if (data.isExpert == -1) {
                    $("#js_piece_liste").jqGrid('hideCol', 'pieceEtape');
                }

                if (data.showDossier == 1) {
                    $('#js_piece_liste').jqGrid('showCol', 'pieceDossier');
                }

                // $("tr.jqgrow:odd").css("background", "#f6f6f6");
            },

            footerrow: true,
            userDataOnFooter: true,
            ajaxRowOptions: {async: true}

        });
    }
    else{

        initilializeDownloadForm(idDossier,idCategorie,idSouscategorie,idSoussouscategorie,exercice,idClient,idSite,utilisateurId,typeSearch,numPiece,periodeSearch,dateScanSearch,dateDebut,dateFin,download, lien)

    }
}


function initilializeDownloadForm(idDossier,idCategorie,idSouscategorie,idSoussouscategorie,exercice,idClient,idSite,utilisateurId,typeSearch,numPiece,periodeSearch,dateScanSearch,dateDebut,dateFin,download, url){

    $('#dossierId').val(idDossier);
    $('#categorieId').val(idCategorie);
    $('#souscategorieId').val(idSouscategorie);
    $('#soussouscategorieId').val(idSoussouscategorie);
    $('#exercice').val(exercice);
    $('#clientId').val(idClient);
    $('#siteId').val(idSite);
    $('#utilisateurId').val(utilisateurId);
    $('#typeSearch').val(typeSearch);
    $('#numPiece').val(numPiece);
    $('#periodeSearch').val(periodeSearch);
    $('#dateScanSearch').val(dateScanSearch);
    $('#dateDebut').val(dateDebut);
    $('#dateFin').val(dateFin);
    $('#download').val(download);

    // verrou_fenetre(true);

    $('#form-export')
        .attr('action', url)
        .submit();


    // verrou_fenetre(true);
    // $.ajax({
    //
    //     url: url,
    //     data: {
    //         clientId: idClient,
    //         siteId: idSite,
    //         dossierId: idDossier,
    //         categorieId: idCategorie,
    //         souscategorieId: idSouscategorie,
    //         soussouscategorieId: idSoussouscategorie,
    //         exercice: exercice,
    //         utilisateurId: utilisateurId,
    //         typeSearch: typeSearch,
    //         numPiece: numPiece,
    //         periodeSearch: periodeSearch,
    //         dateScanSearch: dateScanSearch,
    //         dateDebut: dateDebut,
    //         dateFin: dateFin,
    //         download: download
    //     },
    //     type: 'POST',
    //
    //     success: function(){
    //
    //
    //         verrou_fenetre(false);
    //     }
    // });


}


/**
 * Mi-afficher ny Tree sy JqGrid raha Categorie no filtre
 * @param siteId
 * @param clientId
 * @param exercice
 * @param dossier
 */
function showConsultationCateogorie(clientId, siteId, exercice, dossier) {
    var lien = Routing.generate('consultation_piece_categorie_tree');
    $.ajax({
        datatype: 'json',
        url: lien,
        data: {clientId: clientId, siteId: siteId, exercice: exercice, dossierId: dossier},
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        async: true,
        dataType: 'html',
        success: function (data) {

            var clicke = false;

            $('#js_tree').jstree({
                // 'core': $.parseJSON(data),
                'core' : {
                    'check_callback' : true,
                    'data':  $.parseJSON(data)
                },
                'search': {'case_insensitive': true, 'show_only_matches': true},
                plugins: ['search']
            })
                // .bind("dblclick.jstree", function (event) {
                .bind("click.jstree", function (event) {

                    if(!clicke){
                        clicke=setTimeout(function(){
                            clicke=null;

                        },300);
                    }
                    else {
                        clearTimeout(clicke);
                        clicke=null;

                        var CurrentNode = $(this).jstree("get_selected");
                        var selectId = $('#' + CurrentNode).attr('id');
                        var exercice = $('#js_exercice').val();

                        beforeShowCategorieGrid(selectId, exercice, false);

                    }

                });
        }
    });


}

/**
 *
 * @param selectId
 * @param exercice
 * @param periodeSearch
 */
function beforeShowCategorieGrid(selectId,exercice,periodeSearch) {
    if (selectId != null) {
        var dcsss = selectId.split('cat');

        var dossierId = 0;
        var catId = -1;
        var scatId = -1;
        var sscatId = -1;
        if (dcsss.length > 1) {
            dossierId = dcsss[0];
            var csss = dcsss[1].split('sCat');

            if (csss.length > 1) {
                catId = csss[0];
                var css = csss[1].split('tCat');
                if (css.length > 1) {
                    scatId = css[0];
                    sscatId = css[1];
                }
                else if (css.length == 1) {
                    scatId = css[0];
                }
            }
            else if (csss.length == 1) {
                catId = csss[0];
            }

        }
        else if (dcsss.length == 1) {

            if (dcsss[0].split('encours').length > 1) {
                dossierId = dcsss[0].split('encours')[0];
                catId = -2;
            }
            else {
                dossierId = dcsss[0];
            }
        }

        $('#js_piece_liste').jqGrid('GridUnload');

        setTableauWidth(true);

        // catId = parseInt(catId);

        switch (catId) {
            //Client & fournisseur
            // case 9:
            // case 10:
            case 'CODE_CLIENT':
            case 'CODE_FRNS':
                showClientFournisseurGrid(0, 0, dossierId, catId, scatId, sscatId, exercice, 0, '', periodeSearch, 1, false, false);
                break;
            //Note de frais
            case 'CODE_NDF':
                showNoteFraisGrid(0, 0, dossierId, catId, scatId, sscatId, exercice, 0, '', periodeSearch, 1, false, false);
                break;

            //Banque
            case 'CODE_BANQUE':
                showBanqueGrid(0, 0, dossierId, catId, scatId, sscatId, exercice, 0, '', periodeSearch, 1, false, false);
                break;
            //Social & Fiscal
            case 'CODE_SOC' :
            case 'CODE_FISC' :
                showFiscalSocialGrid(0, 0, dossierId, catId, scatId, sscatId, exercice, 0, '', periodeSearch, 1, false, false);
                break;
            //Contrat courrier & Gestion & Juridique
            case 'CODE_COURRIER':
            case 'CODE_ETATS_COMPTABLE':
            case 'CODE_GESTION':
            case 'CODE_JURIDIQUE':
                showCEGJGrid(0, 0, dossierId, catId, scatId, sscatId, exercice, 0, '', periodeSearch, 1, false, false);
                break;

            default:
                showCommunGrid(0, 0, dossierId, catId, scatId, sscatId, exercice, 0, 0, 0, '', periodeSearch, 1, false, false);
                break;
        }

    }
}


/**
 * Mi-afficher ny Tree sy JqGrid raha Source no filtre
 * @param clientId
 * @param siteId
 * @param dossierId
 * @param exercice
 */
function showConsultationSource(clientId, siteId, dossierId, exercice){
    var lien = Routing.generate('consultation_piece_source_tree');
    $.ajax({
        datatype: 'json',
        url: lien,
        data: {clientId: clientId, siteId: siteId, dossierId: dossierId, exercice: exercice},
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        async: true,
        dataType: 'html',
        success: function (data) {

            var clicke = false;

            $('#js_tree').jstree({
                'core': $.parseJSON(data),
                'search': {'case_insensitive': true, 'show_only_matches': true},
                plugins: ['search']
            })
                // .bind("dblclick.jstree", function (event) {
                .bind("click.jstree", function(event) {


                    if(!clicke){
                        clicke=setTimeout(function(){
                            clicke=null;

                        },300);
                    }
                    else {
                        clearTimeout(clicke);
                        clicke=null;

                        var CurrentNode = $(this).jstree("get_selected");
                        var selectId = $('#' + CurrentNode).attr('id');
                        var exercice = $('#js_exercice').val();

                        beforeShowSourceGrid(selectId,exercice,false);

                    }


                });
        }
    });
}


/**
 *
 * @param selectedId
 * @param exercice
 * @param periodeSearch
 */
function beforeShowSourceGrid(selectedId,exercice, periodeSearch) {
    if (selectedId != null) {
        var dt = selectedId.split('tiers');

        // var dossierId = 0;
        // var tiersId = -1;
        //
        // if (dt.length > 1) {
        //     dossierId = dt[0];
        //     tiersId = dt[1];
        // }
        //
        // else if (dt.length == 1) {
        //     dossierId = dt[0];
        // }

        // $('#js_piece_liste').jqGrid('GridUnload');

        // showCommunGrid(0, 0, dossierId, 0, 0, 0, exercice, tiersId, 0, 0, '', periodeSearch,2, false);
    }
}


/**
 * Mi-afficher ny Tree sy JqGrid raha Tiers no filtre
 * @param clientId
 * @param siteId
 * @param dossierId
 * @param exercice
 */
function showConsultationTiers(clientId, siteId, dossierId, exercice){
    var lien = Routing.generate('consultation_piece_tiers_tree');
    $.ajax({
        datatype: 'json',
        url: lien,
        data: {clientId: clientId, siteId: siteId, dossierId: dossierId, exercice: exercice},
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        async: true,
        dataType: 'html',
        success: function (data) {

            var clicke = false;

            $('#js_tree').jstree({
                'core': $.parseJSON(data),
                'search': {'case_insensitive': true, 'show_only_matches': true},
                plugins: ['search']
            })
                // .bind("dblclick.jstree", function (event) {
                .bind("click.jstree", function (event){

                    if(!clicke){
                        clicke=setTimeout(function(){
                            clicke=null;

                        },300);
                    }
                    else {
                        clearTimeout(clicke);
                        clicke = null;

                        var CurrentNode = $(this).jstree("get_selected");
                        var selectId = $('#' + CurrentNode).attr('id');
                        var exercice = $('#js_exercice').val();

                        beforeShowTiersGrid(selectId, exercice, false);
                    }



                });
        }
    });
}

/**
 *
 * @param selectedId
 * @param exercice
 * @param periodeSearch
 */
function beforeShowTiersGrid(selectedId,exercice, periodeSearch) {
    if (selectedId != null) {
        var dt = selectedId.split('tiers');

        var dossierId = 0;
        var tiersId = -1;

        if (dt.length > 1) {
            dossierId = dt[0];
            tiersId = dt[1];
        }

        else if (dt.length == 1) {
            dossierId = dt[0];
        }

        $('#js_piece_liste').jqGrid('GridUnload');

        showCommunGrid(0, 0, dossierId, 0, 0, 0, exercice, tiersId, 0, 0, '', periodeSearch,2, false, false);
    }
}

/**
 * Mi-afficher ny Tree sy JqGrid raha utilisateur no filtre
 * @param clientId
 * @param siteId
 * @param exercice
 */
function showConsultationUtiliasteur(clientId, siteId, exercice) {
    var lien = Routing.generate('consultation_piece_utilisateur_tree');
    $.ajax({
        datatype: 'json',
        url: lien,
        data: {clientId: clientId, siteId: siteId, exercice: exercice},
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        async: true,
        dataType: 'html',
        success: function (data) {

            var clicke = false;

            $('#js_tree').jstree({
                'core': $.parseJSON(data),
                'search': {'case_insensitive': true, 'show_only_matches': true},
                plugins: ['search']
            })
            // .bind("dblclick.jstree", function (event) {
                .bind("click.jstree", function (event) {

                    if (!clicke) {
                        clicke = setTimeout(function () {
                            clicke = null;

                        }, 300);
                    }
                    else {
                        clearTimeout(clicke);
                        clicke = null;
                        var CurrentNode = $(this).jstree("get_selected");
                        var selectId = $('#' + CurrentNode).attr('id');
                        var exercice = $('#js_exercice').val();
                        beforeShowUtilisateurGrid(selectId, exercice, false);
                    }
                });
        }
    });
}

/**
 *
 * @param selectedId
 * @param exercice
 * @param periodeSearch
 */
function beforeShowUtilisateurGrid(selectedId,exercice, periodeSearch) {
    if (selectedId != null) {
        var ucat = selectedId.split('cat');

        var dossierId = -1;
        var categorieId = -1;
        var utilisateurId = 0;

        if (ucat.length > 1) {
            utilisateurId = ucat[0];
            var catDoss = ucat[1].split('doss');
            if (catDoss.length > 1) {
                categorieId = catDoss[0];
                dossierId = catDoss[1];
            } else {
                categorieId = catDoss[0];
            }
        }

        else if (ucat.length == 1) {

            var encours = selectedId.split('encours');

            if (encours.length > 1) {
                utilisateurId = encours[0];
                categorieId = -2;

                if (encours[1] != '') {
                    dossierId = encours[1];
                }
            }
            else {
                utilisateurId = selectedId;
            }
        }

        $('#js_piece_liste').jqGrid('GridUnload');

        var catId = categorieId;
        switch (catId) {
            //Client & Fournisseur
            case 'CODE_CLIENT':
            case 'CODE_FRNS':
                showClientFournisseurGrid(0, 0, dossierId, categorieId, -1, -1, exercice, utilisateurId, '', periodeSearch, 4, false, false);
                break;

            //Note de frais
            case 'CODE_NDF':
                showNoteFraisGrid(0, 0, dossierId, catId, -1, -1, exercice, utilisateurId, '', periodeSearch, 4, false, false);
                break;

            //Banque
            case 'CODE_BANQUE':
                showBanqueGrid(0, 0, dossierId, catId, -1, -1, exercice, utilisateurId, '', periodeSearch, 4, false, false);
                break;

            //Fiscal & Social
            case 'CODE_SOC':
            case 'CODE_FISC':
                showFiscalSocialGrid(0, 0, dossierId, categorieId, -1, -1, exercice, utilisateurId, '', periodeSearch, 4, false, false);
                break;

            //Contrat courrier & Gestion & Juridique
            case 'CODE_COURRIER':
            case 'CODE_ETATS_COMPTABLE':
            case 'CODE_GESTION':
            case 'CODE_JURIDIQUE':
                showCEGJGrid(0, 0, dossierId, catId, -1, -1, exercice, utilisateurId, '', periodeSearch, 4, false, false);
                break;

            default:
                showCommunGrid(0, 0, dossierId, -1, 0, 0, exercice, 0, utilisateurId, 0, '', periodeSearch, 4, false, false);
                break;
        }
    }
}

/**
 * Initialisation Date
 */
function setDate() {
    $('.input-daterange').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        todayBtn: "linked",
        language: "fr",
        startView: 1
    });
}

function setResponsiveJqGrid(selecteur) {
    // Add responsive to jqGrid
    $(window).bind('resize', function () {
        var width = $('.jqGrid_wrapper').width();
        $('#'+selecteur).setGridWidth(width);

    });
}

/**
 * Initialisation Scroller
 */
function setScrollerHeight() {

    $("#js_scroller_parent").height( window.innerHeight - 130);
    $('#js_filtre_tree').height( window.innerHeight - 155);
}

function setTableauWidth(filtre) {

    var windowWitdh = $(window).width();

    if(windowWitdh>1200) {
        if (filtre == true) {
            // $(".tableau").css("width", $('#wrapper-content').width() - $('.filtre').width() - 60);
            // $("#js_piece_liste").jqGrid('setGridWidth', $('#wrapper-content').width() - $('.filtre').width() - 65);
            $(".tableau").css("width", $('#wrapper-content').width() - $('.filtre').width() - 30);
            $("#js_piece_liste").jqGrid('setGridWidth', $('#wrapper-content').width() - $('.filtre').width());
        }
        else {
            $(".tableau").css("width", $('#wrapper-content').width());
            $("#js_piece_liste").jqGrid('setGridWidth', $('#wrapper-content').width());
        }
    }

}

function setTreeHeight() {
    var windowWitdh = $(window).width();

    if (windowWitdh > 1200) {
        $('#js_filtre_tree').css('height', $('#js_scroller_parent').height() - 25);
    }
    else {
        $('#js_filtre_tree').css('height', ($('#js_scroller_parent').height() / 3));
    }
}

function startCategorieSearch() {
    var idSite = $('#site').val();
    var exercice = $('#js_exercice').val();
    var dossier = $('#dossier').val();
    var idClient = $('#client').val();

    if (exercice != '') {
        $("#js_tree").jstree("destroy");
        $('#js_piece_liste').jqGrid('GridUnload');

        showConsultationCateogorie(idClient, idSite, exercice, dossier);
    }
}

function startSourceSearch(){
    var idSite = $('#site').val();
    var exercice = $('#js_exercice').val();
    var dossier = $('#dossier').val();
    var idClient = $('#client').val();


    if (exercice != '') {
        $("#js_tree").jstree("destroy");
        $('#js_piece_liste').jqGrid('GridUnload');

        showConsultationSource(idClient,idSite, dossier, exercice);
    }
}

function startTiersSearch() {
    var idSite = $('#site').val();
    var exercice = $('#js_exercice').val();
    var dossier = $('#dossier').val();
    var idClient = $('#client').val();


    if (exercice != '') {
        $("#js_tree").jstree("destroy");
        $('#js_piece_liste').jqGrid('GridUnload');

        showConsultationTiers(idClient,idSite, dossier, exercice);
    }
}

function startUtilisateurSearch() {
    var idSite = $('#site').val();
    var exercice = $('#js_exercice').val();
    var idClient = $('#client').val();

    if (idSite != 0 && exercice != '') {
        $("#js_tree").jstree("destroy");
        showConsultationUtiliasteur(idClient, idSite, exercice)
    }
}

function  startDateSearch(download, datatype) {
    var idSite = $('#site').val(),
        idClient = $('#client').val(),
        exercice = $('#js_exercice').val(),
        categoriedate = $('#js_categorie_date'),
        parentDateScan = $('#js_filtre_date_scan').closest('.col-sm-12');


    if(!parentDateScan.hasClass('hidden')){
        parentDateScan.addClass('hidden');
    }

    if(datatype !== undefined){
        if(parseInt(datatype) === 1){
            parentDateScan.removeClass('hidden');
        }
    }


    if(!categoriedate.is(':disabled')){
        categoriedate.attr('disabled', true)
    }


    $('#filtre-date-modal').modal('show');

    $.ajax({
        data: {
            clientId: idClient,
            siteId: idSite,
            exercice: exercice
        },

        url: Routing.generate('consultation_piece_recherche_date'),
        type: 'POST',
        async: true,
        dataType: 'html',
        success: function (data) {

            var res = JSON.parse(data);

            var dossierOpts = $('#js_dossier_date').children();
            dossierOpts.remove();

            if(download != true) {
                dossierOpts.end().append('<option value="">Tous</option>');
            }

            var dossiers = res.dossiers;

            $.each(dossiers, function (k, v) {
                $('<option>').val(v.id).text(v.nom_dossier).appendTo('#js_dossier_date');
            });

            $('#js_categorie_date').children().remove().end().append('<option value="">Tous</option>');

            var categories = res.categories;

            $.each(categories, function (k, v) {
                // $('<option data-id="'+v.code+'">').val(v.id).text(v.libelle).appendTo('#js_categorie_date');
                $('<option>').val(v.id).text(v.libelle).appendTo('#js_categorie_date');
            });

            if(download == true) {
                $('#btn-recherche-date').attr('download', '1');
            }
            else{
                $('#btn-recherche-date').attr('download', '0');
            }

            if(datatype !== undefined) {
                $('#js_filtre_d').val(datatype);

                if(parseInt(datatype) === 1){
                    initDateScans();
                }
            }
        }

    });
}

function startSearch(typeSearch){
    switch (typeSearch){
        case '1':
            startCategorieSearch();
            break;

        case '2':
            startTiersSearch();
            break;

        case '4':
            startUtilisateurSearch();
            break;
        default:
            break;
    }
}

function initDateScans(){
    var dossier = $('#js_dossier_date').val(),
        site = $('#site').val(),
        client = $('#client').val(),
        exercice = $('#js_filtre_exercice').val();

    $.ajax({
        url: Routing.generate('consultation_piece_date_scan_list'),
        type: 'GET',
        data: {
            dossier: dossier,
            site: site,
            client: client,
            exercice: exercice
        },
        success: function (data) {
            var filtreDateScan = $('#js_filtre_date_scan');
            filtreDateScan.html(data);
        }
    });
}