/**
 * Created by SITRAKA on 20/07/2017.
 */
$(document).ready(function(){
    charger_site();
    $('#js_date_range').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        language: "fr",
        format: 'dd/mm/yyyy'
    });

    $(document).on('click','#js_go',function(){
        go();
    });

    $(document).on('click','.js_sh_img',function(){
        var id = $(this).closest('tr').find('td.js_id').text().trim();
        show_image_pop_up(id);
    });
});

function go()
{
    var new_table = '<table id="js_table_image"></table>';
    $('#js_container').html(new_table);

    var filtre = parseInt($('#js_filtre').val()),
        nom = $('#js_id_num_picdata').val().trim(),
        originale = $('#js_id_originale').val().trim();

    if (filtre == 1 && nom == '' && originale == '')
    {
        show_info('NOTICE','Remplir le nom ou le NUMERO','error');
        $('.js_cl_num_piece').each(function(){
            $(this).closest('.form-group').addClass('has-error');
        });
        return;
    }
    else
    {
        $('.js_cl_num_piece').each(function(){
            $(this).closest('.form-group').removeClass('has-error');
        });
    }

    $.ajax({
        data: {
            filtre: filtre,
            client: $('#client').val(),
            site: $('#site').val(),
            dossier: $('#dossier').val(),
            dateStart: $('#js_start').val().frToMysqlFormat(),
            dateEnd: $('#js_end').val().frToMysqlFormat(),
            nom: nom,
            originale: originale
        },
        url: Routing.generate('img_imgs'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);

            var table = $('#js_table_image'),
                h = $(window).height() - 200,
                w = table.parent().width(),
                edit_url = 'index.php';
            set_table_jqgrid($.parseJSON(data),h,models_erros(),models_erros(w),table,'hidden',w,edit_url,false,undefined,true,{groupField : ['cl','dos','date','l'],groupColumnShow : [false,false,false,false]},'asc','p');
        }
    });
}

function models_erros(w)
{
    var colModel1 = [];
    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'p', index:'p', hidden:true, sorttype:'integer', align:'right' });
        colModel1.push({ name:'cl', index:'cl', hidden:true });
        colModel1.push({ name:'dos', index:'dos', hidden:true });
        colModel1.push({ name:'date', index:'date', hidden:true });
        colModel1.push({ name:'l', index:'l', hidden:true });
        colModel1.push({ name:'nom', index:'nom', width:  w * 40 / 100, align:'right', classes:'text-primary' });
        colModel1.push({ name:'org', index:'org', width:  w * 40 / 100, classes:'text-success' });
        colModel1.push({ name:'v', index:'v', width:  w * 3 / 100, align:'center', classes:'pointer js_sh_img', formatter: function (v) { return '<i class="fa fa fa-file-image-o" aria-hidden="true"></i>';} });
        colModel1.push({ name:'ext', index:'ext', width:  w * 7 / 100 });
        colModel1.push({ name:'ex', index:'ex', width:  w * 10 / 100 });
        colModel1.push({ name:'id', index:'id', classes:'js_id', hidden:true });
    }
    else colModel1 = [
        '',
        'Client',
        'Dossier',
        'Date',
        'Lot',
        'NUMEROTATION',
        'ORIGINALE',
        '',
        'EXT',
        'Exercice',
        ''
    ];
    return colModel1;
}
