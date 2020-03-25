/**
 * Created by SITRAKA on 02/06/2017.
 */
$(document).ready(function(){
    var h = $(window).height() - 200;
    $('#js_container_div_tree').height(h);
    $('#js_container_previews').height(h);

    charger_users();

    $(document).on('change','#client',function(){
        charger_users();
        vider();
    });

    $(document).on('change','#js_filtre',function(){
        vider();
        //charger_tree();
    });

    $(document).on('click','#js_charger_tree',function(){
        vider();
        charger_tree();
    });

    $(document).on('click','.jstree-node',function(){
        var level = parseInt($(this).attr('aria-level'));
        if(level == 4) charger_vignettes($(this));
    });

    $(document).on('change','.chk-file',function(){
        checked_file($(this));
    });

    $(document).on('click','#js_container_previews div.image',function(){
        show_image($(this));
    });
});

function charger_users()
{
    $.ajax({
        data: { client: $('#client').val() },
        type: 'POST',
        url: Routing.generate('img_historique_users'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#js_utilisateur_container').html(data);
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
        }
    });
}

function charger_tree()
{
    var users = $('#js_utilisateur').val();
    if(users == null)
    {
        show_info('UTILISATEUR VIDE','Choisir au moins un utilisateur','error');
        $('#js_utilisateur').closest('.form-group ').addClass('has-error');
        return;
    }
    else $('#js_utilisateur').closest('.form-group ').removeClass('has-error');

    $.ajax({
        data: { client:$('#client').val(), filtre: $('#js_filtre').val(), users:users },
        type: 'POST',
        url: Routing.generate('img_historique_tree'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            //$('#js_container_tree').html(data);return;
            var dataObject = $.parseJSON(data);
            $('#js_container_tree')
            .on('click','.jstree-anchor', function (e) {
                $('#js_container_tree').jstree(true).toggle_node(e.target);
            })
            .jstree({ 'core' :
                {
                    'dblclick_toggle' : false,
                    'data' : dataObject
                }
            }).on('open_node.jstree',function (e,data) {
                //alert();
                charger_vignettes($('#'+data.node.li_attr.id));

            }).on('close_node.jstree',function () {
                vider_vignettes();
            });
        }
    });
}

function charger_vignettes(li)
{
    var niveau = parseInt(li.attr('aria-level'));
    if(niveau < 3)
    {
        vider_vignettes();
        return;
    }

    $('#js_container_previews').empty();
    var lot = li.attr('data_lot'),
        img = li.attr('data_image');

    $.ajax({
        data: { lot: lot, img:img },
        type: 'POST',
        url: Routing.generate('img_historique_apercus'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#js_container_previews').html(data);
            $('#js_container_btn_ctrl').removeClass('hidden');
        }
    });
}

function vider()
{
    $('#js_container_div_tree').html('<div id="js_container_tree"></div>');
    vider_vignettes();
}

function vider_vignettes()
{
    $('#js_container_previews').empty();
    $('#js_container_btn_ctrl').addClass('hidden');
}

function checked_file(input)
{
    if(input.is(':checked')) input.closest('.file').addClass('file-checked');
    else input.closest('.file').removeClass('file-checked');
}

function show_image(im)
{
    show_image_pop_up(im.closest('.file-box').attr('data-id'));
}
