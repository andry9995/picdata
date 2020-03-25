/**
 * Created by SITRAKA on 21/07/2017.
 */
$(document).on('change','#js_filtre',function(){
    var val = parseInt($(this).val());
    $('.js_cl_container_fitre').each(function(){
        var data_val = parseInt($(this).attr('data-val'));
        if (val == data_val) $(this).removeClass('hidden');
        else $(this).addClass('hidden');
    });
});

$('.js_cl_num_piece').keyup(function(){
    var text = $(this).val().trim();
    if(text.trim() != '') $('#'+$(this).attr('data-contre')).val('').attr('value','');
});
