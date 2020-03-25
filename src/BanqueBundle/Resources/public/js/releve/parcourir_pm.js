$(document).on('click','.js_show_rapprochement_manuel',function(){
    //montantTTC = number_fr_to_float(span.closest('tr').find('.js_cl_ttc').text());
    montantTTC = number_fr_to_float($(this).closest('tr').find('.js_cl_ttc').text());
    var releve = $(this).closest('tr').find('.js_id_releve').text();
    $.ajax({
        data: { releve: releve },
        type: 'POST',
        url: Routing.generate('banque_rapprochement_manuel'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);

            /*var options = { modal: false, resizable: true,title: 'Tiers & Cles' };
            modal_ui(options,data);            */

            show_modal(data,'Rapprochement Manuel',undefined,'modal-lg');
            charger_proposition_cle();
        }
    });
});

$(document).on('change','#js_id_table_propositon_cle .js_td_check_affecter',function(){
    charger_ecriture_cle_temp();
});

function charger_ecriture_cle_temp()
{
    var table_selected = $('#js_id_table_ecriture_temp'),
        w = table_selected.parent().width(),
        editurl = '#',
        datas = [];

    $('#js_id_table_propositon_cle').find('tr').each(function(){
        if ($(this).hasClass('jqgfirstrow') || !$(this).find('.js_piece_item').is(':checked')) return;
        var dataTemps = getData($(this));
        for (var i = 0; i < dataTemps.length; i++) datas.push(dataTemps[i]);
    });

    set_table_jqgrid(datas,h_table / 2,get_col_model_image_affecter(undefined,true),get_col_model_image_affecter(w,true),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined,false,undefined,false);

    set_ecart('#js_id_table_ecriture_temp');
}

function charger_proposition_cle()
{
    var releve = $('#js_id_table_propositon_cle_contanier').attr('data-id');
    $.ajax({
        data: { releve: releve },
        type: 'POST',
        url: Routing.generate('banque_show_propositions'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            var table_selected = $('#js_id_table_propositon_cle'),
                w = table_selected.parent().width(),
                h = 150,
                editurl = '#';

            set_table_jqgrid($.parseJSON(data),h,get_col_model_cle(),get_col_model_cle(w),table_selected,'hidden',w,editurl,false,undefined,true,{groupField : ['dt'],groupColumnShow : [false],groupText : ['<b>{0}</b> <span style="margin-right: 20px!important;" class="label label-default pull-right">{1} Occurence(s)</span>']},undefined,undefined,false,undefined,false);
        }
    });
}

function get_col_model_cle(w)
{
    var colModel1 = [];

    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'r', index:'r', hidden:true });
        colModel1.push({ name:'x', index:'x', align:'center', classes:'js_td_check_affecter', width:w*3/100, formatter: function(){ return check_box_formatter(); } });
        colModel1.push({ name:'dt', index:'dt' });
        colModel1.push({ name:'in', index:'in', classes:'js_show_image_', width:9*w/100 });
        colModel1.push({ name:'d', index:'d', width:w*9/100, classes:'js_cl_d' });
        colModel1.push({ name:'l', index:'l', width:25*w/100, classes:'js_cl_t' });
        colModel1.push({ name:'b', index:'b', classes:'js_cl_b', width:9 * w/100 });
        colModel1.push({ name:'res', index:'res', classes:'js_cl_r', width:9 * w/100 });
        colModel1.push({ name:'t', index:'t',classes:'js_cl_tva', width:9 * w/100 });
        colModel1.push({ name:'ht', index:'ht', width:w*9/100,classes:'js_cl_ht', align:'right', formatter: function(v) { return number_format(v, 2, ',', ' ') } });
        colModel1.push({ name:'mtva', index:'mtva', width:w*9/100,classes:'js_cl_mtva', align:'right', formatter: function(v) { return number_format(v, 2, ',', ' ') } });
        colModel1.push({ name:'ttc', index:'ttc', width:w*9/100,classes:'js_cl_ttc', align:'right', formatter: function(v) { return number_format(v, 2, ',', ' ') } });

        colModel1.push({ name:'ii', index:'ii', hidden:true, classes:'js_id_image' });
    }
    else
    {
        colModel1 = [
            '',
            '',
            'Dossier - Tiers',
            'Image',
            'Date fact',
            'Libelle',
            'Bilan',
            'Resultat',
            'Tva',
            'M Ht',
            'M TVA',
            'M TTC',

            ''
        ];
    }

    /*if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'a', index:'a', align:'center', classes:'js_td_check_affecter', width:w*3/100, formatter: function(){ return check_box_formatter(); } });
        colModel1.push({ name:'c', index:'c', sortable:true, width:  8 * w/100 });
        colModel1.push({ name:'ti', index:'ti', sortable:true, width:  10 * w/100 });
        colModel1.push({ name:'d', index:'d', sortable:true, width:  17 * w/100, formatter:function(v){ return v.n; } });
        colModel1.push({ name:'l', index:'l', sortable:true, width:  10 * w/100 });
        colModel1.push({ name:'m', index:'m', sortable:true, align:'right', width:  8 * w/100, formatter:function(v){ return number_format(v, 2, ',', ' ') } });
        colModel1.push({ name:'bs', index:'bs', sortable:true, width:  13 * w/100, classes:'js_cl_b', formatter: function(v){ return select_formatter(v); } });
        colModel1.push({ name:'rs', index:'rs', sortable:true, width:  13 * w/100, classes:'js_cl_r', formatter: function(v){ return select_formatter(v); } });
        colModel1.push({ name:'ts', index:'ts', sortable:true, width:  13 * w/100, classes:'js_cl_tva', formatter: function(v){ return select_formatter(v); } });
        colModel1.push({ name:'o', index:'o', sortable:true, width:  5 * w/100 });

        colModel1.push({ name:'imi', index:'imi', hidden:true, classes:'js_id_image' });
        colModel1.push({ name:'tv', index:'tv', hidden:true, classes:'js_tva_taux' });
    }
    else
    {
        colModel1 = [
            '',
            'Cle',
            'Tiers',
            'Dossier',
            'Libelle',
            'M TTC',
            'Bilan',
            'Resultat',
            'Tva',
            'Occurence',

            '',
            'Taux tva'
        ];
    }*/
    return colModel1;
}

function select_formatter(v)
{
    var i,
        sel = '<select class="input-in-jqgrid">';

    for (i = 0; i < v.length; i++)
    {
        sel += '<option data-id="' + v[i].id + '" data-type="' + v[i].t + '">' + v[i].c + ' - ' + v[i].i + '</option>';
    }

    return sel + '</select>';
}

/*$(document).on('click','.js_cl_accordion',function(){
    if ($(this).hasClass('js_piece') && !$(this).hasClass('collapsed'))
    {
        show_pieces_a_affecter();
    }
});*/

/*$(document).on('click','.js_parcourir_pm',function(){
    show_parcourir_pm($(this));
});

$(document).on('click', '#btn-envoi-pm', function (event) {
    event.preventDefault();

    $('#js_envoi_pm').fileinput('upload');

});

function show_parcourir_pm(td)
{
    var releve = td.closest('tr').find('.js_id_releve').text().trim();

    $.ajax({
        data: { releve: releve },
        type: 'POST',
        url: Routing.generate('banque_parcourir_pm'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_modal(data,'Pièce manquante');

            $('#js_envoi_pm').fileinput({
                language: 'fr',
                theme: 'fa',
                uploadAsync: false,
                showPreview: false,
                showUpload: false,
                showRemove: false,
                fileTypeSettings: {
                    image: function(vType, vName) {
                        return (typeof vType !== "undefined") ? vType.match('image.*') : vName.match(/\.(pdf|gif|png|jpe?g)$/i);
                    },
                    text: function(vType, vName) {
                        return typeof vType !== "undefined" && vType.match('text.*') || vName.match(/\.(txt|xls|xlsx|doc|docx|ppt|pptx|csv)$/i);
                    },
                    pdf: function(vType, vName) {
                        return typeof vType !== "undefined" && vType.match('pdf');
                    }
                },
                allowedFileTypes: ['pdf'],
                uploadUrl: Routing.generate('banque_envoi_pm'),
                uploadExtraData:function(previewId, index) {
                    var data = {
                        dossier: $('#dossier').val(),
                        exercice: $('#exercice').val(),
                        releve: releve
                    };
                    return data;
                }
            });

            var browse = $('#js_envoi_pm').closest('.btn-file').find('span');
            browse.text('Parcourir ...');

            $('.kv-upload-progress').hide();
            $('.fileinput-cancel').hide();

            $('#js_envoi_pm').on('filebatchuploadsuccess', function(event, data, previewId, index) {
                var response = data.response;
                var nomImageTemp = response.nomTemp;
                var idImageTemp = response.idTemp;
                $(this).closest('.modal').modal('hide');
                var cellStatus = $('tr[id="' + releve + '"] td[aria-describedby="js_id_releve_liste_s"]');
                cellStatus.html(nomImageTemp);

                var cellItemp = $('tr[id="' + releve + '"] td[aria-describedby="js_id_releve_liste_iti"]');
                cellItemp.text(idImageTemp);

            });

        }
    });
}*/