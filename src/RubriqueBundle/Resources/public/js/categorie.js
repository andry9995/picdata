/**
 * Created by SITRAKA on 22/10/2019.
 */

var cat_edited = 'cat_edited',
    old_val_cat = '';
$(document).ready(function(){
    $(document).on('change','.cl_input_in_tb',function(){
        var type = $(this).hasClass('cl_libelle') ? 1 : 0,
            new_val = $(this).val().trim(),
            cat = $(this).closest('tr').attr('id'),
            input = $(this);

        $.ajax({
            data: {
                old: old_val_cat,
                new: new_val,
                type: type,
                cat: cat
            },
            url: Routing.generate('rubrique_categorie_pcg_save'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                var result = parseInt(data);
                if(result === 1)
                {
                    input.val(new_val);
                    show_info('SUCCES','MODIFICATION ENREGISTREE AVEC SUCCES');
                }
                else
                {
                    input.val(old_val_cat);
                    if(result === 0) show_info('ERREUR',(type === 1) ? 'CETTE CATEGORIE EXISTE DEJA' : 'CE COMPTE EXISTE DEJA','error');
                    else if(result === 2)
                    {
                        var new_compte = parseInt(new_val);
                        if (isNaN(new_compte)) new_compte = '';
                        show_modal($('#js_id_new_compte_hidden').html(),'CREATION NOUVEAU COMPTE');
                        $('#modal-body').find('.js_cl_pcg_compte').val(new_compte).attr('value',new_compte);
                        show_info('ERREUR','CE COMPTE N EXISTE PAS DANS LE PLAN COMPTABLE','error');
                    }
                }
            }
        });
    });

    $(document).on('click','.cl_delete_categorie',function(){
        var tr = $(this).closest('tr'),
            cat = tr.attr('id');
        $.ajax({
            data: {
                cat: cat
            },
            url: Routing.generate('rubrique_categorie_delete'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                if (parseInt(data) === 1)
                {
                    show_info('Succès','Modification bien enregistrée avec succès');
                    tr.remove();
                }
            }
        });
    });

    $(document).on('focusin','.cl_input_in_tb',function(){
        old_val_cat = $(this).val().trim();
    });

    $(document).on('click','.cl_tabs',function(){
        var type = parseInt($(this).attr('data-type'));
        if (type === 1) categorie_containers();
    });

    $(document).on('click','.cl_show_add_categorie',function(){
        $('.'+cat_edited).removeClass(cat_edited);
        $(this).closest('.cl_container_categorie').addClass(cat_edited);
        show_modal($('#id_add_categorie_hidden').html());
    });

    $(document).on('click','.cl_add_sous_cat',function(){
        var type = parseInt($('.'+cat_edited).attr('data-type')),
            libelle = $(this).closest('.form-horizontal').find('.cl_categorie_libelle').val().trim();

        if (libelle === '')
        {
            show_info('Erreur','Nom vide','error');
            $(this).closest('.form-horizontal').find('.cl_categorie_libelle').closest('.form-group').addClass('has-error');
            return;
        }
        else $(this).closest('.form-horizontal').find('.cl_categorie_libelle').closest('.form-group').removeClass('has-error');

        $.ajax({
            data: {
                libelle: libelle,
                type: type,
                action: 0,
                tresorerie_categorie: $('#js_zero_boost').val()
            },
            url: Routing.generate('rubrique_categorie_save'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                show_info('Succès','Modification bien enregistrée avec succès');
                charger_categories($('.'+cat_edited));
                close_modal();
            }
        });
    });
});

function categorie_containers()
{
    $.ajax({
        data: {  },
        url: Routing.generate('rubrique_categorie_containers'),
        type: 'POST',
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#js_tab_categorie').find('.panel-body').html(data);
            $('.cl_container_categorie').find('.ibox-content').height($(window).height() - 250);

            $('.cl_container_categorie').each(function(){
                charger_categories($(this));
            });
        }
    });
}

function charger_categories(div_container)
{
    var type = parseInt(div_container.attr('data-type')),
        id = 'id_tb_categorie_' + type,
        html = '<table id="'+id+'"></table>';
    div_container.find('.ibox-content').html(html);

    $.ajax({
        data: { type: type },
        url: Routing.generate('rubrique_categories'),
        type: 'POST',
        dataType: 'html',
        success: function(data){
            test_security(data);

            var colNames = ['nom',''],
                colModels = [
                    { name:'libelle',index:'libelle', width:150, formatter: function(v){ return '<input value="'+v+'" class="cl_libelle cl_input_in_tb input-in-jqgrid">' }},
                    { name:'x',index:'x', width:25, formatter: function(v){ return '<i class="fa fa-trash-o pointer cl_delete_categorie" aria-hidden="true"></i>' }}
                    ],
                table_selected = $('#'+id),
                w = table_selected.parent().width(),
                h = table_selected.parent().height() - 15,
                editurl = 'index.php';

            for (var i = 0; i < 25; i++)
            {
                colNames.push(i + 1);
                colModels.push({ name:'_'+i,index:'_'+i, width:75, formatter: function(v){ return '<input value="'+(typeof v !== 'undefined' ? v : '')+'" class="cl_input_in_tb input-in-jqgrid">' }});
            }

            set_table_jqgrid($.parseJSON(data),h,colNames,colModels,table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined,false);
        }
    });
}