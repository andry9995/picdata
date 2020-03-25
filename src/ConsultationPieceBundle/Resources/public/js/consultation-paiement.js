$(document).ready(function(){

    var canShowReglePaiement = 1;
    $(document).on('click', '#btn-regle-paiement', function(){

        if(canShowReglePaiement === 1) {

            var image_id = $(this).closest('.data-image').find('.image_id').val();

            $.ajax({
                url: Routing.generate('consultation_piece_regle_paiement'),
                type: 'POST',
                data: {
                    imageId: image_id
                },
                async: true,
                dataType: 'html',
                success: function (data) {
                    var options = {modal: false, resizable: true, title: 'Règle de paiement'};
                    modal_ui(options, data, undefined, 0.4, 0.3);

                    canShowReglePaiement = 0;
                }
            });
        }
        else{
            var div1 = $(this).closest('.data-image').parent().parent();

            var div2 = div1.prev();
            div2.css({'display':'none'});

            canShowReglePaiement = 1;
        }
    });

    $(document).on('click', '.btn-cp-echeance-save', function(){
        var form = $(this).closest('.form-horizontal');
        // var imageId = form.find('.js_cp_image_id').val();
        var imageId = form.find('.js_image_id').val();
        var echeance = form.find('.js_cp_date_echeance').val();

        var lien = Routing.generate('consultation_piece_echeance_edit');
        $.ajax({
            url: lien,
            type: 'POST',
            data: {
                imageId: imageId,
                echeance: echeance
            },
            success: function(data){
                if(data.type !== '' && data.message !== '') {
                    show_info('', data.message, data.type);
                }
                updateEcheance(imageId, form.find('.js_cp_date_echeance'));
            }
        })
    });

    $(document).on('click', '.btn-cp-save', function () {

        var form = $(this).closest('.form-horizontal');
        var lien = Routing.generate('consultation_piece_edit');
        // var imageId = $('.data-image').attr('data-id');
        var imageId = form.find('.js_cp_image_id').val();
        var periodeDu = form.find('.js_cp_periode_du').val();
        var periodeAu = form.find('.js_cp_periode_au').val();
        var dateLivraison =form.find('.js_cp_date_livraison').val();
        var modeReglement = form.find('.js_cp_mode_reglement').val();
        var dateReglement = form.find('.js_cp_date_reglement').val();
        var numReglement = form.find('.js_cp_num_reglement').val();
        var exercice = form.find('.js_cp_exercice').val();

        $.ajax({
            url: lien,
            type: 'POST',
            data: {
                imageId: imageId,
                periodeDu: periodeDu,
                periodeAu: periodeAu,
                dateLivraison: dateLivraison,
                modeReglement: modeReglement,
                dateReglement: dateReglement,
                numReglement: numReglement,
                exercice: exercice
            },
            success: function(data){
                if(data.type !== '' && data.message !== '')
                    show_info('', data.message, data.type);
            }
        });
    });

    $(document).on('change', '.js_banque', function(){
        var form = $(this).closest('.form-horizontal'),
            lien = Routing.generate('consultation_banque_compte'),
            imageId = form.find('.js_cp_image_id').val(),
            banqueId = $(this).val();
        $.ajax({
            url: lien,
            type: 'GET',
            data: {imageId: imageId, banqueId: banqueId},
            datatype: 'html',
            success: function (data) {
                form.find('.js_banque_compte').html(data);
            }
        })
    });

    $(document).on('click', '.btn-cp-save-banque', function () {
        var form = $(this).closest('.form-horizontal'),
            lien = Routing.generate('consultation_piece_banque_edit'),
            imageId = form.find('.js_cp_image_id').val(),
            periodeDu = form.find('.js_debut_banque').val(),
            periodeAu = form.find('.js_fin_banque').val(),
            exercice = form.find('.js_exercice').val(),
            numReleve = form.find('.js_num_releve').val(),
            page = form.find('.js_page').val(),
            banqueCompte = form.find('.js_banque_compte').val(),
            soldeInitial = form.find('.js_solde_initial').val(),
            soldeFinal = form.find('.js_solde_final').val(),
            sousssoucategorie = form.find('.soussouscategorie').val(),
            souscategorie = form.find('.souscategorie').val()
        ;

        $.ajax({
            url: lien,
            type: 'POST',
            data: {
                imageId: imageId,
                periodeDu: periodeDu,
                periodeAu: periodeAu,
                numReleve: numReleve,
                page: page,
                banqueCompte: banqueCompte,
                exercice: exercice,
                soldeInitial: soldeInitial,
                soldeFinal: soldeFinal,
                souscategorie: souscategorie,
                soussouscategorie: sousssoucategorie
            },
            success: function(data){
                if(data.type !== '' && data.message !== '')
                    show_info('', data.message, data.type);

                var jstree = $('#js_tree');

                if(jstree.length > 0) {
                    beforeShowCategorieGrid(data.id, exercice, false);

                    jstree.find('.jstree-clicked').removeClass('jstree-clicked');
                    jstree.find('#'+data.id+'_anchor').addClass('jstree-clicked');
                }
            }
        });



    });


    $(document).on('click', '.btn-modifier-regle-paiement', function(){

        var formulaire = $(this).closest('.form-horizontal');

        var dateLe = formulaire.find('.js_regle_paiement_date_le').val();
        var nbreJour = formulaire.find('.js_regle_paiement_nbre_jour').val();
        var typeDate = formulaire.find('.js_regle_paiement_date').val();
        var imageId = formulaire.find('.js_image_id').val();
        var tiersId = formulaire.find('.js_tiers_id').val();

        var lien = Routing.generate('info_perdos_regle_paiement_tiers_edit');
        $.ajax({

            data:{
                dateLe:dateLe,
                nbreJour:nbreJour,
                typeDate:typeDate,
                imageId:imageId,
                tiersId:tiersId
            },

            url: lien,
            type: 'POST',
            async: true,
            dataType: 'html',
            success: function(data){
                var res = parseInt(data);
                if(res === 2) {
                    show_info('SUCCES', "MODIFICATION DES 'REGLES DE PAIEMENTS' BIEN ENREGISTREE");
                }
                else if (res === 1)
                {
                    show_info('SUCCES', "AJOUT DES 'REGLES DE PAIEMENTS' EFFECTUEE");
                }
                canShowReglePaiement = 1;

                updateEcheance(imageId, formulaire.find('.js_cp_date_echeance'));
            }
        });
    });

    $(document).on('click', '.btn-modifier-client-fournisseur', function(event){
       event.preventDefault();
       event.stopPropagation();

       var form = $(this).closest('.form-horizontal'),
           categorieid = form.find('.categorie').val(),
           souscategorieid = form.find('.souscategorie').val(),
           soussouscategorieid = form.find('.soussouscategorie').val(),
           imageid = form.find('.js_image_id').val();

       $.ajax({
           url: Routing.generate('consultation_piece_save_categorie'),
           type: 'POST',
           data:{
               imageid: imageid,
               categorieid: categorieid,
               souscategorieid: souscategorieid,
               soussouscategorieid: soussouscategorieid
           },
           success: function(response){
               show_info('', response.message, response.type);

               if(response.type !== 'success')
                   return;

               var pieceTable = $('#js_piece_liste');

               if(pieceTable.length > 0){
                   var tr = $('#'+response['id']);

                   if(response['sameCat']) {
                       if (tr.find('.js-piece-ec-1').length > 0) {
                           tr.find('.js-piece-sscategorie').html(response['sCatLib']);
                           tr.find('.js-piece-ssscategorie').html(response['ssCatLib']);
                       }
                   }
                   else{
                       var jsTree = $('#js_tree');

                       if(jsTree.length > 0) {

                           var treeId = response['treeId'];
                           var treeDoss = treeId['dossierId'];

                           var treeCat = treeId['categorie'];
                           var tId = treeDoss + "cat" + treeCat;
                           var treeCatLib = response['catLib'];

                           var oldTreeId = response['oldTreeId'];
                           var oldTreeDoss = oldTreeId['dossierId'];
                           var oldTreeCat = oldTreeId['categorie'];

                           tr.remove();

                           var trs = pieceTable.find('tr');
                           //1: misy entete an'lay grid foana
                           if(trs.length === 1){
                               var childs = $('#'+oldTreeDoss+'cat'+oldTreeCat).find('.jstree-children');
                               //Jerena aloha raha mbola misy zanany ilay noeud ao @jsTress
                               if(childs.length === 0) {
                                   jsTree.jstree().delete_node("#" + oldTreeDoss + "cat" + oldTreeCat);
                               }
                               //Raha misy noeua hafa dio ilay selectionné iahny no fafana
                               else{
                                   // var selected = jsTree.jstree("get_selected").attr('id');
                                   // jsTree.jstree().delete_node('#'+selected);
                               }
                           }


                           if ($('#' + tId).length === 0) {
                               jsTree.jstree().create_node("#"+treeDoss, {
                                   "id": tId,
                                   "text": treeCatLib
                               }, "last", function () {
                               });
                           }
                       }
                   }
               }





           }

       })

    });


    $(document).on('click', '.js_regle_paiement_date_le_active', function(){

        var form = $(this).closest('.form-horizontal');
        var dateLe = form.find('.js_regle_paiement_date_le');

        if ($(this).is(":checked")) {
           dateLe.removeAttr('disabled');
        }
        else {
            dateLe.prop('disabled', true);
            dateLe.val("");
        }
    });
});


function updateEcheance(imageId, input){
    var lien = Routing.generate('consultation_piece_echeance_calcul');
    $.ajax({
        url: lien,
        type: 'POST',
        data: { imageId: imageId },
        success: function(data) {
            // input.html(data);
            if (data.length === 3) {
                input.datepicker({
                    keyboardNavigation: false,
                    forceParse: false,
                    autoclose: true,
                    todayBtn: "linked",
                    language: "fr"
                }).datepicker("setDate", new Date(data[0], parseInt(data[1]) -1 , data[2]));
            }
        }
    });
}