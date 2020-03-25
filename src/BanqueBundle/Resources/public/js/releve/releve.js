var key_numeric = ['0','1','2','3','4','5','6','7','8','9','.'],
    scroll_position = 0;

$(document).on('click','#jqgh_js_id_releve_liste_s',function(){
    //alert('ici');
});

$(document).on('click','.js_refresh',function(){
    $('.' + class_tr_edited).removeClass(class_tr_edited);
    $(this).closest('tr').addClass(class_tr_edited);

    var releve = $(this).closest('tr').attr('id');
    $.ajax({
        data: { 'releve' : releve },
        type: 'POST',
        url: Routing.generate('banque_refresh'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            update_row();
            /*charger_analyse();
            show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
            show_info('REINITIALISATION DE LA LIGNE','Merci de patienter','warning');*/
        }
    });
});

$(document).on('mouseover','.js_refresh',function(){
    $(this).addClass('fa-spin');
});

$(document).on('mouseout','.js_refresh',function(){
    $(this).removeClass('fa-spin');
});

function charger_analyse()
{
    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            exercice: $('#exercice').val(),
            banque: $('#js_banque').val(),
            banque_compte: $('#js_banque_compte').val(),
            action: $('#js_id_action').val()
        },
        url: Routing.generate('banque_analyse'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#js_id_releve_liste').closest('.bande').html(data);return;
            //$('#js_id_releve_liste').closest('.bande').html('<table id="js_id_releve_liste"></table>');

            //$('#test').html(data);return;
            var dataObject = $.parseJSON(data),
                //max_libelle = dataObject.ml,
                datas = dataObject.d,
                // table_selected = $('#js_tb_analyse'),
                table_selected = $('#js_id_releve_liste'),
                w = table_selected.parent().width(),
                h = $(window).height() - 230,
                editurl = Routing.generate('banque_releve_edit');
            set_table_jqgrid(datas,h,get_col_model(),get_col_model(w),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);
            //group_head_jqgrid('js_id_releve_liste',getGroupHeaders(),true);
            $('#gbox_js_id_releve_liste').find('.ui-jqgrid-bdiv').scrollTop(scroll_position);
            $($('#js_id_flottante_hidden').html()).insertBefore(table_selected,undefined);
            charger_stat();
        }
    });
}

function get_col_model(w)
{
    var colModel1 = [];
    var editOptNumeric = { maxlengh: 30, dataInit: function (element) {$(element).keypress(function (e) { return key_numeric.in_array(e.key.toString()); });} },
        banqueSelect = ($('#js_banque option:selected').text() !== '' && $('#js_banque option:selected').text().toUpperCase() !== 'TOUS'),
        banqueCompteSelect = ($('#js_banque_compte option:selected').text() !== '' && $('#js_banque_compte option:selected').text().toUpperCase() !== 'TOUS');

    if(typeof w !== 'undefined')
    {
        var w_l = 35;

        if (!banqueSelect)
        {
            colModel1.push({ name:'b', index:'b', sortable:true, width:  13 * w/100 });
            w_l -= 13;
        }
        if (!banqueCompteSelect)
        {
            colModel1.push({ name:'bc', index:'bc', sortable:true, width:  13 * w/100 });
            w_l -= 13;
        }

        colModel1.push({ name:'i', index:'i', sortable:true, width:  7 * w/100, align:'center', classes:'js_show_image_ pointer text-primary' });
        colModel1.push({ name:'d', index:'d', sortable:true, width:  7 * w/100, align:'center', sorttype: 'date', formatter: 'date', formatoptions: {srcformat: 'd/m/Y', newformat: 'd/m/Y'} });
        colModel1.push({ name:'l', index:'l', sortable:true, width:  w_l * w/100, classes:'js_show_add_cle pointer' });
        colModel1.push({ name:'m', index:'m', sortable:true, width:  7 * w/100, sorttype: 'number', classes:'js_cl_ttc', align:'right', formatter: function(v) { return '<b class="'+ ((v < 0) ? 'text-danger' : 'text-primary') +'">'+ number_format(v, 2, ',', ' ') +'</b>'; } });
        colModel1.push({ name:'s', index:'s', sortable:true, width:  20 * w/100, classes: 'is_show_image_temp_', formatter: function (v) { return status_formatter(v) ; }, sorttype: function (cellValue, rowData) { return rowData.ss; } });
        colModel1.push({ name:'ss', index:'ss', sortable:true, classes:'cl_ss', hidden:true });
        colModel1.push({ name:'t', index:'t', sortable:true, width:  8 * w/100, editable:true, edittype:'select', formatter:'select', editoptions: { value:tiersString, dataInit: function (elem) {/*data init*/}}});
        colModel1.push({ name:'c', index:'c', sortable:true, width:  8 * w/100, editable:true, edittype:'select', editoptions:{value:chargeString}, formatter:'select' });
        colModel1.push({ name:'tva', index:'tva', sortable:true, width:  8 * w/100, editable:true, edittype:'select', editoptions:{value:tvaString}, formatter:'select' });
        colModel1.push({ name:'imi', index:'imi', hidden:true, classes:'js_id_image' });
    }
    else
    {
        if (!banqueSelect)
            colModel1.push('Banque');
        if (!banqueCompteSelect)
            colModel1.push('Compte');

        colModel1.push('Image');
        colModel1.push('Date Op\xB0');
        colModel1.push('Libelle');
        colModel1.push('Mouvements');
        colModel1.push('Rapprochement');
        colModel1.push('status');
        colModel1.push('Bilan');
        colModel1.push('Resultat');
        colModel1.push('Tva');
        colModel1.push('id image');
    }
    return colModel1;
}

function getGroupHeaders()
{
    var colM = [];
    colM.push({startColumnName: 't', numberOfColumns: 3, titleText: '<strong>Compte</strong>'});
    colM.push({startColumnName: 'tm', numberOfColumns: 3, titleText: '<strong>Montant</strong>'});
    return colM;
}

function status_formatter(v,hidden)
{
    hidden = typeof hidden === 'undefined' ? false : hidden;
    if($.isNumeric(v.s))
    {
        var status =
         [
             '<span>&agrave;&nbsp;cat&eacute;goriser</span>', // js_show_rapprochement_manuel
             '<span class="text-success">Piece trouvée</span>' // js_show_rapprochement_manuel
         ];

         if (hidden && v.s !== 4 && v.s !== 5) return '';
         return status[v.s];

        /*var status =
        [
            '<span>&agrave;&nbsp;cat&eacute;goriser</span>', // js_show_rapprochement_manuel
            '<span class="text-success">Pi&egrave;ce&nbsp;manquante</span>', // js_show_rapprochement_manuel
            'Inconnu',
            '<span class="text-primary pointer">Pi&egrave;ce&nbsp;affect&eacute;e</span>',
            '<span class="text-danger pointer js_show_image_a_affecter pointer">Pi&egrave;ce&nbsp;&agrave;&nbsp;affecter</span>',
            '<span class="text-warning pointer js_show_cle_a_affecter">Cl&eacute;s&nbsp;&agrave;&nbsp;valider</span>',
            '<span class="type js_cl_edit_cle pointer" data-id="'+ ((v.s === 6) ? v.ci : '') +'"><i class="fa fa-key" aria-hidden="true"></i>&nbsp;'+ ((v.s === 6) ? v.c : '') +'</span>',
            '<span class="type" data-type="1"><span class="pointer js_show_image_affecter" data-id="'+ v.ii +'"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;'+ ((v.s === 7) ? v.i : '') +'</span>&nbsp;&nbsp;<i class="fa fa-times js_annuler_imputation pointer" aria-hidden="true"></i></span>',
            '<span class="text-info">&agrave;&nbsp;cat&eacute;goriser</span>&nbsp;&nbsp;<i class="fa fa-refresh js_refresh pointer" aria-hidden="true"></i>',
            '<span class="type" data-type="2"><span class="pointer js_show_image_affecter" data-id="'+ v.ids +'"><i class="fa fa-files-o" aria-hidden="true"></i>&nbsp;'+ ((v.s === 9) ? v.i : '') +'</span>&nbsp;&nbsp;<i class="fa fa-times js_annuler_imputation pointer" aria-hidden="true"></i></span>',
            '<span class="text-info">&agrave;&nbsp;cat&eacute;goriser</span>&nbsp;&nbsp;<i class="fa fa-refresh js_refresh pointer" aria-hidden="true"></i>'
        ];

        if (hidden && v.s !== 4 && v.s !== 5) return '';
        return status[v.s];*/
    }
    else
    {
        return v;
    }
}

function status_details(v)
{
    return (v === 3) ? '<i class="js_show_details pointer fa fa-eyedropper" aria-hidden="true"></i>' : '';
}

function show_details_releve(td)
{
    var releve = td.closest('tr').find('.js_id_releve').text().trim();
    $.ajax({
        data: {
            releve: releve
        },
        url: Routing.generate('banque_details_releve'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
        }
    });
}

function update_row()
{
    var id = $('.' + class_tr_edited).attr('id');
    $.ajax({
        data: { releve: id },
        url: Routing.generate('banque_tr_updated'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);

            var newData = $.parseJSON(data);
            newData.id = id;
            $('#js_id_releve_liste').jqGrid('setRowData', id, newData);
            charger_stat();
            close_modal();

            show_info('SUCCES','Enregistrement bien Enregistrée avec succès');
        }
    });
}