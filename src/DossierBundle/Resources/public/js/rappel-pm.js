var window_height = window.innerHeight,
        rappel_pm_client = $('#client-config'),
        rappel_pm_site = $('#site-config'),
        tableau_rappel_pm = $('#table_config_4'),
        email_content = $('#email-content-pm'),
        activer_envoi = $('#activer-envoi'),
        last_table_select,
        now = new Date(),
        rappel_pm_grid_height = $(window).height() - 80,
        index_modal_ui_mail = 0;
$(function() {
    var changeCount = 0;
    HTMLTextAreaElement.prototype.insertAtCaret = function (text) {
        text = text || '';
        if (document.selection) {
            // IE
            this.focus();
            var sel = document.selection.createRange();
            sel.text = text;
        } else if (this.selectionStart || this.selectionStart === 0) {
            // Others
            var startPos = this.selectionStart;
            var endPos = this.selectionEnd;
            this.value = this.value.substring(0, startPos) +
                text +
                this.value.substring(endPos, this.value.length);
            this.selectionStart = startPos + text.length;
            this.selectionEnd = startPos + text.length;
        } else {
            this.value += text;
        }
    };

    /** IGNORER FORM SUBMIT */
    $('form').on('submit', function(event) {
        event.preventDefault();
        return false;
    });

    setTimeout(function() {
        updateGridSizeRappelPm();
    }, 500);

    /** SAVE MODIF PARAM */
    $(document).on('click', '.js-save-rappel-image', function(event) {
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        saveRowEdited(rowid);
    });

    tableau_rappel_pm.on('change', 'input, select', function(event) {
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        saveRowEdited(rowid);
    });

    /** MODIF DESTINATAIRES */
    $(document).on('click', '.js-rappel-pm-dest', function(event) {
        event.preventDefault();
        var rowId = $(this).closest('tr').attr('id'),
            nom_dossier = $(this).closest('tr')
                .find('.js-rappel-pm-dossier')
                .text(),
            nom = $(this).closest('tr')
                .find('.js-rappel-pm-nom')
                .text(),
            titre = $(this).closest('tr')
                .find('.js-rappel-pm-titre')
                .text(),
            destinataire = $(this).closest('tr')
                .find('.js-rappel-pm-dest')
                .text(),
            copie = $(this).closest('tr')
                .find('.js-rappel-pm-copie')
                .text();
        $('#notification-pm-dest-id').val(rowId);
        $('#titre-contact-pm').val(titre);
        $('#nom-contact-pm').val(nom);
        $('#list-destinataire-pm').val(destinataire);
        $('#list-copie-pm').val(copie);
        $('#rappel-pm-dest-modal-title').text(nom_dossier + " - Destinataires");
        $('#rappel-pm-dest-modal').modal('show');
    });

    $('#btn-save-rappel-pm-destinataire').on('click', function(event) {
        event.preventDefault();
        var btn = $(this),
            btn_normal = 'Enregistrer',
            btn_save_running = '<i class="fa fa-spinner fa-pulse fa-fw"></i> Enregistrer',
            titre = $('#titre-contact-pm').val(),
            nom = $('#nom-contact-pm').val().trim(),
            destinataire = $('#list-destinataire-pm').val().trim(),
            copie = $('#list-copie-pm').val().trim(),
            notification = $('#notification-pm-dest-id').val();

        if (nom !== '' && destinataire !== '') {
            var url = Routing.generate('dossier_admin_rappel_pm_destinataire_edit', {notification: notification}),
                formData = new FormData();
            formData.append('titre', titre);
            formData.append('nom', nom);
            formData.append('destinataire', destinataire);
            formData.append('copie', copie);
            btn.html(btn_save_running);
            fetch(url, {
                method: 'POST',
                credentials: 'include',
                body: formData
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                console.log(data);
                $('#rappel-pm-dest-modal').modal('hide');
                btn.html(btn_normal);
                reloadGridRappelPm();
            }).catch(function (error) {
                show_info('Erreur', 'Une erreur est survenue.', 'error');
                btn.html(btn_normal);
                console.log(error);
            });
        } else {
            show_info('Erreur', 'Le nom du contact et le mail de(s) destinataire(s) ne doivent pas être vides.', 'error');
        }
    });

    /** MODIF CONTENU */
    $(document).on('click', '.js-rappel-pm-contenu', function(event) {
        event.preventDefault();
        var rowId = $(this).closest('tr').attr('id'),
            nom_dossier = $(this).closest('tr')
                .find('.js-rappel-pm-dossier')
                .text(),
            contenu = $(this).closest('tr')
                .find('.js-rappel-pm-contenu-full')
                .text(),
            objet  = $(this).closest('tr')
                .find('.js-rappel-pm-objet')
                .text();

        $('#rappel-pm-contenu-modal-title').text(nom_dossier + " - Contenu du mail");
        $('#notification-pm-contenu-id').val(rowId);
        $('#check-content-pm-all').prop('checked', false);
        $('#email-content-pm').val(contenu);
        $('#rappel-pm-contenu-modal').modal('show');
        $('#notification-pm-objet').val(objet);

    });

    /** SAVE MAIL CONTENU */
    $('#btn-save-rappel-pm-contenu').on('click', function(event) {
        event.preventDefault();
        var btn_save = $(this),
            btn_normal = 'Enregistrer',
            btn_save_running = '<i class="fa fa-spinner fa-pulse fa-fw"></i> Enregistrer',
            contenu = email_content.val(),
            client = $('#client-config').val(),
            site = $('#site-config').val(),
            notification = $('#notification-pm-contenu-id').val(),
            objet = $('#notification-pm-objet').val(),
            tous = $('#check-content-pm-all').prop('checked') ? 1 : 0;
        var url = Routing.generate('dossier_admin_rappel_pm_email_content_edit', { tous: tous });
        var formData = new FormData();
        formData.append('contenu', contenu);
        formData.append('client', client);
        formData.append('site', site);
        formData.append('notification', notification);
        formData.append('tous', tous);
        formData.append('objet', objet);

        btn_save.html(btn_save_running);
        fetch(url, {
            method: 'POST',
            credentials: 'include',
            body: formData
        }).then(function(response) {
            return response.json();
        }).then(function(data) {
            btn_save.html(btn_normal);
            $('#rappel-pm-contenu-modal').modal('hide');
            reloadGridRappelPm();
            show_info('', 'Contenu enregistré', 'success', 200);
        }).catch(function(error) {
            btn_save.html(btn_normal);
            show_info('', 'Une erreur est survenue. Merci de réessayer.', 'error');
        });
    });

    /** DEFAULT CONTENT */
    $('#default-content-pm').on('click', function() {
       var btn = $(this),
           btn_normal = '<i class="fa fa-file-text-o"></i> Utiliser le contenu par défaut',
           btn_load_running = '<i class="fa fa-spinner fa-pulse fa-fw"></i> Utiliser le contenu par défaut',
           editor = $('#email-content-pm'),
           url = Routing.generate('dossier_admin_rappel_pm_default_content');
       btn.html(btn_load_running);
       fetch(url, {
           method: 'GET',
           credentials: 'include'
       }).then(function(response) {
           return response.text();
       }).then(function(data) {
           editor.val(data);
           btn.html(btn_normal);
       }).catch(function(error) {
           btn.html(btn_normal);
           console.log(error);
        });
    });

    /** INSERT BALISE IMAGE MANQUANTE DANS MAIL */
    $('#image-manquante').on('click', function() {
        var editor = document.getElementById('email-content-pm');
        editor.insertAtCaret("[[image]]");
    });

    /** INSERT BALISE IMAGE MANQUANTE DANS MAIL */
    $(document).on('click', '#frequence-envoi-pm', function() {
        var editor = document.getElementById('email-content-pm');
        editor.insertAtCaret("[[frequence]]");
    });

    /** UPDATE ENVOI N-1 ALL */
    $(document).on('change', '#envoi-n-1-all', function() {
        var field = 'EnvoiN1',
            value = $(this).prop('checked') ? 1 : 0;
        editParamAll(field, value);
    });

    /** UPDATE FREQUENCE ALL */
    $(document).on('change', '#rappel-frequence-all', function() {
        var field = 'Periode',
            value = $(this).val() !== '' ? $(this).val() : 'M';
        editParamAll(field, value);
    });

    /** UPDATE ENVOI N ALL */
    $(document).on('change', '#envoi-n-all', function() {
        var field = 'EnvoiN',
            value = $(this).prop('checked') ? 1 : 0;
        editParamAll(field, value);
    });

    $(document).on("click", ".jqgrid-tabs a", function () {
        updateGridSizeRappelPm();
    });

    $('#tab-param-rappel').on('click', function() {
        updateGridSizeRappelPm();
    });

    $(window).bind('resize',function () {
        updateGridSizeRappelPm();
    });

    $(document).on("click", ".class_action_pm", function () {
        var rowId = $(this).closest('tr').attr('id'),
            rowId = rowId.split('-'),
            typeEmail = $(this).closest('tr').find('select[name=rappel-pm-tm]').val();
        if(typeEmail === undefined){
            typeEmail = $(this).closest('tr').find('.js-rappel-pm-tm').html();
        }else{
            if(typeEmail == '') return show_info('Contrôle securité', 'Veuillez choisir un type d\'envoi mail', 'error');
            typeEmail = (typeEmail == 1) ? 'Automatique' : 'Manuel';
        }

        $.ajax({
            data: {
                dossier: parseInt(rowId[0]),
                notification: parseInt(rowId[1]),
                typeEmail: typeEmail,
                typeNotif: 'banque'
            },
            url: Routing.generate('dossier_admin_rappel_pm_param_edited'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                if(data === '"erreur"'){
                    return show_info('Contrôle securité', 'Ce dossier n\'a pas de tache', 'error');
                }
                test_security(data);
                //modal_ui(options,data, false,0.95,0.75);
                show_modal(data,'Paramétrage de l\'Envoi '+typeEmail,undefined,'modal-xx-lg');
                /*set_datepicker($('#id_pm_action_date'));
                pm_change_periode();
                pm_change_statut_envoi();*/
            }
        });
    });

    $(document).on('change','#id_pm_action_periode',function(){
        pm_change_periode();
    });

    $(document).on('change','#id_pm_action_stat_envoi',function(){
        pm_change_statut_envoi();
    });

    $(document).on('click','.save_pm_action',function(){
        var _this = $(this);
        var taches = [];
        var listeTaches = $('#listes-taches').find('.tile-wrapper');
        var typeEmail = parseInt(_this.attr('data-type-mail'));
        var typeNotif = _this.attr('data-type-notif');
        var countTache = listeTaches.length;
        var data = [];

        if(countTache === 0)
            return show_info('Contrôle securité', 'Veuiller ajouter des tâches pour le dossier', 'error');

        listeTaches.each(function(){
            var tache = $(this).find('.titre-tache').html();
            taches.push({
                tache: tache.trim()
            });
        });

        if(typeEmail){
            var stateJourFixe = -1;
            var valPrim = $('#js_second_regle').val();
            var valSec = $('#js_jour_fixe').val();
            var state = $('#chk_inp_second_regle').prop('checked');
            var state1 = $('#chk_inp_troisieme_regle').prop('checked');
            if(!state && !state1)
                return show_info('Contrôle securité', 'Veuiller choisir au moin une règle pour le dossier', 'error');

            var value = '';
            if(state)
                valPrim = valPrim;

            //1 =>delai prochaine tache, 2=>date fixe, 3=>les deux
            var choixRegle = -1;
            if(state && state1){
                choixRegle = 3
            }else{
                choixRegle = (state) ? 1 : 2; 
            }

            if(state1 || choixRegle == 3){
                stateJourFixe = $('input[type=radio][name=jf]:checked').attr('value');
                if(stateJourFixe == 'une-fois')
                    valSec = valSec;
                if(stateJourFixe == 'recurr')
                    valSec = $('.value-jour-fixe').attr('data-value');
            }

            if(state){
                if(valPrim == '' || valPrim == 0)
                    return show_info('Contrôle securité', 'Veuiller remplir la formulaire pour la règle', 'error');
            }

            if(state1)
                if(valSec == '' || valSec == 0)
                    return show_info('Contrôle securité', 'Veuiller remplir la formulaire pour la règle', 'error');

            data = { 
                taches: taches,
                valPrim: valPrim,
                valSec: valSec,
                notification: _this.attr('data-id'),
                choixRegle: choixRegle,
                typeNotif: typeNotif,
                type: $('.value-jour-fixe').attr('data-type'),
                recur: $('.value-jour-fixe').attr('data-recur'),
                fin: $('.value-jour-fixe').attr('data-fin'),
                stateJourFixe : (stateJourFixe == 'une-fois') ? 1 : 2,
                typeEmail: _this.attr('data-type-mail')
            };
        }else{
            var value = $('#js_date_unique').val();
            if(value === '') 
                return show_info('Contrôle securité', 'Veuiller remplir la formulaire pour la règle', 'error');
            
            data = {
                value: value, 
                typeEmail: typeEmail, 
                taches: taches, 
                notification: _this.attr('data-id')
            };
        }
        
        $.ajax({
            data: data,
            url: Routing.generate('dossier_admin_save_action_pm'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                close_modal();
                show_info('SUCCES','Modifications bien enregistrées avec succès');
            }
        });
    });

    $(document).on('change', '#js_prem_regle', function () {
        var value = $(this).val();
        if(value == -1){
            $('#js_second_regle').removeAttr('disabled');
        }else{
            $('#js_second_regle').attr('disabled', 'disabled');
        }
    });

    $(document).on('change', '#js_second_regle', function () {
        var value = $(this).val();
        if(value === ''){
           $('#js_prem_regle').removeAttr('disabled'); 
       }else{
            $('#js_prem_regle').attr('disabled', 'disabled'); 
       }
    });

    $(document).on('click', '.chk_second_regle', function () {
        var state = $(this).prop('checked');
        console.log(state);
        if(state){
            $('#chk_inp_troisieme_regle').attr('disabled','disabled');
        }else{
            $('#chk_inp_troisieme_regle').removeAttr('disabled');
        }
    });

    $(document).on('click', '.chk_troisieme_regle', function () {
        var state = $(this).prop('checked');
        if(state){
            $('#chk_inp_second_regle').attr('disabled','disabled');
        }else{
            $('#chk_inp_second_regle').removeAttr('disabled');
        }
    });

    $(document).on('click', '.save_config_perso', function () {
        var state = $('input[type=radio][name=a]:checked').attr('value');
        var valueTousLes = $('.value-tous-les-'+index_modal_ui_mail).val();
        var value = '';
        var fin = '';
        var valueRecur = $('.reccurence_mois_'+index_modal_ui_mail).val();
        if(state === 'le'){
            fin = $('.datepicker-perso-'+index_modal_ui_mail).val();
            value = 'Tous les '+valueRecur+' mois à partir '+valueTousLes+', jusqu\'au '+fin;
        }else if(state === 'apres'){
            fin = $('.occurence_mois_'+index_modal_ui_mail).val();
            value = 'Tous les '+valueRecur+' mois à partir '+valueTousLes+', '+fin+' fois';
        }else{
            value = 'Tous les '+valueRecur+' mois à partir '+valueTousLes;
            fin = -1;
        }

        if((fin == '' || fin == 0) || (valueRecur == '' || valueRecur == 0) || valueTousLes == '') return show_info('Contrôle securité', 'Veuillez remplisser la formulaire', 'error');
        $('.value-jour-fixe').html(value);
        $('.value-jour-fixe').attr('data-recur', valueRecur);
        $('.value-jour-fixe').attr('data-type', state);
        $('.value-jour-fixe').attr('data-fin', fin);
        $('.value-jour-fixe').attr('data-value', valueTousLes);
        $(this).closest('.ui-dialog').find('button.ui-dialog-titlebar-close').click();
    });

    $(document).on('change', 'select[name=rappel-pm-tm]', function() {
        var rowKey = $(this).closest('tr').attr('id'),
            id = rowKey.split('-'),
            value = $(this).val(),
            classe = 'table_pm_8_tm',
            _this = $(this);

        if(value == '') return;

        $.ajax({
            url: Routing.generate('banque_pm_notification_type_mail'),
            type: 'POST',
            data: {
                dossier: id[0],
                value: value,
                classe: classe
            },
            dataType: 'html',
            success: function(data) {
                show_info('Succés','Modification enregistrée avec succès');
            }
        });
    });

    /*$(document).on('click', '.class_action_personnaliser', function (){
        index_modal_ui_mail++;
        var value = $('#js_jour_fixe').val();
        $.ajax({
            data: {
                  index: index_modal_ui_mail,
                  fin: $('.value-jour-fixe').attr('data-fin'),
                  type: $('.value-jour-fixe').attr('data-type'),
                  recur: $('.value-jour-fixe').attr('data-recur'),
                  value: value
            },
            url: Routing.generate('dossier_perso_form'),
            type: 'POST',
            async: true,
            dataType: 'html',
            success: function (data) {
                test_security(data);
                var options = { modal: false, resizable: false, title: 'Récurrence personnalisée' };
                modal_ui(options,data, false,0.6,0.3);
            }
        });
    });*/

    /*$(document).on('click','.save_pm_action',function(){
        var periode = 0,
            jour    = 0,
            date    = '',
            mois    = 0;
            error   = false,
            periode_annuels = [1,2,3,4,6,12],state = $('#id_pm_action_stat_envoi').prop('checked');
            
        periode = parseInt($('#id_pm_action_periode').val());
        if(!state){
            if (periode === -1)
            {
                show_info('Notice','Choisir la période','error');
                $('#id_pm_action_periode').closest('.form-group').addClass('has-error');
                return;
            }
            $('#id_pm_action_periode').closest('.form-group').removeClass('has-error');

            date = $('#id_pm_action_date').val();
            jour = parseInt($('#id_pm_action_jour').val());
            mois = parseInt($('#id_pm_action_mois').val());
            if (periode === 0)
            {
                if (date === '')
                {
                    show_info('Notice','Choisir la date de l\'envoi','error');
                    $('#id_pm_action_date').closest('.form-group').addClass('has-error');
                    error = true;
                }
                else $('#id_pm_action_date').closest('.form-group').removeClass('has-error');
                jour = 0;
                mois = 0;
            }else if (periode_annuels.in_array(periode)){
                if (isNaN(jour) || jour < 1 || jour > 31)
                {
                    show_info('Notice','Verifier le JOUR de l\'envoi','error');
                    $('#id_pm_action_jour').closest('.form-group').addClass('has-error');
                    error = true;
                }
                else $('#id_pm_action_jour').closest('.form-group').removeClass('has-error');

                if (isNaN(mois) || mois < 1 || mois > 12)
                {
                    show_info('Notice','Verifier le mois de l\'envoi','error');
                    $('#id_pm_action_mois').closest('.form-group').addClass('has-error');
                    error = true;
                }
                else $('#id_pm_action_mois').closest('.form-group').removeClass('has-error');
                date = '';
            }

            if (error)
                return;
        }

        $.ajax({
            data: {
                date: date,
                jour: jour,
                mois: mois,
                periode: periode,
                notification: $(this).attr('data-id'),
                state: state
            },
            url: Routing.generate('dossier_admin_save_action_pm'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                close_modal();
                show_info('SUCCES','Modifications bien enregistrées avec succès');
            }
        });
    });*/
});

function action_formatter(v) {
    return '<i class="fa fa-cog pointer icon-action class_action_pm" aria-hidden="true"></i>';
}

function set_table_pm(tableau_rappel_pm, rappel_pm_grid_height, last_table_select) {
    tableau_rappel_pm.jqGrid({
        datatype: 'local',
        loadonce: true,
        sortable: false,
        height: rappel_pm_grid_height,
        autowidth: true,
        shrinkToFit: true,
        viewrecords: true,
        hidegrid: true,
        rownumbers: true,
        rownumWidth: 35,
        rowNum: 2000,
        rowList: [2000, 3000, 5000],
        pager: '#pager_tableau',
        colNames: [
            'Dossiers',
            'Status',
            'Destinataires', 'Copie', 'Titre', 'Nom_Contact',
            'Contenu', 'Type Mail', 'Critère mail automatique', 'Contenu_Complet', 'Objet'
        ],
        colModel: [
            {
                name: 'rappel-pm-dossier', index: 'rappel-pm-dossier', editable: false, sortable: true, width: 180, classes: 'js-rappel-pm-dossier'
            },
            {
                name: 'rappel-pm-dossier-status', index: 'rappel-pm-dossier-status', editable: false, sortable: true, width: 140, classes: 'js-rappel-pm-dossier-status', align: 'center'
            },
            {
                name: 'rappel-pm-dest', index: 'rappel-pm-dest', editable: false, sortable: true, width: 120, classes: 'js-rappel-pm-dest'
            },
            {
                name: 'rappel-pm-copie', index: 'rappel-pm-copie', hidden: true, classes: 'js-rappel-pm-copie'
            },
            {
                name: 'rappel-pm-titre', index: 'rappel-pm-titre', hidden: true, classes: 'js-rappel-pm-titre'
            },
            {
                name: 'rappel-pm-nom', index: 'rappel-pm-nom', hidden: true, classes: 'js-rappel-pm-nom'
            },
            {
                name: 'rappel-pm-contenu', index: 'rappel-pm-contenu', editable: false, sortable: true, width: 120, classes: 'js-rappel-pm-contenu'
            },
            {
                name: 'rappel-pm-tm', index: 'rappel-pm-tm', sortable: true, width: 40, editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('dossier_config_manuelAuto', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                }, classes: 'js-rappel-pm-tm'
            },
            {
                name: 'rappel-pm-action', index: 'rappel-pm-action', editable: false, align: 'center', width: 20, classes: 'js-rappel-pm-action', formatter: function(v){return action_formatter(v)} 
            },
            {
                name: 'rappel-pm-contenu-full', index: 'rappel-pm-contenu-full', editable: false, hidden: true, classes: 'js-rappel-pm-contenu-full'
            },
            {
                name: 'rappel-pm-objet', index: 'rappel-pm-objet', editable: false, hidden: true, classes: 'js-rappel-pm-objet'
            }
        ],
        ajaxRowOptions: { async: true },
        onSelectRow: function (id) {
            if (id && id !== last_table_select) {
                tableau_rappel_pm.restoreRow(last_table_select);
                last_table_select = id;
            }
            tableau_rappel_pm.editRow(id, true);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);
            var periode_col = target.closest('td').hasClass('js-rappel-pm-dest');
            var contenu_col = target.closest('td').hasClass('js-rappel-pm-contenu');

            return (!item_action && !periode_col && !contenu_col);
        },
        loadComplete: function() {
            $(document).find('#debut-envoi-all')
                .datepicker({
                    format:'dd/mm/yyyy',
                    language: 'fr',
                    autoclose:true,
                    todayHighlight: true,
                    clearBtn: true
                })
                .on('changeDate', function() {
                    var field = 'DebutEnvoi',
                        value = $(this).val();
                    editParamAll(field, value);
                })
                .on('clearDate', function() {
                    var field = 'DebutEnvoi',
                        value = $(this).val();
                    editParamAll(field, value);
                });
        }
    });
}

/** RECHARGE GRID RAPPEL IMAGE */
function reloadGridRappelPm() {
    var client = $('#client-config').val(),
        site = $('#site-config').val(),
        tableau_rappel_pm = $('#table_config_4');
    tableau_rappel_pm.jqGrid('setGridParam', {
        url: Routing.generate('dossier_admin_rappel_pm_param_liste', {client: client, site: site}),
        datatype: 'json'
    }).trigger('reloadGrid', [{page: 1, current: true}]);
}

function saveRowEdited(rowid)
{
    tableau_rappel_pm.jqGrid('setGridParam', {
        editurl: Routing.generate('rappel_pm_param_update', {notification:rowid})
    }).jqGrid('saveRow', rowid, {
        "aftersavefunc": function() {
            // reloadGridRappelPm();
        }
    });
}

function editParamAll(field, value)
{
    swal({
        title: 'Attention',
        text: "Voulez-vous modifier le paramètre pour tous les dossiers ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1ab394',
        cancelButtonColor: '#f8ac59',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                var client = rappel_pm_client.val(),
                    site = rappel_pm_site.val(),
                    url = Routing.generate('dossier_config_rappel_image_param_edit_all'),
                    formData = new FormData();
                formData.append('client', client);
                formData.append('site', site);
                formData.append('field', field);
                formData.append('value', value);

                fetch(url, {
                    method: 'POST',
                    credentials: 'include',
                    body: formData
                }).then(function (response) {
                    return response.json();
                }).then(function (data) {
                    resolve();
                    reloadGridRappelImage();
                }).catch(function (error) {
                    show_info('', 'Une erreur est survenue. Merci de réessayer.', 'error');
                });
            })
        }
    });
}

function set_datepicker(input) {
    input.datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: 'fr',
        daysOfWeekHighlighted: '0,6',
        todayHighlight: true,
        autoclose: true
    });
}

function pm_change_statut_envoi() {
    var state = $('#id_pm_action_stat_envoi').prop('checked');
    if(state){
        $('#id_pm_action_periode').attr('disabled', 'disabled');
        $('#id_pm_action_periode option:eq(0)').prop('selected', true);
        pm_change_periode();  
    }else{  
        $('#id_pm_action_periode').removeAttr('disabled');
    }
}

function pm_change_periode(argument) {
    var periode = parseInt($('#id_pm_action_periode').val());
    $('.container').addClass('hidden');
    /*0=ponctuel,1=annuel,2=semestriel,3=quadrimestriel,4=trimestriel,6=bimensuel,12=mensuel*/
    if (periode === 0)
    {
        $('#id_pm_action_date').closest('.container').removeClass('hidden');
    }
    else if (periode !== -1)
    {
        $('#id_pm_action_periode').closest('.container').removeClass('hidden');
        $('#id_pm_action_jour').closest('.container').removeClass('hidden');
        $('#id_pm_action_mois').closest('.container').removeClass('hidden');
    }
}

function updateGridSizeRappelPm() {
    setTimeout(function() {
        window_height = window.innerHeight;
        rappel_pm_grid_height = $(window).height() - 80;
        tableau_rappel_pm.jqGrid("setGridWidth", $("#rappel-image").width() - 50);
        tableau_rappel_pm.jqGrid("setGridHeight", rappel_pm_grid_height);
    }, 0);
}