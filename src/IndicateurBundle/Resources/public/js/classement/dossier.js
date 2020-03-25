/**
 * Created by SITRAKA on 10/02/2017.
 */
$(document).on('click','#js_dossier_table tr',function(){
    charger_group_dossier($(this));
});

function charger_dossier_table()
{
    var client = $('#client').val();
    var site = $('#js_zero_boost').val();

    if($('#site').length > 0)
    {
        site = $('#site').val();
    }
    var lien = Routing.generate('ind_classement_dossier_indicateur');

    $.ajax({
        data: { client:client, site:site },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            var table = $('#js_dossier_table'),
                w = table.parent().width(),
                h = $(window).height() * 0.75,
                editurl = 'index.php';
            set_table_jqgrid($.parseJSON(data),h,dossier_indicateur_col_model(),dossier_indicateur_col_model(w),table,'hidden',w,editurl,false);
            format_table_dossier();
        }
    });
}

function dossier_indicateur_col_model(w)
{
    var colModel1 = new Array();
    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'nom', index:'nom', width:  w * 100 / 100, classes:'js_td_dossier_nom' });
        colModel1.push({ name:'idCrypter', index:'idCrypter', hidden:true, classes:'js_td_dossier_id' });
        colModel1.push({ name:'indicateurGroup', index:'indicateurGroup', classes:'js_td_dossier_groupe', formatter: function (indicateurGroup) { return (indicateurGroup != null) ? indicateurGroup.libelle : '' } })
    }
    else colModel1 = ['Dossier','','Groupe'];

    return colModel1;
}

function charger_group_dossier(tr)
{
    $('.js_cl_groups_contener').empty();
    var client = $('#js_zero_boost').val(),
        dossier = tr.find('.js_td_dossier_id').text(),
        lien = Routing.generate('ind_classement_groups'),
        param_gen = $('#js_is_general').is(':checked') ? 1 : 0;

    $.ajax({
        data: { client:client, dossier:dossier, param_gen:param_gen },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('.js_cl_groups_contener').html(data);

            $( "#id_groups_contener" ).sortable({
                placeholder: "ui-state-highlight",
                cancel: ".group-not-sortable",
                update: function() {
                    change_rang(2);
                }
            }).disableSelection();

            menu_context();
            set_action_tableau();
            set_height_pack_contener();
        }
    });
}

function format_table_dossier()
{
    $('#js_dossier_table').find('tr.ui-row-ltr').each(function(){
       if($(this).find('.js_td_dossier_groupe').text().trim() == '') $(this).addClass('tr-error');
       else $(this).removeClass('tr-error');
    });
}
