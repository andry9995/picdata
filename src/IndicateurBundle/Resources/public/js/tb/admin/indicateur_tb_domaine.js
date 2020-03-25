/**
 * Created by SITRAKA on 19/09/2019.
 */

var old_domaine_name = '';
$(document).ready(function(){
    $(document).on('click','#id_show_add_domaine',function(){
        show_modal($('#js_id_indicateur_tb_add_hidden').html(),'Nouveau Domaine');
    });

    $(document).on('click','.js_cl_add_indicateur_tb',function(){
        var nom = $(this).closest('.form-horizontal').find('.js_cl_indicateur_tb_nom').val().trim();

        if (nom.trim() === '')
        {
            show_info('erreur','Le nom ne doit pas être vide','error');
            return;
        }

        $.ajax({
            data: {
                action: 0,
                indicateur_tb_domaine: $('#js_zero_boost').val(),
                nom: nom,
                affichage: $('#id_tb_type').val()
            },
            type: 'POST',
            url: Routing.generate('ind_tb_domaine_edit'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                var res = $.parseJSON(data);
                show_info(res.t,res.m,res.s);
                if (res.s.trim() === 'success')
                {
                    charger_tb_domaine();
                    close_modal();
                }
            }
        });
    });

    $(document).on('change','.cl_nom_domaine_edit',function(){
        var nom = $(this).val(),
            indicateur_tb_domaine = $(this).closest('.panel').attr('data-id');

        if (nom.trim() === '')
        {
            show_info('erreur','Le nom ne doit pas être vide','error');
            return;
        }

        $.ajax({
            data: {
                action: 1,
                indicateur_tb_domaine: indicateur_tb_domaine,
                nom: nom
            },
            type: 'POST',
            url: Routing.generate('ind_tb_domaine_edit'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                var res = $.parseJSON(data);
                show_info(res.t,res.m,res.s);
            }
        });
    });

    $(document).on('click','.cl_remove_domaine',function(){
        var el = $(this).closest('.panel'),
            indicateur_tb_domaine = el.attr('data-id');

        $.ajax({
            data: {
                action: 2,
                indicateur_tb_domaine: indicateur_tb_domaine
            },
            type: 'POST',
            url: Routing.generate('ind_tb_domaine_edit'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                var res = $.parseJSON(data);
                show_info(res.t,res.m,res.s);
                el.remove();
            }
        });
    });

    $(document).on('click','.cl_tranferer_indicateur_tb',function(){
        var indicateur_tb = $(this).closest('.cl_li').attr('data-id');

        $.ajax({
            data: {
                affichage: parseInt($('#id_tb_type').val().trim()),
                indicateur_tb: indicateur_tb
            },
            type: 'POST',
            url: Routing.generate('ind_tb_transfert'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_modal(data,'Tranferer dans le domaine');
            }
        });
    });

    $(document).on('click','#id_tranferer_indicateur_tb',function(){
        var indicateur_tb = $('#id_new_indicateur_tb').attr('data-indicateur_tb'),
            indicateur_tb_domaine = $('#id_new_indicateur_tb').val();
        $.ajax({
            data: {
                indicateur_tb_domaine: indicateur_tb_domaine,
                indicateur_tb: indicateur_tb
            },
            type: 'POST',
            url: Routing.generate('ind_tb_transferer'),
            dataType: 'html',
            success: function(data) {
                test_security(data);

                if (parseInt(data) === 0)
                {
                    show_info('Succès','Tranfert enregistrée avec succès');
                    charger_tb_domaine();
                    close_modal();
                }
                else
                {
                    show_info('Erreur','Une erreur c est produite pendant le tranfert');
                }
            }
        });
    });
});

function charger_tb_domaine()
{
    $.ajax({
        data: {
            affichage: parseInt($('#id_tb_type').val().trim())
        },
        type: 'POST',
        url: Routing.generate('ind_tb_domaine_show'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#js_id_container_indicateur').html(data);

            $('#id_accordion').on('shown.bs.collapse', function (e) {
                $(e.target).closest('.panel').find('.cl_chevron.fa-chevron-down').addClass('hidden');
                $(e.target).closest('.panel').find('.cl_chevron.fa-chevron-up').removeClass('hidden');
                charger_indicateurs();
            }).on('hide.bs.collapse', function (e) {
                $(e.target).closest('.panel').find('.cl_chevron.fa-chevron-down').removeClass('hidden');
                $(e.target).closest('.panel').find('.cl_chevron.fa-chevron-up').addClass('hidden');
            });

            charger_indicateurs();
        }
    });
}
