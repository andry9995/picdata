$(document).ready(function () {
    charge_list_cfonb();
    charge_replace_cfonb();

    $(document).on('change', '.checkbox-select', function() {
        var checkbox = $(this);
        var state = checkbox.prop('checked');
        checkbox.closest('.t-activation')
            .find('.checkbox-select')
            .prop('checked', state);
        /*if(state === true) {
            checkbox.closest('.t-activation')
                .parent()
                .find('.t-regle')
                .find('.fa-eyedropper')
                .removeAttr('style');
        }else{
            checkbox.closest('.t-activation')
                .parent()
                .find('.t-regle')
                .find('.fa-eyedropper')
                .attr('style','cursor:no-drop; color:#c0c0c0; background-color: #ffffff;');
        }*/

        var id = checkbox.closest('.t-activation')
                    .parent()
                    .attr('id');

        $.ajax({
            url: Routing.generate('banque_cfonb_activation'),
            type: 'POST',
            data: {
                id : id,
                state : (state) ? 1 : 0
            },
            success: function (data) {
                if (data === 'SUCCESS') {
                    show_info("", "Modification enregistrée avec succès.", "success");
                    charge_list_cfonb();
                } else {
                    show_info("", "Une erreur est survenue pendant l'activation", "error");
                }
            }
        });
    });

    $(document).on('click', '.t-regle', function() {
        /*var state_activation = $(this).parent().find('.t-activation').find('.checkbox-select')[0].value;
        if(state_activation === '0'){
            return;
        }*/
        var id = $(this).parent().attr('id');
        $.ajax({
            data: {
                id: id
            },
            type: 'POST',
            url: Routing.generate('banque_cfonb_regle'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                show_modal(data,'Edit Règle');
            }
        });
    });

    $(document).on('click', '.save_regle', function(){
        var debut = $('#id_debut').val();
        var fin = 0;
        var longueur = $('#id_long').val();
        var cfonbId = $(this).attr('data-id');
        if(longueur === '' && (debut === '' || fin === '')){
            show_info('ATTENTION', 'Il y a des champs obligatoires non renseignés','error');
            return false;
        }
        $.ajax({
            url: Routing.generate('banque_cfonb_add_regle'),
            type: 'POST',
            data: {
                debut: debut,
                fin: fin,
                longueur: longueur,
                cfonbId: cfonbId
            },
            success: function (data) {
                if (data === 'SUCCESS') {
                    show_info("", "Nouvelle règle enregistrée.", "success");
                    close_modal();
                } else {
                    show_info("", "Une erreur est survenue pendant l'enregistrement", "error");
                }
            }
        });
    });

    $(document).on('click', '.js-remove-replace', function () {
        var id = $(this).closest('tr').attr('id'),
            animated = 'bounceInRight',
            titre = '<i class="fa fa-remove-circle"></i> <span>Confirmation suppression</span>';
        $('.js_remove_filtre_cfonb_select').attr('data-id',id);
        show_modal($('#js_hidden_remove_filtre_cfonb').html(), titre, animated);
    });

    $(document).on('click', '.js_remove_filtre_cfonb_select', function () {
        var cfonbReplaceGrid = $('#js_gestion_replace_liste');
        var rowKey = cfonbReplaceGrid.jqGrid('getGridParam',"selrow");
        $.ajax({
            url: Routing.generate('banque_cfonb_remove_replace', {id: rowKey}),
            type: 'GET',
            async: true,
            success: function (data) {
                if (data === 'SUCCESS') {
                    charge_replace_cfonb();
                    show_info("", "Supprimée avec succès.", "success");
                    close_modal();
                } else {
                    show_info("", "Une erreur est survenue pendant la suppression", "error");
                }
            }
        });
    });

    $(document).on('click', '#btn-add-replace', function () {
        var animated = 'bounceInRight',
            titre = '<i class="fa fa-plus"></i> <span>Nouveau filtre</span>';
        $('.js_add_filtre_cfonb_select').attr('data-id', '');
        show_modal($('#js_hidden_add_filtre_cfonb').html(), titre, animated);
    });

    $(document).on('click', '.js_add_filtre_cfonb_select', function () {
        var input_mot = $('#modal-body .js_mot_cfonb'),
            input_replace = $('#modal-body .js_replace_cfonb'),
            mot = input_mot.val().trim(),
            replace = input_replace.val().trim();
        if(mot === '' && replace === ''){
            show_info('ATTENTION', 'Il y a des champs obligatoires non renseignés','error');
            return false;
        }
        $.ajax({
            url: Routing.generate('banque_cfonb_add_replace'),
            type: 'POST',
            data: {
                mot: mot,
                replace: replace,
                id: $('.js_add_filtre_cfonb_select').attr('data-id')
            },
            success: function (data) {
                if (data === 'SUCCESS') {
                    charge_replace_cfonb();
                    show_info("", "Nouveau filtre enregistrée.", "success");
                    close_modal();
                } else {
                    show_info("", "Une erreur est survenue pendant l'enregistrement", "error");
                }
            }
        });
    });

    $(document).on('click', '.js-save-modif-replace', function () {
        var  id = $(this).closest('tr').attr('id'),
            cfonbReplaceGrid = $('#js_gestion_replace_liste'),
            mot = cfonbReplaceGrid.jqGrid('getCell',id,'t-recherche'),
            replace = cfonbReplaceGrid.jqGrid('getCell',id,'t-remplace'),
            animated = 'bounceInRight',
            titre = '<span>Modification filtre</span>';
        $('.js_add_filtre_cfonb_select').attr('data-id', id);
        show_modal($('#js_hidden_add_filtre_cfonb').html(), titre, animated);
        setTimeout(function(){
            $('#modal-body .js_mot_cfonb').val(mot);
            $('#modal-body .js_replace_cfonb').val(replace);
        }, 200);
    });
});

function charge_list_cfonb() {

    var cfonbGrid = $('#js_gestion_cfonb_liste'),
        lastsel_cfonb,
        window_height = window.innerHeight,
        container_cfonb = $('.container-cfonb');
    cfonbGrid.jqGrid('GridUnload');
    var cfonbGrid = $('#js_gestion_cfonb_liste');

    container_cfonb.height(window_height - 100);

    cfonbGrid.jqGrid({
        url: Routing.generate('banque_cfonb_get_list'),
        mtype: 'POST',
        datatype: 'json',
        rownumbers: true,
        viewrecords: true,
        autowidth: true,
        hidegrid: false,
        shrinkToFit: true,
        loadonce: true,
        sortable: true,
        height: container_cfonb.height() - 100,
        rowList: [100, 200, 500],
        altRows: true,
        colNames: ['Code', 'Libelle', 'Activation'],
        colModel: [
            {
                name: 't-code',
                sortable: true,
                width: 80,
                align: 'left',
                classes: 't-code'
            },
            {
                name: 't-libelle',
                width: 400,
                sortable: true,
                align: 'left',
                classes: 't-libelle',
                fixed: true
            },
            {
                name: 't-activation',
                width: 80,
                sortable: true,
                align: 'center',
                classes: 't-activation',
                fixed: true
            }
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_cfonb) {
                cfonbGrid.restoreRow(lastsel_cfonb);
                lastsel_cfonb = id;
            }
            cfonbGrid.editRow(id, false);
        },
        ajaxRowOptions: {async: true}
    });
}

function charge_replace_cfonb() {
    var cfonbReplaceGrid = $('#js_gestion_replace_liste'),
        lastsel_cfonb_replace,
        window_height = window.innerHeight,
        container_cfonb_replace = $('.container-cfonb-replace');
    cfonbReplaceGrid.jqGrid('GridUnload');
    var cfonbReplaceGrid = $('#js_gestion_replace_liste');

    container_cfonb_replace.height(window_height - 100);

    cfonbReplaceGrid.jqGrid({
        url: Routing.generate('banque_cfonb_get_replace_list'),
        mtype: 'POST',
        datatype: 'json',
        rownumbers: true,
        viewrecords: true,
        autowidth: true,
        hidegrid: false,
        shrinkToFit: true,
        loadonce: true,
        sortable: true,
        height: container_cfonb_replace.height() - 100,
        rowList: [100, 200, 500],
        altRows: true,
        colNames: ['Recherche', 'Remplace', 'Action'],
        colModel: [
            {
                name: 't-recherche',
                sortable: true,
                width: 500,
                align: 'left',
                classes: 't-recherche'
            },
            {
                name: 't-remplace',
                width: 80,
                sortable: true,
                align: 'center',
                classes: 't-remplace',
                fixed: true
            },
            {
                name: 't-action',
                width: 80,
                sortable: true,
                align: 'center',
                classes: 't-action',
                fixed: true
            }
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_cfonb_replace) {
                cfonbReplaceGrid.restoreRow(lastsel_cfonb_replace);
                lastsel_cfonb_replace = id;
            }
            cfonbReplaceGrid.editRow(id, false);
        },
        ajaxRowOptions: {async: true}
    });
}
