function show_image_a_affecter(span)
{
    $.ajax({
        data: {
            releve: span.closest('tr').find('.js_id_releve').text()
        },
        url: Routing.generate('banque_images_a_affecter'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            show_modal('<table id="js_cl_tb_affecter"></table>','Images à affecter',undefined,'modal-lg');
            var table_selected = $('#js_cl_tb_affecter'),
                w = table_selected.parent().width(),
                h = $(window).height() - 350,
                editurl = 'index.php';
            set_table_jqgrid($.parseJSON(data),h,get_col_model_image_affecter(),get_col_model_image_affecter(w),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined,false,undefined,false);
        }
    });
}

function get_col_model_image_affecter(w)
{
    var colModel1 = [];
    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'ii', index:'ii', hidden:true, classes:'js_id_image' });
        colModel1.push({ name:'i', index:'i', width:w*10/100, classes:'js_show_image_ pointer text-primary' });
        colModel1.push({ name:'d', index:'d', width:w*10/100 });
        colModel1.push({ name:'t', index:'t', width:w*20/100 });
        colModel1.push({ name:'b', index:'b', width:w*10/100 });
        colModel1.push({ name:'r', index:'r', width:w*10/100 });
        colModel1.push({ name:'tva', index:'tva', width:w*10/100 });
        colModel1.push({ name:'ht', index:'ht', width:w*10/100, align:'right', formatter: function(v) { return number_format(v, 2, ',', ' ') } });
        colModel1.push({ name:'mtva', index:'mtva', width:w*10/100, align:'right', formatter: function(v) { return number_format(v, 2, ',', ' ') } });
        colModel1.push({ name:'ttc', index:'ttc', width:w*10/100, align:'right', formatter: function(v) { return number_format(v, 2, ',', ' ') } });
    }
    else
    {
        colModel1 = [
            'id  image',
            'Image',
            'Date facture',
            'Tiers',
            'Bilan',
            'Résultat',
            'Cpt. Tva',
            'Mt. HT',
            'Mt. Tva',
            'Mt. TTC'
        ];
    }
    return colModel1;
}

function charger_analyse()
{
    // if( $('#dossier option:selected').text().trim() == '' ||
    //     $('#dossier option:selected').text().trim().toUpperCase() == 'TOUS' ||
    //     $('#dossier').length <= 0 ||
    //     parseInt($('#js_exercice').val()) == 0
    // ){
    //     show_info('NOTICE','CHOISIR UN DOSSIER ET UN EXERCICE','error');
    //     return;
    // }

    var banque = $('#js_banque').val().trim(),
        banque_compte = $('#js_num_compte_hidden').attr('data-id');

    if(banque == '') banque = $('#js_zero_boost').val();

    // var new_table = '<table id="js_tb_analyse"></table>';
    // $('#tab-releve .panel-body').html(new_table);
    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            exercice: $('#js_exercice').val(),
            banque: banque,
            banque_compte: banque_compte
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
            var dataObject = $.parseJSON(data),
                //max_libelle = dataObject.ml,
                datas = dataObject.d,
                // table_selected = $('#js_tb_analyse'),
                table_selected = $('#js_releve_liste'),
                w = table_selected.parent().width(),
                h = $(window).height() - 100,
                editurl = 'index.php';
            $($('#js_id_flottante_hidden').html()).insertBefore(table_selected);
            set_table_jqgrid(datas,h,get_col_model(),get_col_model(w),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined,false,undefined,false);
        }
    });
}

function get_col_model(w)
{
    var colModel1 = [];
    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'id', index:'id', hidden:true, classes:'js_id_releve' });
        colModel1.push({ name:'i', index:'i', sortable:true, width:  w*8/100, align:'center', classes:'js_show_image_ pointer text-primary' });
        colModel1.push({ name:'imi', index:'imi', hidden:true, classes:'js_id_image' });
        colModel1.push({ name:'d', index:'d', sortable:false, width:  w*10/100, align:'center' });
        colModel1.push({ name:'l', index:'l', sortable:false, width:  w*25/100 });
        colModel1.push({ name:'m', index:'m', sortable:false, width:  w*10/100, align:'right', formatter: function(v) { return '<span class="'+ ((v < 0) ? 'text-danger' : '') +'">'+ number_format(v, 2, ',', ' ') +'</span>'; } });
        colModel1.push({ name:'s', index:'s', sortable:true, width:  w*10/100, formatter: function (v) { return status_formatter(v) ; } });
        colModel1.push({ name:'is', index:'is', sortable:false, width:  w*10/100, align:'center', formatter: function (v) {  return (v.trim() != '') ? '<span class="pointer text-primary js_show_image_soeur">'+ v +'</span>' : '' } });
        colModel1.push({ name:'isi', index:'isi', hidden:true, classes:'js_id_image_soeur' });
        colModel1.push({ name:'t', index:'t', sortable:false, width:  w*10/100, align:'center' });
        colModel1.push({ name:'n', index:'n', sortable:false, width:  w*10/100, align:'center' });
        colModel1.push({ name:'s', index:'s', sortable:false, width:  w*7/100, align:'center', formatter: function (v) { return status_details(v) ;} });
    }
    else
    {
        colModel1 = [
            'id releve',
            'Image',
            'id image',
            'Date Operation',
            'Libelle',
            'Mouvements',
            'statut',
            'Num piece',
            'id piece',
            'Tiers',
            'Nature',
            'Ecritures'];
    }
    return colModel1;
}

function status_formatter(v)
{
    var status = [
        '<span class="">Libelle&nbsp;non&nbsp;identifi&eacute;</span>',
        '<span class="text-success">Pi&egrave;ce&nbsp;manquante</span>',
        'Inconnu','<span class="text-primary">Pi&egrave;ce&nbsp;affect&eacute;e</span>',
        '<span class="label label-danger pointer js_show_image_a_affecter">Pi&egrave;ce&nbsp;&agrave;&nbsp;affecter</span>'
    ];
    return status[v];
}

function status_details(v)
{
    return (v == 3) ? '<i class="js_show_details pointer fa fa-eyedropper" aria-hidden="true"></i>' : '';
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