function initCEGJForm(id){

    var ariaLabelledby = 'ui-id-'+id;
    var current = $('.ui-dialog[aria-labelledby="'+ariaLabelledby+'"]');
    current.find('.save-cegj').attr('id', 'save-cegj-'+id);
    current.find('.save-commentaire').attr('id', 'save-commentaire-'+id);

    current.find('.add-row').attr('id', 'add-row-'+id);
    current.find('.table-bordered').attr('id', 'ecriture-'+id);

    $(document).on('change', '.categorie', function () {
        initSousCategorie($(this));
    });

    $(document).on('change', '.souscategorie', function(){
        initSousSousCategorie($(this));
    });

    $(document).on('click', '.edit-commentaire', function(){
       editCommentaire($(this));
    });

    $(document).on('click', '#save-cegj-'+id, function () {
       saveCegj($(this));
    });


    $(document).on('click', '#add-row-'+id, function (){
       var tab = $('#ecriture-'+id);

        var image_id = $(this).closest('.data-image').find('.image_id').val();

       $.ajax({
          url: Routing.generate('consultation_piece_ecriture_cegj'),
          type: 'POST',
          data: {imageId: image_id },
          success: function(data){
              tab.find('tbody').append(data);
          }
       });
    });

}

function initSousCategorie(categorieCombo) {

    var form = categorieCombo.closest('.form-horizontal');

    var categorieId = form.find('.categorie').val();
    var souscategorieId = form.find('.souscategorie-id').val();

    $.ajax({
        url: Routing.generate('consultation_piece_souscategorie_init'),
        data: {categorieId: categorieId, souscategorieId: souscategorieId},
        type: 'POST',
        dataType: 'html',
        success: function (data) {
            form.find('.souscategorie').html(data);
            form.find('.soussouscategorie').html('<option value="-1"></option>');
        }
    });
}

function initSousSousCategorie(sousCategorieCombo) {


    var form = sousCategorieCombo.closest('.form-horizontal');
    var souscategorieId = form.find('.souscategorie').val();
    var soussouscategorieId = form.find('.soussouscategorie-id').val();


    $.ajax({
        url: Routing.generate('consultation_piece_soussouscategorie_init'),
        data: {souscategorieId: souscategorieId, soussouscategorieId: soussouscategorieId},
        type: 'POST',
        dataType: 'html',
        success: function (data) {

            form.find('.soussouscategorie').html(data);

        }
    });
}

function editCommentaire(editIcon){
    var form = editIcon.closest('.form-horizontal');
    setSummerNote(form.find('.commentaire'));
    form.find('.edit-commentaire').addClass('hidden');
    form.find('.save-commentaire').removeClass('hidden');

}

function saveCegj(saveBtn){

    var form = $(saveBtn).closest('.form-horizontal');

    var souscategorieVal = form.find('.souscategorie').val();
    var categorieVal = form.find('.categorie').val();

    if((souscategorieVal == '' || souscategorieVal == '-1') && categorieVal != '41'){
        show_info('Attention', 'La sous categorie ne peut être vide', 'warning');
        return false;
    }

    $.ajax({
        url: Routing.generate('consultation_piece_cegj_save'),
        type: 'POST',
        data: form.serialize(),
        success: function(response) {

            if (response['action'] === 'add') {
                if (response['type'] === 'success')
                    show_info('Succès', 'Ajout effectué', response['type']);
                else if (response['type'] === 'error')
                    show_info('Erreur', 'Ajout non effectué', response['type']);
            }

            else if (response['action'] === 'edit') {
                if (response['type'] === 'success')
                    show_info('Succès', 'Modification sauvegardée', response['type']);
                else if (response['type'] === 'error')
                    show_info('Erreur', 'Modification non sauvegardée', response['type']);
            }

            var pieceTable = $('#js_piece_liste');

            if(pieceTable.length > 0){
                var tr = $('#'+response['id']);

                if(response['sameCat']) {
                    if (tr.find('.js-piece-ec-1').length > 0) {
                        tr.find('.js-piece-sscategorie').html(response['sCatLib']);
                        tr.find('.js-piece-ssscategorie').html(response['ssCatLib']);
                        tr.find('.js-piece-description').html(response['description']);
                        tr.find('.js-piece-ec-1').html(response['ec1']);
                        tr.find('.js-piece-ec-2').html(response['ec2']);
                        tr.find('.js-piece-date-piece').html(response['datePiece']);
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

    });


}
