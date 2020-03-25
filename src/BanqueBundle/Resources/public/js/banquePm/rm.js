/**
 * Created by SITRAKA on 07/12/2018.
 */
 var index_ui_modal_log = 0;
$(document).ready(function(){
    $(document).on('click', '.show-detail-image', function () {
        show_detail_mois($(this));
    });

    $(document).on('click', '.t_ob_show', function () {
        $('.t_ob_qtip').qtip('hide');
        var rowKey = $('#table_pm_8').jqGrid('getGridParam',"selrow"),
            rowKey = rowKey.split('-'),
            dossierId = rowKey[0],
            banqueCompteId = rowKey[1],
            nature = $(this).attr('data-id');
        $.ajax({
            data: {
                banqueCompteId: banqueCompteId,
                dossierId: dossierId,
                client: $('#client').val(),
                nature: nature,
            },
            url: Routing.generate('banque_pm_show_ob_manquante'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                show_modal(data,'OB Manquantes',undefined,'modal-lg');
            }
        });
    });

    $(document).on('click', '.ob_categorie_status', function () {
        $('.t_ob_qtip').qtip('hide');
        var rowKey = $('#table_pm_8').jqGrid('getGridParam',"selrow"),
            rowKey = rowKey.split('-'),
            dossierId = rowKey[0],
            status = $(this).is(':checked') ? 1 : 0,
            s_categorie = $(this).attr('data_s_categorie');

        $.ajax({
            data: {
                dossier: dossierId,
                s_categorie: s_categorie,
                status: status
            },
            type: 'POST',
            url: Routing.generate('banque_pm_ob_save_status'),
            dataType: 'html',
            success: function(data) {
                //show_modal(data);return;
                show_info('Succès','Modification enregistrée avec succès');
            }
        });
    });

    $(document).on('click', '.show_detail_table', function () {
        var rowKey = $('#table_pm_9').jqGrid('getGridParam',"selrow"),
            rowKey = rowKey.split('-'),
            type = $(this).attr('data-type'),
            titre = '';
        if(parseInt($(this).html()) === 0) return;

        if(type === 'dec'){
            titre = 'Dépenses sans pièces';
        }else if(type === 'enc'){
            titre = 'Encaissement sans pièces';
        }else{
            titre = 'Chèques non identifiés';
        }

        $.ajax({
            data: {
                dossierId: rowKey[0],
                banqueCompteId: rowKey[1],
                type: type,
                exercice: $('#exercice').val(),
            },
            type: 'POST',
            url: Routing.generate('banque_pm_show_detail_pm'),
            dataType: 'html',
            success: function(data) {
                show_modal(data, titre, undefined, 'modal-lg');
            }
        });
    });

    $(document).on('change', '.cl_notif_select_banque', function () {
        var rowKey = $(this).closest('tr').attr('id'),
            rowKey = rowKey.split('-'),
            value = $(this).val(),
            classe = $(this).parent().attr('aria-describedby');

        if(value == '') return;

        $.ajax({
            url: Routing.generate('banque_pm_notification_type_mail'),
            type: 'POST',
            data: {
                dossier: rowKey[0],
                value: value,
                classe: classe
            },
            dataType: 'html',
            success: function(data) {
                show_info('Succés','Modification enregistrée avec succès');
            }
        });
    });

    $(document).on('click', '.show_log_ml_do', function() {
        var rowKey = $(this).closest('tr').attr('id'),
            rowKey = rowKey.split('-'),
            options = { modal: false, resizable: true,title: 'LISTES LOG EMAILS' };
        index_ui_modal_log++;
        $.ajax({
            url: Routing.generate('banque_pm_notification_log'),
            type: 'POST',
            data: {
                dossier: rowKey[0],
                index: index_ui_modal_log
            },
            dataType: 'html',
            success: function(data) {
                modal_ui(options,data, false,0.6,0.5);
            }
        });
    });

    $(document).on('click', '.show_contenu_mail', function() {
        var id = $(this).closest('tr').attr('data-id'),
            options = { modal: false, resizable: true,title: 'Contenu' };
        $.ajax({
            url: Routing.generate('banque_pm_notification_log_contenu_mail'),
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'html',
            success: function(data) {
                modal_ui(options,data, false,0.8,0.6);
            }
        });
    });

    $(document).on("click", ".class_action_pm_notif", function () {
        var rowId = $(this).closest('tr').attr('id'),
            rowId = rowId.split('-'),
            typeEmail = $(this).closest('tr').find('.cl_notif_select_banque').val();
        if(typeEmail == '') return;
        typeEmail = (typeEmail == 1) ? 'Automatique' : 'Manuel';

        $.ajax({
            data: {
                dossier: parseInt(rowId[0]),
                notification: parseInt(rowId[2]),
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


    $(document).on("click", ".class_action_autres_pm_notif", function () {
        var rowId = $(this).closest('tr').attr('id'),
            rowId = rowId.split('-'),
            typeEmail = $(this).closest('tr').find('.cl_notif_select_banque').val();
        if(typeEmail == '') return;
        typeEmail = (typeEmail == 1) ? 'Automatique' : 'Manuel';

        $.ajax({
            data: {
                dossier: parseInt(rowId[0]),
                notification: parseInt(rowId[2]),
                typeEmail: typeEmail,
                typeNotif: 'autres_pm'
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
                show_modal(data,'Paramétrage de l\'Envoi '+typeEmail,undefined,'modal-xx-lg');
            }
        });
    });
});

function set_table_rm(tab_element,datas)
{
    var type = parseInt(tab_element.attr('data-type')),
        table = '<table id="table_pm_'+type+'"></table>';
    tab_element.find('.panel-body').html(table);
    var table_selected = $('#table_pm_'+type),
        w = table_selected.parent().width(),
        h = $(window).height() - 210,
        editurl = 'index.php';

    set_table_jqgrid(datas['datas'],h,get_col_model_rm(datas['m']),get_col_model_rm(datas.m,w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);
}

function get_col_model_rm(mois,w)
{
    var colM = [],i;

    if (typeof w !== 'undefined')
    {
        colM.push({ name:'b', index:'b', sortable:true, width: 10 * w/100, classes:'' });
        colM.push({ name:'bc', index:'bc', sortable:true, width: 10 * w/100, classes:'' });
        colM.push({ name:'sc', index:'sc', sortable:true, width: 10 * w/100, classes:'' });
        colM.push({ name:'sci', index:'sci', hidden: true, classes:'cl_sc_id', formatter: function(v){ return sc_formatter(v) } });
        for (i = 0; i < mois.length; i++)
            colM.push({ name:'m_'+i, index:'m_'+i, sortable:true, align:'center', width: (68/mois.length) * w/100, classes:'', formatter: function(v){return pm_formatter(v)} });
    }
    else
    {
        colM.push('Banque');
        colM.push('Compte');
        colM.push('S. Catégorie');
        colM.push('');
        for (i = 0; i < mois.length; i++)
        {
            colM.push(mois[i]);
        }
    }
    return colM;
}

function sc_formatter(v)
{
    return (typeof v !== 'undefined') ? v : $('#js_zero_boost').val();
}

function pm_formatter(v)
{
    if (parseInt(v.s) === 0)
    {
        if (v.it === null) return '<i class="fa fa-upload pointer js_show_upload_image text-warning" data-is_releve="'+((typeof v.d !== 'undefined' && parseInt(v.d) === 1) ? 1 : 0)+'" data-type="1" data-mois="'+v.m+'" aria-hidden="true"></i>';
        else return image_formatter(v.it);
    }
    else if (parseInt(v.s) === -1)
    {
        return '';
    }
    else if (typeof v.d !== 'undefined' && parseInt(v.d) === 1)
    {
        return '';
    }
    return '<span class="show-detail-image pointer" data-mois="'+v.m+'">'+v.s+'</span>';
}

function show_detail_mois(span)
{
    index_ui_modal_pm++;
    var datas = span.closest('tr').attr('id').split('-'),
        type = parseInt(datas[0]),
        banque_compte = datas[1];

    $.ajax({
        data: {
            mois: span.attr('data-mois'),
            banque_compte: banque_compte,
            type: type
        },
        url: Routing.generate('banque_pm_detail_mois'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            show_modal(data,'Liste de pièces');
        }
    });
}

function set_table_rm_all_dossier(tab_element,datas) {
     var type = parseInt(tab_element.attr('data-type')),
        table = '<table id="table_pm_'+type+'"></table>';
    tab_element.find('.panel-body').html(table);
    var table_selected = $('#table_pm_'+type),
        w = table_selected.parent().width(),
        h = $(window).height() - 210,
        editurl = 'index.php';

    set_table_jqgrid(datas,h,get_col_model_rm_all_dossier(),get_col_model_rm_all_dossier(w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);   
    activer_qtip_rm_all();
    var table_pm_8_grid = $('#table_pm_8');
    table_pm_8_grid.jqGrid('hideCol',["d_ob_m"]);
    table_pm_8_grid.jqGrid('hideCol',["tache"]);
    table_pm_8_grid.jqGrid('hideCol',["mq"]);
    table_pm_8_grid.jqGrid('hideCol',["d_m_rb"]);
}

function set_table_autr_pm(tab_element,datas) {
     var type = parseInt(tab_element.attr('data-type')),
        table = '<table id="table_pm_'+type+'"></table>';
    tab_element.find('.panel-body').html(table);
    var table_selected = $('#table_pm_'+type),
        w = table_selected.parent().width(),
        h = $(window).height() - 210,
        editurl = 'index.php';

    set_table_jqgrid(datas,h,get_col_model_autr_pm(),get_col_model_autr_pm(w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);   
    activer_qtip_rm_all();
    var table_pm_9_grid = $('#table_pm_9');
    table_pm_9_grid.jqGrid('hideCol',["d_ob_m"]);
    table_pm_9_grid.jqGrid('hideCol',["tache"]);
    table_pm_9_grid.jqGrid('hideCol',["mq"]);
    table_pm_9_grid.jqGrid('hideCol',["d_m_rb"]);
}

function get_col_model_autr_pm(w) {
    var colM = [],i;

    if(typeof w !== 'undefined')
    {
        colM.push({ name:'d', index:'d', sortable:true, width: 33 * w/100, classes:'' });
        colM.push({ name:'e', index:'e', sortable:true, width: 8 * w/100, classes:'', formatter: function(v){return ech_pm_formatter(v)} });  
        colM.push({ name:'pe', index:'pe', sortable:true, width: 5 * w/100, classes:'', sorttype:'date', formatter:'date', 
                formatoptions: {newformat:'d/m/y'} });
        colM.push({ name:'m', index:'m', sortable:true, width: 5 * w/100, align:'center', sorttype: 'number', classes:'', formatter: function(v){return mois_mq_pm_formatter(v)} });
        colM.push({ name:'ob', index:'ob', sortable:true, width: 5 * w/100, align:'center', classes:'', formatter: function(v){return ob_pm_formatter(v)} });
        colM.push({ name:'ur', index:'ur', sortable:true, width: 4 * w/100, align:'center', sorttype:'number', classes:'', formatter: function(v){return ur_pm_formatter(v)} });
        colM.push({ name:'al', index:'al', sortable:true, width: 4 * w/100, align:'center', sorttype:'number', classes:'' });
        colM.push({ name:'dec', index:'dec', sortable:true, width: 4 * w/100, align:'center', sorttype:'number', classes:'', formatter: function(v){return dec_formatter(v)} });
        colM.push({ name:'enc', index:'enc', sortable:true, width: 4 * w/100, align:'center', sorttype:'number', classes:'', formatter: function(v){return enc_formatter(v)} });
        colM.push({ name:'chq', index:'chq', sortable:true, width: 4 * w/100, align:'center', sorttype:'number', classes:'', formatter: function(v){return chq_formatter(v)} });
        colM.push({ name:'frns', index:'frns', sortable:true, width: 4 * w/100, align:'center', sorttype:'number', classes:'' });
        colM.push({ name:'clt', index:'clt', sortable:true, width: 4 * w/100, align:'center', sorttype:'number', classes:'' });
        colM.push({ name:'tm', index:'tm', sortable:true, width: 5 * w/100, align:'center',classes:'', formatter: function(v){return autoManuel_formatter(v)} });
        colM.push({ name:'ml', index:'ml', sortable:true, width: 5 * w/100, align:'left',classes:'' });
        colM.push({ name:'mail', index:'mail', sortable:false, align:'center', width: 5 * w/100, classes:'' });
        colM.push({ name:'d_ob_m', index:'d_ob_m', classes:'' });
        colM.push({ name:'tache', index:'tache', classes:'' });
        colM.push({ name:'mq', index:'mq', classes:'' });
        colM.push({ name:'d_m_rb', index:'d_m_rb', classes:'' });
    }
    else
    {
        colM.push('Dossier');
        colM.push('Echéance');
        colM.push('Prochaine échéance');
        colM.push('RB');
        colM.push('OB');
        colM.push('Urgence à relancer');
        colM.push('Pièces à lettrer');
        colM.push('Dépenses sans pièces');
        colM.push('Encaissement sans pièces');
        colM.push('Chèques non identifiés');
        colM.push('Fournisseurs impayées');
        colM.push('Clients impayées');
        colM.push('Type mail');
        colM.push('Mail');
        colM.push('Critère');
        colM.push('d_ob_m');
        colM.push('tache');
        colM.push('mq');
        colM.push('d_m_rb');
    }
    return colM;
}

function get_col_model_rm_all_dossier(w) {
    var colM = [],i;

    if(typeof w !== 'undefined')
    {
        colM.push({ name:'d', index:'d', sortable:true, width: 27 * w/100, classes:'' });
        colM.push({ name:'b', index:'b', sortable:true, width: 27.4 * w/100, classes:'' });
        colM.push({ name:'e', index:'e', sortable:true, width: 8 * w/100, classes:'', formatter: function(v){return ech_formatter(v)} });  
        colM.push({ name:'pe', index:'pe', sortable:true, width: 5 * w/100, classes:'', sorttype:'date', formatter:'date', 
                formatoptions: {newformat:'d/m/y'} });
        colM.push({ name:'ur', index:'ur', sortable:true, width: 4 * w/100, align:'center', sorttype:'number', classes:'', formatter: function(v){return ur_formatter(v)} });
        colM.push({ name:'m', index:'m', sortable:true, width: 5 * w/100, align:'center', sorttype: 'number', classes:'', formatter: function(v){return mois_mq_formatter(v)} });
        colM.push({ name:'ob', index:'ob', sortable:true, width: 5 * w/100, align:'center', classes:'', formatter: function(v){return ob_formatter(v)} });
        colM.push({ name:'tm', index:'tm', sortable:true, width: 5 * w/100, align:'center',classes:'', formatter: function(v){return autoManuel_formatter(v)} });
        colM.push({ name:'ml', index:'ml', sortable:true, width: 5 * w/100, align:'left',classes:'' });
        colM.push({ name:'mail', index:'mail', sortable:false, align:'center', width: 5 * w/100, classes:'' });
        colM.push({ name:'d_ob_m', index:'d_ob_m', classes:'' });
        colM.push({ name:'tache', index:'tache', classes:'' });
        colM.push({ name:'mq', index:'mq', classes:'' });
        colM.push({ name:'d_m_rb', index:'d_m_rb', classes:'' });
    }
    else
    {
        colM.push('Dossier');
        colM.push('Banque');
        colM.push('Echéance');
        colM.push('Prochaine échéance');
        colM.push('Urgence à relancer');
        colM.push('RB');
        colM.push('OB');
        colM.push('Type mail');
        colM.push('Mail');
        colM.push('Critère');
        colM.push('d_ob_m');
        colM.push('tache');
        colM.push('mq');
        colM.push('d_m_rb');
    }
    return colM;
}

function autoManuel_formatter(v)
{
    return '' +
        '<select class="input-in-jqgrid cl_notif_select_banque">' +
            '<option value="" '+((v === '') ? 'selected' : '')+'></option>' +
            '<option value="0" '+((v === 'Manuel') ? 'selected' : '')+'>Manuel</option>' +
            '<option value="1" '+((v === 'Automatique') ? 'selected' : '')+'>Automatique</option>' +
        '</select>';
}

function ur_formatter(v) {
    var color = '';
    var title = v+' Jours';
    if(v === 0){
        color = '#008000';
        title = 'Aucun relance à faire';
    }else if(v < 8){
        color = '#e95443';
    }else if(v >= 8 && v < 15){
        color = '#ffd700';
    }else{
        color = 'blue';
    }
    return '<i class="fa fa-circle pointer" title="'+title+'" style="color: '+color+';" aria-hidden="true"></i>';
}

function mois_mq_formatter(v) {
    if(v === '' || v === null) return v;
    var color = '';
    var title = '';
    if(v === 10){
        v = 'Inc.'
    }else{
        v =  'M-'+v;
    }
    
    if(v === 'M-1'){
        color = '#008000';
        title = 'Validé';
    }else{
        color = '#e95443';
        title = 'Non validé';
    }
    return '<span style="text-align: center;">'+v+'</span><i class="fa fa-circle pointer qtip_mois" title = "'+title+'" style="float: right; color: '+color+'; padding-top: 3px;" data-type="8" aria-hidden="true"></i>';
}

function ech_formatter(v) {
    if(v === '' || v === null) return v;
    return '<span class="pointer qtip_tache" aria-hidden="true">'+v+'</span>';
}

function dec_formatter(v) {
    if(v === '' || v === null) return v;
    return '<span class="pointer show_detail_table" data-type="dec">'+v+'</span>';
}

function enc_formatter(v) {
    if(v === '' || v === null) return v;
    return '<span class="pointer show_detail_table" data-type="enc">'+v+'</span>';
}

function chq_formatter(v) {
    if(v === '' || v === null) return v;
    return '<span class="pointer show_detail_table" data-type="chq">'+v+'</span>';
}

function ob_formatter(v) {
    var new_val = '';
    var color = '';
    var classOb = '';

    if(v === 'PB'){
        color = '#e95443';
        classOb = 'isPB';
    }else{
        color = '#008000';
        classOb = 'isOB';
    }
    new_val = '<i class="fa fa-circle '+classOb+' t_ob_qtip pointer" style="color:' + color + ';margin-left: 0px !important; padding-top: 3px;"></i>';
    return new_val;
}

function ur_pm_formatter(v) {
    var color = '';
    var title = v+' Jours';
    if(v === 0){
        color = '#008000';
        title = 'Aucun relance à faire';
    }else if(v < 8){
        color = '#e95443';
    }else if(v >= 8 && v < 15){
        color = '#ffd700';
    }else{
        color = 'blue';
    }
    return '<i class="fa fa-circle pointer" title="'+title+'" style="color: '+color+';" aria-hidden="true"></i>';
}

function mois_mq_pm_formatter(v) {
    if(v === '' || v === null) return v;
    var color = '';
    var title = '';
    if(v === 10){
        v = 'Inc.'
    }else{
        v =  'M-'+v;
    }
    
    if(v === 'M-1'){
        color = '#008000';
        title = 'Validé';
    }else{
        color = '#e95443';
        title = 'Non validé';
    }
    return '<span style="text-align: center;">'+v+'</span><i class="fa fa-circle pointer qtip_mois_pm" title = "'+title+'" style="float: right; color: '+color+'; padding-top: 3px;" data-type="8" aria-hidden="true"></i>';
}

function ech_pm_formatter(v) {
    if(v === '' || v === null) return v;
    return '<span class="pointer qtip_tache_pm" aria-hidden="true">'+v+'</span>';
}

function ob_pm_formatter(v) {
    var new_val = '';
    var color = '';
    var classOb = '';

    if(v === 'PB'){
        color = '#e95443';
        classOb = 'isPB';
    }else{
        color = '#008000';
        classOb = 'isOB';
    }
    new_val = '<i class="fa fa-circle '+classOb+' t_ob_qtip_pm pointer" style="color:' + color + ';margin-left: 0px !important; padding-top: 3px;"></i>';
    return new_val;
}

function activer_qtip_rm_all() {
    $('.t_ob_qtip_pm').qtip({
        content: {
            text: function (event, api) {
                var table_pm_8_grid = $('#table_pm_9');
                var row_key = table_pm_8_grid.jqGrid('getGridParam', 'selrow');
                var statutOb = table_pm_8_grid.getCell(row_key, 'ob');
                var dataObMq = table_pm_8_grid.getCell(row_key, 'd_ob_m');
                dataObMq = $.parseJSON(dataObMq);
                statutOb = statutOb.split('class')[1];
                statutOb = statutOb.split(' ')[2];
                var table_html = '<table class="table table-bordered">';
                var classOb = '';
                if (statutOb === 'isPB') {
                    table_html += '' +
                        '<tr>' +
                        '<th style="text-align: center;"><b>Sous Categorie<b></th>' +
                        '<th style="text-align: center;"><b>Envoi<b></th>' +
                        '<th style="text-align: center;"><b>Mois manquant</b></th>' +
                        '</tr>';
                    $.each(dataObMq, function (i,v)
                    {
                        if(v.nb != ''){
                            if(v.nb != 0){
                                classOb = (v.nature === '') ? '' : 't_ob_show';
                                table_html += '' +
                                    '<tr>' +
                                    '<td class="pointer '+classOb+'" data-id="'+v.nature+'">'+v.libelle+'</td>' +
                                    '<td style="text-align: center;"><input type="checkbox" class="ob_categorie_status pointer" data_s_categorie="'+v.souscatid+'" checked></td>' +
                                    '<td>'+v.nb+'</td>' +
                                    '</tr>';
                            }
                        }
                    });
                }else{
                    table_html += '<tr><td class="col-sm-12 aucun-mq-ob"> <i class="fa fa-check-circle"></i> Aucun Manquant </td></tr>';
                }
                table_html += '</table>';
                return table_html;
            }
        },
        position: {
            viewport: $(window),
            adjust  : {
                method: 'shift none'
            }
        },
        show : 'click',
        hide : 'unfocus',
        style: {
            classes: 'qtip-tache-css qtip-light qtip-shadow'
        }
    });

    $('.qtip_tache_pm').qtip({
        content: {
            text: function (event, api) {
                var table_pm_8_grid = $('#table_pm_9');
                var row_key = table_pm_8_grid.jqGrid('getGridParam', 'selrow');
                var data_tache = table_pm_8_grid.getCell(row_key, 'tache');
                data_tache = JSON.parse(data_tache);
                var table_html = '<table class="table table-bordered table-wrapper-gestion-tache-scroll-y my-custom-scrollbar-gestion-tache">';
                var color = '';
                var responsable = $('#js_filtre_respons_tache').val();
                var newTitre = '';
                /*var responsable = '';*/
                table_html += '' +
                    '<tr>' +
                    '<th style="text-align: center;">Titre</th>' +
                    '<th style="text-align: center;">Date</th>' +
                    '</tr>';
                $.each(data_tache, function (i,v)
                {
                    newTitre = (v.titre2).split('*');
                    color = '';
                    if(v.expirer){
                        color = 'color: #e95443;'
                    }
                    table_html += '' +
                                '<tr>' +
                                '<td style = "'+color+'">'+newTitre[0]+'*'+newTitre[2]+'</td>' +
                                '<td style = "'+color+'">'+v.date+'</td>' +
                                '</tr>';
                });
                table_html += '</table>';
                return table_html;
            }
        },
        position: {
            viewport: $(window),
            adjust  : {
                method: 'shift none'
            }
        },
        show : 'click',
        hide : 'unfocus',
        style: {
            classes: 'qtip-tache-css qtip-light qtip-shadow'
        }
    });

    $('.qtip_mois_pm').qtip({
        content: {
            text: function (event, api) {
                var table_pm_8_grid = $('#table_pm_9');
                var row_key = table_pm_8_grid.jqGrid('getGridParam', 'selrow');
                var data_mq = table_pm_8_grid.getCell(row_key, 'mq');
                var table_html = '<table class="table table-bordered table-wrapper-gestion-tache-scroll-y my-custom-scrollbar-gestion-tache">';
                var color = '';
                var isOK = true;
                var responsable = $('#js_filtre_respons_tache').val();
                /*var responsable = '';*/
                table_html += '' +
                    '<tr>' +
                    '<th style="text-align: center;"><b>Sous catégorie</b></th>' +
                    '<th style="text-align: center;"><b>Mois manquant</b></th>' +
                    '</tr>';

                data_mq = JSON.parse(data_mq);
                
                $.each(data_mq, function (i,v)
                {
                    if(v.m != ''){
                        isOK = false;
                        table_html += '' +
                        '<tr>' +
                        '<td><b>'+v.sc+'</b></td>' +
                        '<td>'+v.m+'</td>' +
                        '</tr>';
                    }
                });
                table_html += '</table>';
                if(isOK){
                    table_html = '<table class="table table-bordered">'+
                                 '<tr><td class="col-sm-12 aucun-mq-ob"> <i class="fa fa-check-circle"></i> Aucun Manquant </td></tr>'+
                                 '</table>'
                }
                return table_html;
            }
        },
        position: {
            viewport: $(window),
            adjust  : {
                method: 'shift none'
            }
        },
        show : 'click',
        hide : 'unfocus',
        style: {
            classes: 'qtip-tache-css qtip-light qtip-shadow'
        }
    });

    $('.t_ob_qtip').qtip({
        content: {
            text: function (event, api) {
                var table_pm_8_grid = $('#table_pm_8');
                var row_key = table_pm_8_grid.jqGrid('getGridParam', 'selrow');
                var statutOb = table_pm_8_grid.getCell(row_key, 'ob');
                var dataObMq = table_pm_8_grid.getCell(row_key, 'd_ob_m');
                dataObMq = $.parseJSON(dataObMq);
                statutOb = statutOb.split('class')[1];
                statutOb = statutOb.split(' ')[2];
                var table_html = '<table class="table table-bordered">';
                var classOb = '';
                if (statutOb === 'isPB') {
                    table_html += '' +
                        '<tr>' +
                        '<th style="text-align: center;"><b>Sous Categorie<b></th>' +
                        '<th style="text-align: center;"><b>Envoi<b></th>' +
                        '<th style="text-align: center;"><b>Mois manquant</b></th>' +
                        '</tr>';
                    $.each(dataObMq, function (i,v)
                    {
                        if(v.nb != ''){
                            if(v.nb != 0){
                                classOb = (v.nature === '') ? '' : 't_ob_show';
                                table_html += '' +
                                    '<tr>' +
                                    '<td class="pointer '+classOb+'" data-id="'+v.nature+'">'+v.libelle+'</td>' +
                                    '<td style="text-align: center;"><input type="checkbox" class="ob_categorie_status pointer" data_s_categorie="'+v.souscatid+'" checked></td>' +
                                    '<td>'+v.nb+'</td>' +
                                    '</tr>';
                            }
                        }
                    });
                }else{
                    table_html += '<tr><td class="col-sm-12 aucun-mq-ob"> <i class="fa fa-check-circle"></i> Aucun Manquant </td></tr>';
                }
                table_html += '</table>';
                return table_html;
            }
        },
        position: {
            viewport: $(window),
            adjust  : {
                method: 'shift none'
            }
        },
        show : 'click',
        hide : 'unfocus',
        style: {
            classes: 'qtip-tache-css qtip-light qtip-shadow'
        }
    });

    $('.qtip_tache').qtip({
        content: {
            text: function (event, api) {
                var table_pm_8_grid = $('#table_pm_8');
                var row_key = table_pm_8_grid.jqGrid('getGridParam', 'selrow');
                var data_tache = table_pm_8_grid.getCell(row_key, 'tache');
                data_tache = JSON.parse(data_tache);
                var table_html = '<table class="table table-bordered table-wrapper-gestion-tache-scroll-y my-custom-scrollbar-gestion-tache">';
                var color = '';
                var responsable = $('#js_filtre_respons_tache').val();
                var newTitre = '';
                /*var responsable = '';*/
                table_html += '' +
                    '<tr>' +
                    '<th style="text-align: center;">Titre</th>' +
                    '<th style="text-align: center;">Date</th>' +
                    '</tr>';
                $.each(data_tache, function (i,v)
                {
                    color = '';
                    newTitre = (v.titre2).split('*');
                    if(v.expirer){
                        color = 'color: #e95443;'
                    }

                    table_html += '' +
                                '<tr>' +
                                '<td style = "'+color+'">'+newTitre[0]+'*'+newTitre[2]+'</td>' +
                                '<td style = "'+color+'">'+v.date+'</td>' +
                                '</tr>';
                });
                table_html += '</table>';
                return table_html;
            }
        },
        position: {
            viewport: $(window),
            adjust  : {
                method: 'shift none'
            }
        },
        show : 'click',
        hide : 'unfocus',
        style: {
            classes: 'qtip-tache-css qtip-light qtip-shadow'
        }
    });

    $('.qtip_mois').qtip({
        content: {
            text: function (event, api) {
                var table_pm_8_grid = $('#table_pm_8');
                var row_key = table_pm_8_grid.jqGrid('getGridParam', 'selrow');
                var data_mq = table_pm_8_grid.getCell(row_key, 'mq');
                var table_html = '<table class="table table-bordered table-wrapper-gestion-tache-scroll-y my-custom-scrollbar-gestion-tache">';
                var color = '';
                var isOK = true;
                var responsable = $('#js_filtre_respons_tache').val();
                /*var responsable = '';*/
                table_html += '' +
                    '<tr>' +
                    '<th style="text-align: center;"><b>Sous catégorie</b></th>' +
                    '<th style="text-align: center;"><b>Mois manquant</b></th>' +
                    '</tr>';

                data_mq = JSON.parse(data_mq);
                
                $.each(data_mq, function (i,v)
                {
                    if(v.m != ''){
                        isOK = false;
                        table_html += '' +
                        '<tr>' +
                        '<td><b>'+v.sc+'</b></td>' +
                        '<td>'+v.m+'</td>' +
                        '</tr>';
                    }
                });
                table_html += '</table>';
                if(isOK){
                    table_html = '<table class="table table-bordered">'+
                                 '<tr><td class="col-sm-12 aucun-mq-ob"> <i class="fa fa-check-circle"></i> Aucun Manquant </td></tr>'+
                                 '</table>'
                }
                return table_html;
            }
        },
        position: {
            viewport: $(window),
            adjust  : {
                method: 'shift none'
            }
        },
        show : 'click',
        hide : 'unfocus',
        style: {
            classes: 'qtip-tache-css qtip-light qtip-shadow'
        }
    });
}

function loadCompleteJQgrid()
{
    activer_qtip_rm_all();
}
