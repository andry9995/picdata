/**
 * Created by SITRAKA on 07/07/2017.
 */
var index_ui_modal = 0;
$(document).on('click','.js_show_image',function(){
    show_image_pop_up($(this).attr('data-id_image'));
});

/**
 *
 * @param id
 * @param type
 * @param exercices
 * @param moiss
 * @param periodes
 * @param titre
 * type : 0=>pcc ; 1=>tiers
 */
function show_compte_details(id,type,exercices,moiss,periodes,titre)
{
    index_ui_modal++;
    $.ajax({
        data: {
            id:id,
            type: type,
            dossier: ($('#dossier').length > 0) ? $('#dossier').val() : $('#js_zero_boost').val(),
            exercices: JSON.stringify(exercices),
            mois: JSON.stringify(moiss),
            periodes: JSON.stringify(periodes)
        },
        url: Routing.generate('img_show_details_compte'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var id_t = 'js_table_'+ index_ui_modal,
                new_table = '<table id="'+ id_t +'"></table>',
                options = { modal: false, resizable: true,title: titre };
            modal_ui(options,new_table);
            var table = $('#'+id_t),
                w = table.parent().width() - 10,
                h = table.parent().height() - 50,
                editurl = 'index.php';
            set_table_jqgrid($.parseJSON(data),h,gl_col_mod(),gl_col_mod(w),table,'hidden',w,editurl,false,undefined,true,{groupField : ['cp'],groupColumnShow : [false]},'asc','p');
            set_tables_responsive();
        }
    });
}

/**
 *
 * @param id
 * @param cr
 *
 * id: id image crypter
 */
function show_image_pop_up(id,cr)
{
    cr = typeof cr !== 'undefined' ? cr : 1;
    $.ajax({
        data: { imageId:id, height:$(window).height() - 250, cr:cr },
        url: Routing.generate('consultation_piece_data_image'), //Routing.generate('img_show_image_data'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var options = { modal: false, resizable: true,title: '' };
            modal_ui(options,data, false, 0.8);

            $(document).off('focusin.modal');

            $('.js_embed').each(function(){
                $(this).height($(this).closest('.row').height() - 25);
            });


            $('.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                language: "fr"
            });

            if(typeof initCEGJForm === 'function') initCEGJForm(parseInt($('#modal-ui').attr('data-id')));
        }
    });
}

function image_formatter(ob)
{
    return '<span class="js_show_image pointer text-primary" data-id_image="'+ob.id+'">'+ob.nom+'</span>';
}