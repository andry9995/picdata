$(document).ready(function () {
    var notificationGrid = $('#js_gestion_notification_liste'),
        lastsel_notif,
        window_height = window.innerHeight,
        container_notification = $('.container-notification'),
        mydata = [];

    container_notification.height(window_height - 240);
    charger_site();

    notificationGrid.jqGrid({
        data: mydata,
        datatype: "local",
        rownumbers: false,
        viewrecords: true,
        autowidth: true,
        hidegrid: false,
        shrinkToFit: true,
        loadonce: true,
        sortable: true,
        height: container_notification.height() - 100,
        rowList: [100, 200, 500],
        altRows: true,
        caption: "LISTE NOTIFICATIONS",
        editurl: Routing.generate('notification_edit'),
        pager: '#js_gestion_notification_liste_pager',
        colNames: ['Libelle', 'Code', 'Email', 'Responsable', '<span class="fa fa-bookmark-o" style="display:inline-block"/>'],
        colModel: [
            {name: 't-libelle', editable: false, sortable: true, width: 150, align: 'left', classes: 't-libelle'},
            {name: 't-code', editable: true, sortable: true, width: 200, edittype:"select", fixed: true, align: 'center', editoptions: { dataUrl: Routing.generate('notification_code_titre', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                }, classes: 't-code'},
            {name: 't-email', editable: true, width: 200, sortable: true, align: 'left', classes: 't-email', editrules: { custom: true, custom_func: verifier_mail_jqgrid }},
            {name: 't-responsable', editable: true, sortable: true, width: 200, edittype:"select", align: 'center', editoptions: { dataUrl: Routing.generate('notification_responsable_titre', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },classes: 't-responsable'},
            {name: 't-actions', title: false, sortable: false, width: 100, align: 'center', classes: 't-actions', editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-modif-notification" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-notification" title="Supprimer"></i>'}}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_notif) {
                notificationGrid.restoreRow(lastsel_notif);
                lastsel_notif = id;
            }
            notificationGrid.editRow(id, false);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);
            return !item_action;
        },
        loadComplete: function () {
            if ($("#btn-add-notification").length == 0) {
                notificationGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-notification" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }
        },
        ajaxRowOptions: {async: true}
    });

    $(document).on('click', '#btn-add-notification', function () {
        if($('#dossier option:selected').text().trim() !== '') {
            $.ajax({
                type: 'GET',
                url: Routing.generate('notification_show_add_notif_form'),
                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                beforeSend: function (jqXHR) {
                    jqXHR.overrideMimeType('text/html;charset=utf-8');
                },
                dataType: 'html',
                success: function (data) {
                    var animated = 'bounceInRight',
                        titre = '<i class="fa fa-plus-circle"></i> <span>Nouvelle notification</span>';
                    show_modal(data, titre, animated, 'modal-lg');
                }
            });
        }else{
            show_info('Erreur','Choisir un dossier','error');
        }
    });

    $(document).on('click', '.js_btn_cancel', function () {
        close_modal();
    });

    $(document).on('change', '#notification-code', function () {
        if($('#notification-code option:selected').text().trim() !== ''){
            $('#notification-libelle').val($('#notification-code option:selected').attr('data-libelle'));
        }else{
            $('#notification-libelle').val('');
        }
    });

    $(document).on('change', '#dossier', function () {
        if($('#dossier option:selected').text().trim() !== ''){
            show_list_notification();
        }else{
            show_info('Erreur','Choisir un dossier','error');
        }
    });

    $(document).on('click', '.js_btn_save_notification', function () {
        if($('#dossier option:selected').text().trim() !== '') {
            var notification = $('#notification-code').val(),
                libelle = $('#notification-libelle').val(),
                email = $('#notification-email').val(),
                responsable = $('#notification-responsable').val(),
                url = Routing.generate('notification_add');
            if(notification !== '' && libelle !== '' && email !== '' && responsable !== ''){
                if(verifier_mail(email)){
                    $.ajax({
                        url:url,
                        type: "POST",
                        dataType: "json",
                        data: {
                            dossier: $('#dossier').val(),
                            client: $('#client').val(),
                            notification: notification,
                            email: email,
                            responsable: responsable,
                            isAdd: 1
                        },
                        async: true,
                        success: function (data) {
                            if (data === 'SUCCES') {
                                show_info('SUCCES', 'Nouvelle notification bien enregistrée');
                                close_modal();
                                show_list_notification()
                            } else if (data === 'MAIL_EXIST') {
                                show_info('ERREUR', 'L\'addresse email existe déjà', 'error');
                            } else {
                                show_info('ERREUR', 'Une erreur est survenue pendant l\'enregistrement', 'error');
                            }
                        }
                    });
                }
            }else{
                show_info('ATTENTION', 'Il y a des champs obligatoires non renseignés','error');
            }
        }else{
            show_info('Erreur','Choisir un dossier','error');
        }
    });
    $(document).on('click', '.js-remove-notification', function() {
        var id = $(this).closest('tr').attr('id');
            animated = 'bounceInRight',
            titre = '<i class="fa fa-remove-circle"></i> <span>Confirmation suppression</span>';
        $('.js_remove_notification_select').attr('data-id',id);
        show_modal($('#js_hidden_remove_notification').html(), titre, animated);
    });

    $(document).on('click', '.js_remove_notification_select', function(){
        var notification_id = $(this).attr('data-id'),
            url = Routing.generate('notification_delete', {id: notification_id});
        $.ajax({
            url:url,
            type: "GET",
            async: true,
            success: function (data)
            {
                if(data === 'SUCCES'){
                    show_info('SUCCES', 'Suppression notification avec succès');
                }else{
                    show_info('ERREUR', 'Une erreur est survenue pendant la suppression', 'error');
                }
                close_modal();
                show_list_notification();
            }
        });
    });

    $(document).on('change', '.t-code', function() {
        var id = notificationGrid.getGridParam('selrow');
        if($('.t-code option:selected').text().trim() !== ''){
            $('#js_gestion_notification_liste').find('tr[id="' +id+ '"]').find('.t-libelle').html($('.t-code option:selected').attr('data-libelle'));
        }else{
            $('#js_gestion_notification_liste').find('tr[id="' +id+ '"]').find('.t-libelle').html('');
        }
    });

    $(document).on('click', '.js-save-modif-notification', function (event) {
        event.preventDefault();
        event.stopPropagation();
        notificationGrid.jqGrid('saveRow', lastsel_notif, {
            "aftersavefunc": function () {
                show_info('SUCCES', 'Modification notification avec succès');
                show_list_notification();
            }
        });
    });
});

function show_list_notification(){
    var notificationGrid = $('#js_gestion_notification_liste'),
        notification = $('#notification-code').val();
    notificationGrid.jqGrid("clearGridData");
    notificationGrid.jqGrid('setGridParam', {
        url: Routing.generate('notification_get_liste'),
        postData: { dossier: $('#dossier').val(), client: $('#client').val(), notification: notification },
        mtype: 'POST',
        datatype: 'json'
    })
        .trigger('reloadGrid', {fromServer: true, page: 1});
}

function verifier_mail(emailAddress) {
    var message = "";
    if (isValidEmailAddress(emailAddress))
        return true;
    else {
       message = emailAddress + " est un mail invalide";
    }

    show_info('INFORMATION', message, 'warning');
    return false;
}

function verifier_mail_jqgrid(posdata, colName) {
    var message = "";
    if (posdata != '' && isValidEmailAddress(posdata))
        return [true, ""];
    else {
        if (posdata == '')
            message = "Le champ " + colName + " est obligatoire";
        else{
            message = posdata + " est un mail invalide";
        }
    }

    setTimeout(function () {
        $("#info_dialog").hide();
    }, 10);

    show_info('INFORMATION', message, 'warning');
    return [false, ""];
}

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}

function after_charged_dossier_not_select() {
    if ($('#dossier option:selected').text().trim() === '')
    {
        $('#dossier option:selected').text('Tous')
    }
}