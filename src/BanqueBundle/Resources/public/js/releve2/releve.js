/**
 * Created by SITRAKA on 30/07/2018.
 */

var key_numeric = ['0','1','2','3','4','5','6','7','8','9','.'],
    scroll_position = 0,
    results = [],
    can_close_modal = true,
    limit_query = 300,
    can_lanch = true,
    jg_table_releve = null;

$(document).ready(function(){
//<editor-fold > desc="Refresh"
    $(document).on('click','.js_refresh',function(){
        $('.' + class_tr_edited).removeClass(class_tr_edited);
        $(this).closest('tr').addClass(class_tr_edited);

        var releve = $(this).closest('tr').attr('id');
        $.ajax({
            data: { 'releve' : releve },
            type: 'POST',
            url: Routing.generate('banque2_refresh'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                update_row();
            }
        });
    });
    $(document).on('mouseover','.js_refresh',function(){
        $(this).addClass('fa-spin');
    });
    $(document).on('mouseout','.js_refresh',function(){
        $(this).removeClass('fa-spin');
    });
//</editor-fold>

    $(document).on('change','#id_detailler_obs',function(){
        go();
    });

    $(document).on('click', '.cl_edit_libelle', function(e){
        $('.' + class_tr_edited).removeClass(class_tr_edited);
        var releve = $(this).closest('tr').addClass(class_tr_edited).attr('id');

        $.ajax({
            data: {
                releve: releve,
                action: 0
            },
            url: Routing.generate('banque2_libelle_show_edit'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                show_modal(data,'Modification Libellé');
            }
        });
    });

    $(document).on('click','#id_save_libelle',function(){
        var libelle = $('#id_libelle-rel').val().trim(),
            releve = $('#js_releve_selected').attr('data-id');

        $.ajax({
            data: {
                releve: releve,
                action: 1,
                libelle: libelle
            },
            url: Routing.generate('banque2_libelle_show_edit'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                show_info('Succès','Modification bien enregistrée avec succès');
                update_row();
                close_modal();
            }
        });
    });
});

function go()
{
    if ($('#dossier option:selected').text().trim() === '')
    {
        $('#dossier').closest('.form-group').addClass('has-error');
        //show_info('NOTICE','Choisir le dossier','error');
        vider_table();
        return;
    }
    else $('#dossier').closest('.form-group').removeClass('has-error');

    /*if ($('#js_banque_compte option:selected').text().trim() === '' || $('#js_banque_compte option:selected').text().toUpperCase().trim() === 'TOUS')
    {
        $('#js_banque_compte').closest('.form-group').addClass('has-error');
        show_info('NOTICE','Choisir le numero de COMPTE','error');
        vider_table();
        return;
    }
    else $('#js_banque_compte').closest('.form-group').removeClass('has-error');*/

    scroll_position = 0;
    charger_analyse();
    charger_control();
}

function charger_analyse()
{
    //if (!can_lanch) return;

    can_lanch = false;

    results = [];
    var banque_comptes = [],i;
    if ($('#js_banque_compte option:selected').text().trim() === '' || $('#js_banque_compte option:selected').text().toUpperCase().trim() === 'TOUS')
    {
        $('#js_banque_compte option').each(function()
        {
            if ($(this).text().trim() !== '' && $(this).text().toUpperCase().trim() !== 'TOUS')
                banque_comptes.push($(this).attr('value'));
        });
    }
    else banque_comptes.push($('#js_banque_compte').val());

    if (banque_comptes.length === 0)
    {
        vider_table();
        return;
    }

    ajax_get_data(banque_comptes,0,0);
}

function ajax_get_data(banque_comptes,index,offset)
{
    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            exercice: $('#exercice').val(),
            banque: $('#js_banque').val(),
            banque_compte: banque_comptes[index],
            action: $('#js_id_action').val(),
            obs: $('#id_detailler_obs').is(':checked') ? 1 : 0,
            limit_query: limit_query,
            offset: offset
        },
        url: Routing.generate('banque2_analyse'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);

            var dataObject = $.parseJSON(data),
                datas = dataObject.d;
            if (index === banque_comptes.length - 1 && datas.length < limit_query)
            {
                $('#id-container').html('<table id="js_id_releve_liste"></table>');
                results = results.concat(datas);
                can_lanch = true;
                set_table(results);
            }
            else
            {
                results = results.concat(datas);
                var i = index;

                if (datas.length < limit_query)
                {
                    ajax_get_data(banque_comptes,i + 1,0);
                }
                else
                {
                    ajax_get_data(banque_comptes,i,offset + 1);
                }
            }
        }
    });
}

function set_table(datas)
{
    var table_selected = $('#js_id_releve_liste'),
        w = table_selected.parent().width(),
        h = $(window).height() - 210,
        editurl = Routing.generate('banque_releve_edit');

    jg_table_releve = set_table_jqgrid(datas,h,get_col_model(),get_col_model(w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);
    $('#gbox_js_id_releve_liste').find('.ui-jqgrid-bdiv').scrollTop(scroll_position);
    charger_stat();
    activer_qtip();
}

function get_col_model(w)
{
    var colM = [];
    var editOptNumeric = { maxlengh: 30, dataInit: function (element) {$(element).keypress(function (e) { return key_numeric.in_array(e.key.toString()); });} },
        banqueSelect = ($('#js_banque option:selected').text() !== '' && $('#js_banque option:selected').text().toUpperCase() !== 'TOUS'),
        banqueCompteSelect = ($('#js_banque_compte option:selected').text() !== '' && $('#js_banque_compte option:selected').text().toUpperCase() !== 'TOUS');

    if(typeof w !== 'undefined')
    {
        var w_l = 27,
            w_b = 8;

        if (!banqueSelect && !banqueCompteSelect)
        {
            colM.push({ name:'b', index:'b', sortable:true, width:  w_b * w/100 });
            w_l -= w_b;
        }
        if (!banqueCompteSelect)
        {
            colM.push({ name:'bc', index:'bc', align:'center', sortable:true, width:  w_b * w/100 });
            w_l -= w_b;
        }

        colM.push({ name:'i', index:'i', sortable:true, width: 7 * w/100, align:'center', classes:'js_show_image_ pointer text-primary' });
        colM.push({ name:'d', index:'d', sortable:true, width: 7 * w/100, align:'center', sorttype: 'date', formatter: 'date', formatoptions: {srcformat: 'd/m/Y', newformat: 'd/m/Y'} });
        colM.push({ name:'l', index:'l', sortable:true, width: w_l * w/100, classes:'pointer', formatter:function(v){ return '<i class="fa fa-pencil-square-o cl_edit_libelle" aria-hidden="true"></i>' + '&nbsp;<span class="js_show_add_cle">'+v+'</span>' } });

        //colM.push({ name:'find', index:'find', sortable:true, width: 20 });

        colM.push({ name:'m', index:'m', sortable:true, width: 7 * w/100, sorttype: 'number', classes:'js_cl_ttc', align:'right', formatter: function(v) { return '<b class="'+ ((v < 0) ? 'text-danger' : 'text-primary') +'">'+ number_format(v, 2, ',', ' ') +'</b>'; } });
        colM.push({ name:'ss2', index:'ss2', align:'center', sortable:true, width:  3 * w/100, formatter: function (v) { return status_formatter_icon(v) ; }, sorttype: function (cellValue, rowData) { return rowData.ss; } });
        colM.push({ name:'s', index:'s', sortable:true, width: 16 * w/100, classes: 'is_show_image_temp_', formatter: function (v) { return status_formatter(v) ; }, sorttype: function (cellValue, rowData) { return rowData.ss; } });
        colM.push({ name:'s', index:'s', width: 2 * w/100, formatter: function (v) { return lettrage_manuel(v) ; } });
        colM.push({ name:'nat', index:'nat', sortable:true,align:'center', width: 6 * w/100, formatter: function (v) { return nature_formatter(v) ; }, sorttype: function (cellValue, rowData) { return rowData.n; } });
        //colM.push({ name:'ss3', index:'ss3', width: 2 * w/100 });
        colM.push({ name:'ss', index:'ss', sortable:true, classes:'cl_ss', hidden:true });
        colM.push({ name:'t', index:'t', sortable:true, width: 7 * w/100, formatter: function (v) { return compte_formatter(v) ; } });
        colM.push({ name:'c', index:'c', sortable:true, width: 7 * w/100, formatter: function (v) { return compte_formatter(v) ; } }); //, dataInit: function (elem) {} }
        colM.push({ name:'tva', index:'tva', sortable:true, width: 7 * w/100, formatter: function (v) { return compte_formatter(v) ; } });
        colM.push({ name:'ss3', index:'ss3', sortable:true, width: 2 * w/100, formatter: function(v){ return (v === 0 || v === 5) ? '<i class="pointer fa fa-eyedropper cl_show_edit_releve_compte" aria-hidden="true"></i>' : ''; } });

        /*colM.push({ name:'t', index:'t', sortable:true, width:  9 * w/100, editable:true, edittype:'select', formatter:'select', editoptions:{value:tiersString} });
        colM.push({ name:'c', index:'c', sortable:true, width:  12 * w/100, editable:true, edittype:'select', formatter:'select', editoptions:{value:chargeString,multiple:true} }); //, dataInit: function (elem) {} }
        colM.push({ name:'tva', index:'tva', sortable:true, width:  9 * w/100, editable:true, edittype:'select', formatter:'select', editoptions:{value:tvaString} });*/

        colM.push({ name:'imi', index:'imi', hidden:true, classes:'js_id_image' });
        colM.push({ name:'cleWP', index:'cleWP', hidden:true, classes:'cl_cleWP' });
        colM.push({ name:'italic', index:'italic', hidden:true, classes:'cl_italic_row', formatter: function(v) { return (typeof v !== 'undefined' && parseInt(v) === 1) ? '1' : '0'} });
        colM.push({ name:'cde', index:'cde', hidden:true, classes:'cl_cde_id', formatter: function(v) { return (typeof v !== 'undefined') ? v : $('#js_zero_boost').val() } });
        colM.push({ name:'r_goup', index:'r_goup', hidden:true, formatter: function(v) { return '<i class="r_gr r_gr_'+v+'">'+v+'</i>' } });
        colM.push({ name:'is_stat', index:'is_stat', hidden:true, classes:'in_stat', formatter: function(v) { return typeof v !== 'undefined' ? v : 1 } });
    }
    else
    {
        if (!banqueSelect && !banqueCompteSelect)
            colM.push('Banque');
        if (!banqueCompteSelect)
            colM.push('Compte');

        colM.push('Image');
        colM.push('Date Op\xB0');
        colM.push('Libelle');

        //colM.push('Find');

        colM.push('Mouvements');
        colM.push('');
        colM.push('Rapprochement');
        colM.push('');
        colM.push('Nature');
        colM.push('status');
        colM.push('Bilan');
        colM.push('Resultat');
        colM.push('Tva');
        colM.push('');
        colM.push('id image');
        colM.push('cl_cleWP');
        colM.push('italic');
        colM.push('cde');
        colM.push('');
        colM.push('');
    }
    return colM;
}

function nature_formatter(v)
{
    if (parseInt(v.s) === 1) return '';

    return '' +
        '<select class="input-in-jqgrid cl_nature_releve">' +
            '<option value="0" '+((parseInt(v.n) === 0) ? 'selected' : '')+'></option>' +
            '<option value="1" '+((parseInt(v.n) === 1) ? 'selected' : '')+'>Rem BQ</option>' +
            '<option value="2" '+((parseInt(v.n) === 2) ? 'selected' : '')+'>R LCR</option>' +
            '<option value="3" '+((parseInt(v.n) === 3) ? 'selected' : '')+'>R CB</option>' +
        '</select>';
}

function lettrage_manuel(v)
{
    if (v === null) return '';

    var s = parseInt(v.s);
    if (typeof v.rExt !== 'undefined' && (s === 1 || s === 2)) return '';

    if (typeof v.ecla !== 'undefined' && parseInt(v.ecla) === 1) return '';

    else if($.isNumeric(v.s) && parseInt(v.s) !== 1) return '<span class="cl_desiquilibre pointer qtip_new" title="Lettrage Manuel"><i class="fa fa-tags" aria-hidden="true"></i></span>';

    return '';
}

function status_r_ext(v)
{
    var s = parseInt(v.s);

    if (s === 1)
        return '<span class="text-success pointer cl_det_imp_re_ext" data-images="'+v.imgs+'">'+v.libelle+'</span>';
    else if (s === 2)
        return '<span class="text-warning pointer js_show_image_a_affecter has_cde">Pi&egrave;ce&nbsp;&agrave;&nbsp;valider</span>';
    else return '';
}

function status_formatter(v,hidden)
{
    if (v === null) return '';

    if (typeof v.rExt !== 'undefined')
    {
        return status_r_ext(v);
    }

    var percent_lettrage = '',
        class_piece_a_valider = 'text-success';

    if (v.sl !== null && parseInt(v.sl.total) > 0)
    {
        percent_lettrage = ' ('+v.sl.lettre+'/'+v.sl.total+')';
        if (parseInt(v.sl.lettre) !== parseInt(v.sl.total))
            class_piece_a_valider = 'text-danger';
    }

    hidden = typeof hidden === 'undefined' ? false : hidden;
    if($.isNumeric(v.s))
    {
        var statPieceCle = '';
        if (parseInt(v.s) === 2 && v.sPiece !== null)
            statPieceCle = '<i class="fa fa-hand-o-right" aria-hidden="true"></i>&nbsp;<span class="text-warning pointer js_show_image_a_affecter">Pi&egrave;ce&nbsp;&agrave;&nbsp;valider</span>';

        var statNonLettrable = '';
        if (typeof v.inl !== 'undefined' && v.inl.length > 0)
            statNonLettrable = '<i class="fa fa-thumbs-down text-danger" aria-hidden="true"></i>&nbsp;<span class="text-danger pointer js_show_non_lettrable">Pi&egrave;ce&nbsp;non&nbsp;lettrable</span>';

        var libelle_pm,
            ids = '',
            noms = '';

        if (v.isoeur.length === 0) libelle_pm = 'Pièce&nbsp;manquante';
        else
        {
            for (var il = 0; il < v.isoeur.length; il ++)
            {
                ids += v.isoeur[il].id;
                noms += v.isoeur[il].nom;

                if (il !== v.isoeur.length - 1)
                {
                    ids += ';';
                    noms += ';';
                }
            }

            libelle_pm = (v.isoeur.length === 1) ? v.isoeur[0].n : 'multiple';
        }

        /**
         * 0 : a categorise
         * 1 : flaguer piece
         * 2 : flaguer cle
         * 3 : piece trouve
         * 4 : cle trouve
         * 5 : piece manquante
         */
        var status =
            [
                '<span>Pièce&nbsp;manquante&nbsp;'+ ((v.s === 0 && v.t !== 0) ? '<i class="fa fa-refresh js_refresh pointer qtip_new" title="Réactiver recherche par pièce et/ou clé" aria-hidden="true"></i>' : '' ) +'</span>&nbsp;' + statNonLettrable, // js_show_rapprochement_manuel
                '<span class="'+class_piece_a_valider+' pointer cl_detail_imputation" data-type="'+ v.t +'" data-id="'+ v.id +'" data-it="'+ v.it +'">'+ ((v.s === 1) ? v.l : '') + percent_lettrage +'</span>&nbsp;<i class="text-success pointer cl_annuler_imputation hidden fa fa-times" aria-hidden="true"></i>',
                '<span class="pointer text-info js_cl_edit_cle" data-id="'+ v.id +'" data-tous_dossier="'+ v.it +'">'+((v.s === 2 ) ? v.l : '')+'</span>&nbsp;' + statPieceCle + '&nbsp;' + statNonLettrable,
                '<span class="text-warning pointer js_show_image_a_affecter">Pi&egrave;ce&nbsp;&agrave;&nbsp;valider</span>&nbsp;' + statNonLettrable,
                '<span class="text-danger pointer js_show_cle_a_affecter">Cl&eacute;s&nbsp;&agrave;&nbsp;valider</span>&nbsp;' + statNonLettrable,
                '<span class="'+((v.isoeur.length === 0) ? 'text-danger' : 'pointer cl_soeurs text-primary')+'" data-noms="'+noms+'" data-ids="'+ ids +'">'+libelle_pm+'</span>&nbsp;' + statNonLettrable
            ];

        if (hidden) return '';
        return status[v.s];
    }
    else return v;
}

function status_formatter_icon(v,hidden)
{
    if (v === null) return '';

    hidden = typeof hidden === 'undefined' ? false : hidden;
    if($.isNumeric(v.s))
    {
        if (typeof v.rExt !== 'undefined')
        {
            var st = parseInt(v.s);
            if (st === 1)
                return '<span class="text-success qtip_new" title="Lettré"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></span>';
            else if (st === 2)
                return '<span class="text-warning qtip_new" title="Pièce(s) à lettrer"><i class="fa fa-file-powerpoint-o" aria-hidden="true"></i></span>';
            else return '';
        }

        if (v.s === 1 && v.diff !== 0)
            return '<span class="text-danger cl_desiquilibre pointer qtip_new" title="Lettré mais déséquilibré"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>';

        /**
         * 0 : a categorise
         * 1 : flaguer piece
         * 2 : flaguer cle
         * 3 : piece trouve
         * 4 : cle trouve
         * 5 : piece manquante
         */
        var status =
            [
                '<i class="fa fa-question qtip_new" title="Ni pièce, ni clé" aria-hidden="true"></i>',
                '<span class="text-success qtip_new" title="Lettré">' + ((v.s === 1) ? ((v.t !== 1) ? '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>' : '<i class="fa fa-files-o" aria-hidden="true"></i>') : '') + '</span>',
                '<i class="fa fa-key text-info qtip_new" aria-hidden="true" title="Affecté par Clé"></i>',
                '<span class="text-warning qtip_new" title="'+(((v.s === 3) ? v.nb : 0) + ' pièce(s) à lettrer')+'"><i class="fa fa-file-powerpoint-o" aria-hidden="true"></i></span>',
                '<span class="text-danger qtip_new" title="'+(((v.s === 4) ? v.nb : 0) + ' clé à valider')+'"><i class="fa fa-file-archive-o" aria-hidden="true"></i></span>',
                '<i class="fa fa-question qtip_new" title="Pièce Manquante" aria-hidden="true"></i>'
            ];

        if (hidden) return '';
        return status[v.s];
    }
    else
    {
        return v.s;
    }
}

function update_row(id)
{
    id = (typeof id === 'undefined') ? $('.' + class_tr_edited).attr('id') : id;
    var tr = $('tr[id="'+ id +'"]'),
        tr_prev = tr.prev('tr'),
        r_group = parseInt(tr.find('.r_gr').text()),
        not_stop = true;

    while (not_stop)
    {
        if (tr_prev.hasClass('jqgfirstrow')) not_stop = false;
        else if (tr_prev.find('.r_gr_'+r_group).length === 0) not_stop = false;
        else tr_prev = tr_prev.prev('tr');
    }

    var row_num = tr_prev.hasClass('jqgfirstrow') ? 0 : 1;

    $.ajax({
        data: { releve: id, obs: $('#id_detailler_obs').is(':checked') ? 1 : 0  },
        async: true,
        url: Routing.generate('banque2_tr_updated'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);

            $('.r_gr_'+r_group).each(function(){
                var id_ = $(this).closest('tr').attr('id');
                $('#js_id_releve_liste').jqGrid('delRowData',id_);
            });

            $.each($.parseJSON(data), function( index, value ) {
                if (row_num === 0)
                {
                    $('#js_id_releve_liste').jqGrid('addRowData', value.id, value, 'first');
                    row_num++;
                    tr_prev = $('tr[id="'+ value.id +'"]')
                }
                else
                {
                    $('#js_id_releve_liste').addRowData(value.id,value,'after',tr_prev.attr('id'));
                    tr_prev = tr_prev.next('tr');
                }
            });

            charger_stat();
            set_italic_eclater();
            if (can_close_modal) close_modal();
            can_close_modal = true;
            activer_qtip();
            show_info('SUCCES','Enregistrement bien Enregistrée avec succès');

            //$('#test_test').html(data);return;
            /*var newData = $.parseJSON(data);
            newData.id = id;
            $('#js_id_releve_liste').jqGrid('setRowData', id, newData);
            charger_stat();
            if (can_close_modal) close_modal();
            can_close_modal = true;
            activer_qtip();
            show_info('SUCCES','Enregistrement bien Enregistrée avec succès');*/
        }
    });
}

function activer_qtip()
{
    $('.qtip_new').qtip({
        content: {
            text: function (event, api) {
                return $(this).removeClass('qtip_new').attr('title');
            }
        },
        position: {my: 'bottom center', at: 'top left'},
        style: {
            classes: 'qtip-dark qtip-shadow'
        }
    });
}