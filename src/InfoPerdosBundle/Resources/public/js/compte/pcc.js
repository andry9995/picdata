/**
 * Created by SITRAKA on 21/10/2016.
 */
function charger_pccs()
{
    lien = Routing.generate('info_perdos_pccs');
    $.ajax({
        data: { dossier:$('#dossier').val().trim(),action:0 },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            editurl = 'test.php';
            var w = $("#js_pccs").parent().width();
            var h = $(window).height() * 0.4;
            set_table_jqgrid($.parseJSON(data),h,pcc_get_col_model(),pcc_get_col_model(w),$("#js_pccs"),'hidden',w,editurl,false);
        }
    });
}

function pcc_get_col_model(w)
{
    colModel1 = new Array();
    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'compte', index:'compte', width:  w * 10 / 100, classes:'js_pcg_digit' });
        /*colModel1.push({ name:'pcg_intitule', index:'pcg_intitule', width:  w * 42 / 100 });
        colModel1.push({ name:'rubrique', index:'rubrique', width:  w * 15 / 100, classes:'js_jg_rubrique', editable:true ,formatter:"select" ,edittype:"select" , editoptions:rubrique });
        colModel1.push({ name:'superRubrique', index:'superRubrique', width:  w * 15 / 100, classes:'js_jg_super_rubrique', editable:true ,formatter:"select" ,edittype:"select" , editoptions:superRubrique });
        colModel1.push({ name:'hyperRubrique', index:'hyperRubrique', width:  w * 15 / 100, classes:'js_jg_hyper_rubrique', editable:true ,formatter:"select" ,edittype:"select" , editoptions:hyperRubrique });
        colModel1.push({ name:'save', index:'save', width:  w * 3 / 100, align:'center', formatter:'jqGridSaveFormatter',classes:'js-entite-action' });*/
    }
    else colModel1 = ['Compte'];

    return colModel1;
}
