/**
 * Created by SITRAKA on 10/04/2018.
 */
var w_l = 24,
    last_action = null;

$(document).on('click','.js_return',function(){
    if (last_action !== null)
    {
        if (last_action.type === 'c') last_action.element.click();
        else show_edit_cle(last_action.text,last_action.element);
    }
});

$(document).on('mouseup','.js_cl_etendre_recherche',function(){
    var selectedText = window.getSelection().toString().trim();
    if (selectedText === '') return;

    $.ajax({
        data: {
            selected_text: selectedText,
            dossier: $('#dossier').val(),
            exercice: $('#exercice').val(),
            banque_compte:$('#js_banque_compte').val()
        },
        type: 'POST',
        url: Routing.generate('banque_show_search'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_modal(data,'Imputation par LETTRAGE: '+selectedText,undefined,'modal-lg');
            var table_releve = $('#js_id_tb_releve'),
                table_selected = $('#js_cl_tb_affecter'),
                w = table_selected.parent().width(),
                h_table = 200,
                editurl = 'index.php',
                text =
                    '<div class="checkbox checkbox-inline" style="margin-top:1px;margin-left:50px">' +
                    '<input type="checkbox" class="js_piece_item" id="chk_{0}">' +
                    '<label for="chk_{0}">&nbsp;</label>' +
                    '</div>'+
                    '<span class="js_show_image_temp pointer text-primary">' + '{0}' + '</span>',
                group_object = {
                    groupField : ['g'],
                    //groupCollapse : true,
                    groupText : [ text ],
                    groupColumnShow : [false]
                },
                exercice = parseInt($('#exercice').val().trim());
            set_table_jqgrid($.parseJSON(table_releve.closest('.js_container_tb').attr('data-datas')),h_table,get_col_model_releve(),get_col_model_releve(w),table_releve,'hidden',w,editurl,false,undefined/*,true,group_object,'asc','p'*/);
            set_table_jqgrid($.parseJSON(table_selected.closest('.js_container_tb').attr('data-datas')),h_table,get_col_model_image_affecter(undefined,undefined,true),get_col_model_image_affecter(w,undefined,true),table_selected,'hidden',w,editurl,false,undefined/*,true,group_object,'asc','p'*/);
            gerer_ligne_hidden();
            resize_in_modal();
        }
    });
});

$(document).on('click','#js_id_valider_search',function(){
    var type_compta = $('input[name="radio-type-compta"]:checked').val(),
        lettrages = [],
        rapprochements = [];

    $('#js_id_tb_releve').find('.js_class_lettrage').each(function(){
        var lettrage = $(this).val().trim();
        if (lettrage !== '')
        {
            var trouve = false;
            $('#js_cl_tb_affecter').find('.js_class_lettrage').each(function(){
                if ($(this).val().trim() === lettrage && !trouve) trouve = true;
            });

            if (trouve && !lettrages.in_array(lettrage)) lettrages.push(lettrage);
        }
    });

    for (var i = 0; i < lettrages.length; i++)
    {
        var releves = [];
        $('#js_id_tb_releve').find('.js_class_lettrage').each(function() {
            if ($(this).val().trim() === lettrages[i]) releves.push($(this).closest('tr').attr('id'));
        });

        var images = [];
        $('#js_cl_tb_affecter').find('.js_class_lettrage').each(function() {
            if ($(this).val().trim() === lettrages[i]) images.push($(this).closest('tr').find('.js_id_image').text().trim());
        });

        rapprochements.push({ 'rs':releves, 'is':images, 'l':lettrages[i] });
    }

    $.ajax({
        data: {
            type_compta: type_compta,
            rapprochements:JSON.stringify(rapprochements)
        },
        url: Routing.generate('banque_rapprochers'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            scroll_position = $('#gbox_js_id_releve_liste').find('.ui-jqgrid-bdiv').scrollTop();
            charger_analyse();
            close_modal();
            show_info('SUCCES','MODIFICATIONS BIENS ENREGISTREES AVEC SUCCES');
            //$('#test_rapprochement').html(data);
        }
    });
});

function get_col_model_releve(w)
{
    var colModel1 = [];
    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'x', index:'x', width:w_l ,formatter:function(){ return lettrage_formatter(); } });
        colModel1.push({ name:'i', index:'i', sortable:true, width:15 *w/100, align:'center', classes:'js_show_image_ pointer text-primary' });
        colModel1.push({ name:'d', index:'d', sortable:true, width:15 *w/100, align:'center', sorttype: 'date', formatter: 'date', formatoptions: {srcformat: 'd/m/Y', newformat: 'd/m/Y'} });
        colModel1.push({ name:'l', index:'l', sortable:true, width:55 *w/100 - w_l });
        colModel1.push({ name:'m', index:'m', sortable:true, width:15 *w/100, sorttype: 'number', classes:'js_cl_ttc', align:'right', formatter: function(v) { return '<b class="'+ ((v < 0) ? 'text-danger' : 'text-primary') +'">'+ number_format(v, 2, ',', ' ') +'</b>'; } });

        colModel1.push({ name:'ii', index:'ii', classes:'js_id_image', hidden: true });
    }
    else
    {
        colModel1 = [
            'L',
            'Image',
            'Date Op\xB0',
            'libelle',
            'Montant',

            'id image'
        ];
    }
    return colModel1;
}

function lettrage_formatter()
{
    return '<input type="text" class="input-in-jqgrid text-center js_class_lettrage">';
}
