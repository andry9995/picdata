/***********************************
 *          EVENEMENTS
***********************************/
var index_table_detail = 0;
$(document).on('change','#dossier',function(){
    charger_journaux();
    set_cloture();
});
$(document).on('click','#js_eb_journal_affichage',function(){
    if($(this).hasClass('fa-chevron-down'))
    {
        $(this).removeClass('fa-chevron-down');
        $(this).addClass('fa-chevron-up');
    }
    else
    {
        $(this).removeClass('fa-chevron-up');
        $(this).addClass('fa-chevron-down');
    }
    gerer_affichage_journal();
});
$(document).on('click','#js_eb_table_to_grid td[aria-describedby="js_eb_table_to_grid_js_eb_compte"]',function() {
    eb_affiche_details_compte($(this));
});
$(document).on('click','table[aria-labelledby="gbox_js_eb_table_to_grid"] tr td.js_show_image',function(){
    affiche_image_picdata($(this));
});
$(document).on('click','#js_eb_avec_solde',function(){
    eb_change_status_solde($(this));
    go();
});
$(document).on('click','.js_export',function(event){
    event.preventDefault();
    //return;
    eb_exporter($(this));
});
$(document).on('click','#js_eb_parametre_agee',function(){
    eb_show_parametrage_agee();
});
$(document).on('click','#js_eb_ajout_periode_agee',function(){
    eb_ajouter_periode_agee();
});
$(document).on('click','.js_eb_delete_periode_agee',function(){
   $(this).parent().parent().remove();
});
$(document).on('click','#js_eb_defaut_periode',function(){
    eb_reset_periode_agee();
    close_modal();
    eb_show_parametrage_agee();
});
$(document).on('click','#js_eb_valider_periode',function(){
    eb_valider_periode_agee();
});
$(document).on('click','.js_eb_avec_solde',function(){
    var compte_str = $(this).attr('data-compte_str').trim(),
        avec_solde = ($(this).find('i.fa').hasClass(eb_get_class_solde())) ? 0 : 1,
        td = null;
    $('#js_eb_table_to_grid td[aria-describedby="js_eb_table_to_grid_js_eb_compte"]').each(function(){
        if($(this).text().trim() == compte_str) td = $(this);
    });
    if(td == null) return;
    eb_affiche_details_compte(td,avec_solde);

    $(this).parent().parent().parent().find('button.ui-dialog-titlebar-close').click();
});
$(document).on('click','.js_menu_left',function(event){
    event.preventDefault();
    etat = parseInt($(this).attr('data-donnees'));
    $('#js_etat').val(etat);
    if(etat == 5) $('#js_id_journal').val($(this).attr('data-id_journal'));
    if(parseInt($(this).attr('data-donnees')) == -1234) return;
    go();
});

$(document).on('click','.pi',function(){
    var image_id = $(this).parent().find('.ip').text().trim(),
        lien = Routing.generate('app_image_picdata'),
        nom_image = $(this).text().trim();

    if(image_id == '')
    {
        show_info('ERREUR','CET PIECE N EST PAS ACCESSIBLE','error');
        return;
    }

    $.ajax({
        data: { image_id:image_id },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);

            var src = 'http://picdata.fr/picdataovh/visua_image_vao.php' + data,
                contenu = '<iframe src="'+src+'" width="100%" height="100%"></iframe>',
                options = {modal: false, resizable: true,title: nom_image};
            modal_ui(options,contenu);

            /*var src = 'http://picdata.fr/picdataovh/'+data.trim(),
                contenu = '<embed src="'+src+'" width="100%" height="100%" id="js_embed"/>',
                options = {modal: false, resizable: true,title: nom_image};
            modal_ui(options,contenu);*/
        }
    });
});



/***********************************
 *          FONCTIONS
 ***********************************/
function go()
{
    $('#eb_etat_conteneur').empty();
    $('#js_export').empty();

    if(!set_parametre()) return;

    etat = parseInt($('#js_etat').val());

    if(etat == 3 || etat == 4) $('#js_eb_parametre_agee').removeClass('hidden');
    else $('#js_eb_parametre_agee').addClass('hidden');

    if(etat < 3 || etat > 6)
    {
        if(etat < 3) $('#js_eb_avec_solde_text').html('Sold&eacute;es');
        else $('#js_eb_avec_solde_text').html('Lettr&eacute;es');
        $('#js_eb_avec_solde').removeClass('hidden');
    }
    else $('#js_eb_avec_solde').addClass('hidden');

    avec_solde = ($('#js_eb_avec_solde').find('.fa').hasClass(eb_get_class_solde())) ? 1 : 0;

    if(etat > 2 && exercice.length > 1)
    {
        show_info('Erreur','Choisir un seul exercice pour cet ETAT','warning');
        return;
    }

    journal = (etat == 5) ? parseInt($('#js_id_journal').val()) : 0;

    if(etat == 10) $('#js_eb_export').addClass('hidden');
    else  $('#js_eb_export').removeClass('hidden');

    gerer_height();

    verrou_fenetre(true);
    var lien = Routing.generate('etat_base_item')+'/'+etat;

    eb_set_titre(exercice);

    $.ajax({
        data: { dossier:dossier,exercice:JSON.stringify(exercice),
                mois:(mois.length != 12) ? JSON.stringify(mois) : 'Tous',
                journal:journal,id_compte:0,avec_solde:avec_solde,
                typeTier:-1,periode_agee:JSON.stringify(periode_agee),
                date_anciennete:date_anciennete.toMysqlFormat() },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(etat == 7 || etat == 8 || etat == 9)
            {
                //$('#eb_etat_conteneur').html(data);return;
                $('#eb_etat_conteneur').html('<table id="js_grand_livre_table"></table>');
                var table = $('#js_grand_livre_table'),
                    w = table.parent().width(),
                    h = $(window).height() * 0.55,
                    editurl = 'index.php';
                //mydata,height,colNames,colModel,table,caption,width,editurl,rownumbers,rowNum,grouping,groupingView
                set_table_jqgrid($.parseJSON(data),h,eb_get_col_moldel_gl(),eb_get_col_moldel_gl(w),table,'hidden',w,editurl,false,undefined,true,{groupField : ['cp'],groupColumnShow : [false]},'asc','p');
                set_tables_responsive();
                return;
            }

            $('#eb_etat_conteneur').html(data);

            if(etat == 3 || etat == 4) $('#eb_etat_conteneur').addClass('scroller');
            else $('#eb_etat_conteneur').removeClass('scroller');

            var width100 = $("#js_eb_table_to_grid").parent().width();
            var hauter = $(window).height() * 0.6;

            if(etat < 7 && etat != 3 && etat != 4)
            {
                tableToGrid("#js_eb_table_to_grid", {colModel:eb_get_col_model(width100), height:hauter,width:width100});
                group_head_jqgrid('js_eb_table_to_grid',getGroupHeaders(),true);
                remove_j_query_ui();
            }

            gerer_height();

            if(etat > 6 || etat == 3 || etat == 4)
            {
                reinitialiser_inspinia('eb_etat_conteneur');
                activer_qTip();
            }

            eb_set_class_table();
            recharger_date_ancienne = true;
        }
    });
}

function eb_get_col_moldel_gl(w)
{
    var colModel1 = new Array();
    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'p', index:'p', width:  w * 10 / 100, hidden: true,sorttype:'integer' });
        colModel1.push({ name:'de', index:'de', width:  w * 10 / 100, sortable: false });
        colModel1.push({ name:'j', index:'j', width:  w * 3 / 100, sortable: false });
        colModel1.push({ name:'pi', index:'pi', width:  w * 10 / 100, classes: 'pi', sortable: false});
        colModel1.push({ name:'ip', index:'ip', hidden:true, width:  0, classes:'ip' });
        colModel1.push({ name:'l', index:'l', width:  w * 35 / 100, sortable: false });
        colModel1.push({ name:'d', index:'d', width:  w * 10 / 100, align:'right', sortable: false, formatter: function (v) { return number_format(v,2,',','\x20');} });
        colModel1.push({ name:'c', index:'c', width:  w * 10 / 100, align:'right', sortable: false, classes:'text-danger', formatter: function (v) { return number_format(v,2,',','\x20');} });
        colModel1.push({ name:'lt', index:'lt', width:  w * 2 / 100, sortable: false });
        colModel1.push({ name:'sd', index:'sd', width:  w * 10 / 100, sortable: false, align:'right',formatter: function (v) { return number_format(v,2,',','\x20');} });
        colModel1.push({ name:'sc', index:'sc', width:  w * 10 / 100, sortable: false, align:'right', classes:'text-danger', formatter: function (v) { return number_format(v,2,',','\x20');} });
        colModel1.push({ name:'cp', index:'cp', hidden:true, width:  0 });
    }
    else colModel1 = [
        'N',
        'Date',
        'Jnl',
        'Piece',
        '',
        'Libelle',
        'Debit',
        'Credit',
        'L',
        'solde Debit',
        'solde Credit',
        'Compte'];
    return colModel1;
}

//get col model
function eb_get_col_model(width100)
{
    var colModel1 = [];

    //etat = parseInt($('#js_eb_menu a.eb_menu_active').attr('data-etat'));
    etat = parseInt($('#js_etat').val());
    if(etat == 0 || etat == 1 || etat == 2)
    {
        colModel1.push({name:"js_eb_est_tiers",align:"left",width:0,hidden:true});
        colModel1.push({name:"js_eb_id_compte",align:"center",width:0,hidden:true});
        colModel1.push({name:"js_eb_compte",align:"center",width:width100*15/100});
        colModel1.push({name:"js_eb_intitule",align:"left",width:width100*55/100});
        if(exercice.length == 1)
        {
            colModel1.push({name: "js_eb_debit", align: "right", width: width100 * 15 / 100});
            colModel1.push({name: "js_eb_credit", align: "right", width: width100 * 15 / 100});
        }
        for(i = 0;i < exercice.length;i++)
        {
            colModel1.push({name: "js_eb_solde_debit_" + exercice[i], align: "right", width: width100 * 15 / 100});
            colModel1.push({name: "js_eb_solde_credit_" + exercice[i], align: "right", width: width100 * 15 / 100});
        }
    }
    if(etat == 5)
    {
        colModel1.push({name:"js_eb_est_tiers",align:"center",width:0,hidden:true});
        colModel1.push({name:"js_eb_id_compte",align:"center",width:0,hidden:true});

        colModel1.push({name:"js_eb_date",align:"center",width:width100*10/100});
        colModel1.push({name:"js_eb_journal",align:"center",width:width100*6/100});
        colModel1.push({name:"js_eb_compte",align:"left",width:width100*10/100});
        colModel1.push({name:"js_eb_piece",align:"left",width:width100*11/100});
        colModel1.push({name:"js_eb_libelle",align:"left",width:width100*45/100});
        colModel1.push({name:"js_eb_debit",align:"right",width:width100*9/100});
        colModel1.push({name:"js_eb_credit",align:"right",width:width100*9/100});
    }
    if(etat == 6)
    {
        colModel1.push({name:"js_eb_date",align:"center",width:width100*15/100});
        colModel1.push({name:"js_eb_journal",align:"center",width:width100*10/100});
        colModel1.push({name:"js_eb_libelle",align:"left",width:width100*35/100});
        colModel1.push({name:"js_eb_debit",align:"right",width:width100*20/100});
        colModel1.push({name:"js_eb_credit",align:"right",width:width100*20/100});
    }
    return colModel1;
}

function getGroupHeaders()
{
    colModel1 = new Array();
    //etat = parseInt($('#js_eb_menu a.eb_menu_active').attr('data-etat'));
    etat = parseInt($('#js_etat').val());
    if(etat == 0 || etat == 1 || etat == 2)
    {
        if(exercice.length == 1) colModel1.push({startColumnName: 'js_eb_debit', numberOfColumns: 4, titleText: '<strong>'+exercice[0]+'</strong>'});
        else
            for(i = 0;i < exercice.length;i++)
                colModel1.push({startColumnName: 'js_eb_solde_debit_'+exercice[i], numberOfColumns: 2, titleText: '<strong>'+exercice[i]+'</strong>'});
    }
    if(etat == 5)
        colModel1.push({startColumnName: 'js_eb_est_tiers', numberOfColumns: 9, titleText: '<strong>'+exercice[0]+'</strong>'});
    if(etat == 6)
        colModel1.push({startColumnName: 'js_eb_date', numberOfColumns: 5, titleText: '<strong>'+exercice[0]+'</strong>'});
    return colModel1;
}

function charger_journaux()
{
    verrou_fenetre(true);
    var lien = Routing.generate('etat_base_journal');
    $.ajax({
        data: { dossier:$('#dossier').val() },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#js_eb_journaux').remove();
            $('#side-menu li.pointer a.js_menu_item').each(function(){
                if($(this).attr('data-libelle').trim() == 'Journaux')
                {
                    $('#js_arrow_journal').remove();
                    $(this).html($(this).html() + '<i class="fa arrow" id="js_arrow_journal"></i>');

                    //$('<i class="fa arrow"></i>').insertAfter($(this).find('i.fa'));
                    class_new_ul = get_next_class_ul_menu($(this).parent().parent());
                    new_ul = '<ul id="js_eb_journaux" class="'+class_new_ul+'"></ul>';
                    $(new_ul).insertAfter($(this));
                    $('#js_eb_journaux').html(data);
                    refreshMetsiMenu();
                }
            });
            go();
        }
    });
}

function gerer_affichage_journal()
{
    if($('#js_eb_journal_affichage').hasClass('fa-chevron-down'))
        $('.js_eb_journal_liste').addClass('hidden');
    else
        $('.js_eb_journal_liste').removeClass('hidden');
}

function eb_set_class_table()
{
    //compte cliquable
    $('#js_eb_table_to_grid td[aria-describedby="js_eb_table_to_grid_js_eb_compte"]').addClass(eb_get_class_compte());
    $('#jqgh_js_eb_table_to_grid_js_eb_compte').addClass(eb_get_class_compte());
    //piece
    $('#js_eb_table_to_grid tr td span.js_show_image').each(function(){
        $(this).parent().attr('data-id_image',$(this).attr('data-id_image'))
                        .addClass(eb_get_class_piece() + ' pointer js_show_image')
                        .html($(this).html().trim());
    });
    $('#jqgh_js_eb_table_to_grid_js_eb_piece').addClass(eb_get_class_piece());
    //solde credit
    for(i = 0;i<exercice.length;i++)
    {
        $('#jqgh_js_eb_table_to_grid_js_eb_solde_credit_' + exercice[i]).addClass(eb_get_class_credit());
        $('#js_eb_table_to_grid td[aria-describedby="js_eb_table_to_grid_js_eb_solde_credit_'+ exercice[i] +'"]').addClass(eb_get_class_credit());
    }
    //credit
    $('#jqgh_js_eb_table_to_grid_js_eb_credit').addClass(eb_get_class_credit());
    $('#js_eb_table_to_grid td[aria-describedby="js_eb_table_to_grid_js_eb_credit"]').addClass(eb_get_class_credit());
}

function eb_affiche_details_compte(td,avec_sold)
{
    etat_sel = parseInt(td.parent().find('td[aria-describedby="js_eb_table_to_grid_js_eb_est_tiers"]').text().trim());
    id_compte  = parseInt(td.parent().find('td[aria-describedby="js_eb_table_to_grid_js_eb_id_compte"]').text().trim());
    exercice_sel = new Array();
    exercice_sel.push(exercice[0]);
    avec_solde = ($('#js_eb_avec_solde').find('.fa').hasClass(eb_get_class_solde())) ? 1 : 0;
    avec_solde = typeof avec_sold !== 'undefined' ? avec_sold : avec_solde;

    compte = (etat_sel != 1) ? td.text().trim().toString().substr(0,5) : null;
    typeTier = -1;

    if(compte == '41100' || compte == '40100')
    {
        id_compte = 0;
        etat_sel = 1;
        typeTier = (compte == '40100') ? 0 : 1;
    }

    lien = Routing.generate('etat_base_item')+'/'+((etat_sel == 1) ? 7 : 7);
    $.ajax({
        data: { dossier:dossier,exercice:JSON.stringify(exercice_sel),mois:(mois.length != 12) ? JSON.stringify(mois) : 'Tous',journal:journal,id_compte:id_compte, avec_solde:avec_solde, typeTier:typeTier},
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            class_i = (avec_solde == 1) ? eb_get_class_solde() : '';
            mise_en_forme = (avec_solde == 1) ? '' : '&nbsp;&nbsp;&nbsp;&nbsp;';
            compte_str = td.parent().find('td[aria-describedby="js_eb_table_to_grid_js_eb_compte"]').text();
            intitule_str = td.parent().find('td[aria-describedby="js_eb_table_to_grid_js_eb_intitule"]').text();
            titre = '<h3>'+
                        '<span>'+compte_str+'&nbsp;&nbsp;<small class="text-white">'+intitule_str+'</small>&nbsp;&nbsp;&nbsp;&nbsp;</span>'+
                        '<span class="label label-default">Exercice:&nbsp;'+exercice[0]+'</span>&nbsp;&nbsp;&nbsp;&nbsp;';
            titre +=    '<span class="js_eb_avec_solde pointer" class="pointer text-white" data-compte_str="'+compte_str+'">'+
                            '<span class="btn btn-xs btn-default btn-outline">'+
                                '<i class="fa '+class_i+'"></i><span>'+ mise_en_forme +'</span>'+
                            '</span>&nbsp;'+
                            '<span><small class="text-white">Avec&nbsp;Lignes&nbsp;<span class="text-white" id="js_eb_avec_solde_text">Lettr&eacute;es</span></small></span>'+
                        '</span>'+
                    '</h3>';

            if(etat_sel != 1)
            {
                var options = {modal: false, resizable: true,title: titre};
                modal_ui(options,'<table id="js_grand_livre_table_'+index_table_detail+'"></table>',true);

                var table = $('#js_grand_livre_table_'+index_table_detail),
                    w = table.parent().width(),
                    h = $(window).height() * 0.55,
                    editurl = 'index.php';

                //mydata,height,colNames,colModel,table,caption,width,editurl,rownumbers,rowNum,grouping,groupingView
                set_table_jqgrid($.parseJSON(data),h,eb_get_col_moldel_gl(),eb_get_col_moldel_gl(w),table,'hidden',w,editurl,false,undefined,true,{groupField : ['cp'],groupColumnShow : [false]},'asc','p');
                index_table_detail++;
                return;
            }

            options = {modal: false, resizable: true,title: titre};
            modal_ui(options,'',true);
        }
    });
}

function affiche_image_picdata(td)
{
    alert('test');
    var image_id = td.attr('data-id_image'),
        lien = Routing.generate('app_image_picdata'),
        nom_image = td.text();
    $.ajax({
        data: { image_id:image_id },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var //src = 'http://picdata.fr/picdataovh/'+data.trim(),
                src = 'http://picdata.fr/picdataovh/visua_image_vao.php' + data,
                //contenu = '<embed src="'+src+'" width="100%" height="100%" id="js_embed"/>',
                contenu = '<iframe src="'+src+'" width="100%" height="100%"></iframe>',
                options = {modal: false, resizable: true,title: nom_image};
            modal_ui(options,contenu);
        }
    });
}

function eb_change_status_solde(a)
{
    if(a.find('.fa').hasClass(eb_get_class_solde()))
    {
        a.find('.fa').removeClass(eb_get_class_solde());
        a.find('.js_eb_mise_forme').html('&nbsp;&nbsp;&nbsp;&nbsp;');
    }
    else
    {
        a.find('.fa').addClass(eb_get_class_solde());
        a.find('.js_eb_mise_forme').html('');
    }
}

function eb_exporter(a)
{
    if(!set_parametre()) return;

    extention = a.attr('data-en');
    lien = Routing.generate('etat_base_export')+'/'+extention;
    content = encodeURI($('#js_eb_table_to_grid').html());
    //etat = parseInt($('#js_eb_menu a.eb_menu_active').attr('data-etat'));

    etat = parseInt($('#js_etat').val());

    array_tr = new Array();
    avec_solde = ($('#js_eb_avec_solde').find('.fa').hasClass(eb_get_class_solde())) ? 1 : 0;

    if(etat < 3 || etat == 5 || etat == 6)
    {
        $('#js_eb_table_to_grid tbody tr.ui-row-ltr').each(function () {
            array_td = new Array();
            $(this).find('td').each(function () {
                array_td.push($(this).text().trim());
            });
            array_tr.push(array_td);
        });
    }
    if(etat > 2 && etat < 5)
    {
        array_tr = new Array();
        $('#eb_etat_conteneur').find('div.ibox').each(function(){
            debit_credit = ($(this).find('div.ibox-title h5').hasClass('text-primary')) ? 1 :0;
            donnee_tr = new Array();
            $(this).find('div.ibox-content table.table tbody tr').each(function(){
                donnee_td = new Array();
                $(this).find('td').each(function(){
                    donnee_td.push($(this).text().trim());
                });
                donnee_tr.push(donnee_td);
            });
            donnee_tr_foot = new Array();
            $(this).find('div.ibox-content table.table tfoot tr').each(function(){
                donnee_td = new Array();
                index = 0;
                $(this).find('th').each(function(){
                    if(index != 0)
                        donnee_td.push($(this).text().trim());
                    index++;
                });
                donnee_tr_foot.push(donnee_td);
            });
            var ibox = {'d':debit_credit , 't':donnee_tr , 'f':donnee_tr_foot};
            array_tr.push(ibox);
        });
    }
    if(etat > 6 && etat < 10)
    {
        array_tr = new Array();
        $('#js_eb_gl_donnee div.ibox').each(function(){
            compte = $(this).find('div.ibox-title h5 span.btn strong').text().trim();
            intitule = $(this).find('div.ibox-title h5 small.m-l-sm').text().trim();
            donnee_tr = new Array();
            $(this).find('table.table tbody tr').each(function(){
                donnee_td = new Array();
                $(this).find('td').each(function(){
                    donnee_td.push($(this).text().trim());
                });
                donnee_tr.push(donnee_td);
            });
            donnee_tr_foot = new Array();
            $(this).find('table.table tfoot tr').each(function(){
                donnee_td = new Array();
                $(this).find('th').each(function(){
                    if(typeof $(this).attr('colspan') === "undefined")
                        donnee_td.push($(this).text().trim());
                });
                donnee_tr_foot.push(donnee_td);
            });
            var ibox = {'c':compte , 'i':intitule , 't':donnee_tr , 'f':donnee_tr_foot};
            array_tr.push(ibox);
        });
    }

    parametre = '<input type="hidden" name="array_tr" value="'+encodeURI(JSON.stringify(array_tr))+'">'+
        '<input type="hidden" name="exercice" value="'+encodeURI(JSON.stringify(exercice))+'">'+
        '<input type="hidden" name="etat" value="'+etat+'">'+
        '<input type="hidden" name="titre" value="'+encodeURI(JSON.stringify($('#js_eb_titre').text().trim()))+'">'+
        '<input type="hidden" name="dossier" value="'+dossier+'">'+
        '<input type="hidden" name="periode_agee" value="'+encodeURI(JSON.stringify(periode_agee))+'">';

    $('#js_export').attr('action',lien).html(parametre).submit();
}

function eb_reset_periode_agee()
{
    if(set_parametre()) charger_date_annciennete();
    periode_agee = new Array();
    periode_agee.push(90);
    periode_agee.push(60);
    periode_agee.push(30);
}

function eb_show_parametrage_agee()
{
    lien = Routing.generate('etat_base_param_agee');
    $.ajax({
        data: { periode_agee:JSON.stringify(periode_agee) , date_anciennete:date_anciennete.toMysqlFormat() },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            show_modal(data,'Param\xE8trage balance ag\xE9e','pulse');
            var formatted = $.datepicker.formatDate("d/m/yy", date_anciennete);
            $('#js_date_anciennete_choise input').val(formatted);
            $('#js_date_anciennete_choise .input-group.date').datepicker({
                startView: 1,
                todayBtn: "linked",
                language: "fr",
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true
            });
        }
    });
}

function eb_ajouter_periode_agee()
{
    $('#js_eb_periode_nouveau').blur();
    nouveau_periode = parseInt($('#js_eb_periode_nouveau').val().trim());
    if(isNaN(nouveau_periode) || periode_agee.in_array(nouveau_periode))
    {
        $('#js_eb_periode_nouveau').parent().parent().addClass('has-warning');
        return;
    }
    else
        $('#js_eb_periode_nouveau').parent().parent().removeClass('has-warning');

    $('<tr><td class="js_eb_periode_agee">'+nouveau_periode+'</td><td class="pointer"><i class="fa fa-trash js_eb_delete_periode_agee"></i></td></tr>').insertBefore($('#js_eb_last_line_agee'));
}

function eb_valider_periode_agee()
{
    recharger_date_ancienne = false;
    date_anciennete = $('#js_date_anciennete_choise').datepicker('getDate');

    periode_agee = new Array();
    $('td.js_eb_periode_agee').each(function(){
        valeur = parseInt($(this).text().trim());
        if(!periode_agee.in_array(valeur)) periode_agee.push(valeur);
    });
    periode_agee.sort(function(a, b) {
        return (a < b);
    });
    close_modal();
    go();
}

function eb_set_titre(exercices)
{
    var etat = parseInt($('#js_etat').val()),
        titre = '';
    if(etat == 0) titre = 'BALANCE GENERALE';
    if(etat == 1) titre = 'BALANCE FOURNISSEUR';
    if(etat == 2) titre = 'BALANCE CLIENT';
    if(etat == 3) titre = 'BALANCE AGEE FOURNISSEUR';
    if(etat == 4) titre = 'BALANCE AGEE CLIENT';
    if(etat == 5) titre = 'JOURNAL';
    if(etat == 6) titre = 'JOURNAL CENTRALISATEUR';
    if(etat == 7) titre = 'GRAND LIVRE GENERAL';
    if(etat == 8) titre = 'GRAND LIVRE FOURNISSEUR';
    if(etat == 9) titre = 'GRAND LIVRE CLIENT';
    $('#js_eb_titre').html(titre);

    var lien = Routing.generate('app_cloture_exercices');
    $.ajax({
        data: { dossier:$('#dossier').val(), exercices:JSON.stringify(exercices) },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('.js_chart_footer').html(data);
            //$('#js_eb_titre').html(titre + ' ' + data);
            //div_current.find('.js_chart_footer').html(data);
        }
    });

    /*titre = titre.sansAccent().toUpperCase();
    titre += ' <small>(Comptabilit\xE9 en cours '+get_derniere_mise_a_jour_ecriture()+', non encore cl\xF4tur\xE9)';

    //date anciennete
    etat = parseInt($('#js_etat').val());
    if(etat == 3 || etat == 4) titre += ' Anciennete : ' + date_anciennete.toLocaleDateString();
    titre += '</small>';

    $('#js_eb_titre').html(titre);

    dateCloture = get_fin_mois(get_clotureDossier(),exercice[0]);

    $('#js_cloture').text('cl\xF4ture : '+dateCloture.getDate()+"/"+(dateCloture.getMonth() + 1));*/
}

function get_derniere_mise_a_jour_ecriture()
{
    exercice_sel = exercice[0];
    lien = Routing.generate('etat_base_date_maj_ecriture');

    date_maj = null;

    $.ajax({
        data: { dossier:dossier,exercice:exercice_sel },
        url: lien,
        type: 'POST',
        async: false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            date_maj = data.trim();
        }
    });

    return (date_maj != '') ? 'au '+date_maj : '';
}

function eb_get_class_solde()
{
    return 'fa-check';
}

function eb_get_class_compte()
{
    return 'text-success pointer';
}

function eb_get_class_credit()
{
    return 'text-danger';
}

function eb_get_class_piece()
{
    return 'text-info pointer';
}