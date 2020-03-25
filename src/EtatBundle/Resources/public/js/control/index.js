/**
 * Created by SITRAKA on 22/06/2017.
 */
$(document).ready(function(){
    var config = {
        '.chosen-select'           : {},
        '.chosen-select-deselect'  : {allow_single_deselect:true},
        '.chosen-select-no-single' : {disable_search_threshold:10},
        '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
        '.chosen-select-width'     : {width:"95%"}
    };
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
    charger_site();

    $(document).on('change','#dossier',function(){
        charger_input_errors();
    });
    $(document).on('change','#exercice',function(){
        charger_input_errors();
    });
});

function after_charged_dossier()
{
    charger_input_errors();
}
function after_charged_dossier_not_select()
{
    charger_input_errors();
}

function charger_errors()
{
    var new_table = '<table id="js_table_erros"></table>';
    $('#js_conteneur_table').html(new_table);
    var table = $('#js_table_erros'),
        h = $(window).height() - 200,
        w = table.parent().width(),
        editurl = 'inde.php';
    set_table_jqgrid($.parseJSON($('#js_id_errors').val()),h,models_erros(),models_erros(w),table,'hidden',w,editurl,false,undefined,true,{groupField : ['cl','et','etR'],groupColumnShow : [false,false,false]},'asc','p');
}

function models_erros(w)
{
    var colModel1 = [];
    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'p', index:'p', width:  w * 40 / 100, sorttype:'integer', align:'right', formatter: function (v) { return number_format(v + 1,0,',','\x20');} });
        colModel1.push({ name:'et', index:'et', width:  0, hidden: true });
        colModel1.push({ name:'etR', index:'etR', width:  0, hidden: true });
        colModel1.push({ name:'cl', index:'cl', width:  0, hidden: true });
        colModel1.push({ name:'dos', index:'dos', width:  w * 30 / 100 });
        colModel1.push({ name:'ex', index:'ex', width:  w * 30 / 100 });
    }
    else colModel1 = [
        '',
        'etat',
        'Libelle',
        'Client',
        'Dossier',
        'EXERCICES[COLONNES(periodes)]'
    ];
    return colModel1;
}

function charger_input_errors()
{
    $.ajax({
        data: {
            client: $('#client').val(),
            site: $('#site').val(),
            dossier: $('#dossier').val(),
            exercices: $('#exercice').val()
        },
        url: Routing.generate('etat_control_errors'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#js_id_errors').val(data);
            charger_errors();
            //$('#jivo_container').addClass('hidden').setAttribute('style', 'display:none !important');
        }
    });
}