/**
 * Created by SITRAKA on 21/03/2017.
 */
$(document).on('change','.js_table_radio_type',function(){
    charger_tableau_rubriques();
});

$(document).on('change','#js_id_table_rubrique tr td input.input-in-jqgrid',function(){
    table_change_rubrique($(this));
});

$(document).on('focusin','#js_id_table_rubrique tr td input.input-in-jqgrid',function(){
   old_val = $(this).val().trim();
});

$(document).on('click','.js_full_screen_tableau',function(){
    charger_tableau_rubriques();
});

$(document).on('click','.js_show_add_rubrique',function(){
    var titre,animated = 'bounceInRight';
    titre = '<i class="fa fa-plus-circle"></i>&nbsp;<span>Nouvelle Rubrique</span>';
    show_modal($('#js_hidden_add_rubrique').html(),titre,animated);
});

$(document).on('click','#modal-body .js_table_add_rubrique',function(){
    add_new_rubrique();
});

$(document).on('click','#js_id_table_rubrique .js_rem',function(){
    var tr = $(this).closest('tr'),
        rubrique = tr.find('.js_id').text().trim();
    swal({
        title: 'Supprimer',
        text: "Voulez-vous vraiment supprimer cette rubrique ?",
        type: 'question',
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler'
    }).then(function () {
        $.ajax({
            data: { rubrique:rubrique, modif:0, action:1 },
            url: Routing.generate('rubrique_table_edit'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                var result = parseInt(data);
                if(result == 0)
                {
                    show_info('ERREUR','CETTE RUBRIQUE NE PEUT PAS ETRE SUPPRIMEE','error');
                }
                else
                {
                    show_info('SUCCES','RUBRIQUE BIEN SUPPRIMEE');
                    tr.remove();
                }
            }
        });
    }, function (dismiss) {
        // dismiss can be 'cancel', 'overlay',
        // 'close', and 'timer'
        if (dismiss === 'cancel') {
        }
    });
});

function add_new_rubrique()
{
    var input_libelle = $('#modal-body .js_table_libelle_rubrique'),
        libelle = input_libelle.val().trim().sansAccent().toUpperCase(),
        lien = Routing.generate('rubrique_table_edit'),
        type = parseInt($("#js_table_type_rubrique input[type='radio']:checked").attr('data-val'));

    if(libelle == '')
    {
        show_info('ERREUR','LE NOM EST VIDE','error');
        input_libelle.closest('.form-group').addClass('has-error');
        return;
    }
    else input_libelle.closest('.form-group').removeClass('has-error');

    $.ajax({
        data: { libelle:libelle, modif:0, rubrique:$('#js_zero_boost').val(), type:type },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            var result = parseInt(data);
            if(result == 1)
            {
                show_info('SUCCES','LA NOUVELLE RUBRIQUE '+libelle+' EST BIEN AJOUTEE');
                close_modal();
                charger_tableau_rubriques();
            }
            else
            {
                show_info('SUCCES','CE NOM EXISTE DEJA','error');
                input_libelle.closest('.form-group').addClass('has-error');
            }
        }
    });
}

function table_change_rubrique(input)
{
    var new_val = input.val().trim().sansAccent().toUpperCase(),
        modif = (input.closest('td').hasClass('js_lib')) ? 0 : 1,
        old = old_val,
        rubrique = input.closest('tr').find('td.js_id').text().trim();

    if(modif === 0 && new_val === '')
    {
        show_info('ERREUR','NOM VIDE','error');
        input.val(old);
        return;
    }

    $.ajax({
        data: { rubrique:rubrique, old_val:old, new_val:new_val, modif:modif, action:0 },
        url: Routing.generate('rubrique_table_edit'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            //show_modal(data);return;

            var result = parseInt(data);
            if(result === 1)
            {
                input.val(new_val);
                show_info('SUCCES','MODIFICATION ENREGISTREE AVEC SUCCES');
            }
            else
            {
                input.val(old);
                if(result === 0) show_info('ERREUR',(modif === 0) ? 'CETTE RUBRIQUE EXISTE DEJA' : 'CE COMPTE EXISTE DEJA','error');
                else if(result === 2)
                {
                    var new_compte = parseInt(new_val);
                    show_modal($('#js_id_new_compte_hidden').html(),'CREATION NOUVEAU COMPTE');
                    $('#modal-body').find('.js_cl_pcg_compte').val(new_compte).attr('value',new_compte);
                    show_info('ERREUR','CE COMPTE N EXISTE PAS DANS LE PLAN COMPTABLE','error');
                }
            }
        }
    });
}

function charger_tableau_rubriques()
{
    $('#js_conteneur_table_rubrique').html('<table id="js_id_table_rubrique"></table>');
    var lien = Routing.generate('rubrique_table_rubriques'),
        type = parseInt($("#js_table_type_rubrique input[type='radio']:checked").attr('data-val'));
    if($('#js_table_type_r').is(':checked')) type = 0;
    else if($('#js_table_type_sr').is(':checked')) type = 1;

    $.ajax({
        data: { type:type },
        url: lien,
        type: 'POST',
        //async:false ,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            var dataObject = $.parseJSON(data),
                datas = dataObject.datas,
                entetes = dataObject.entetes,
                models = dataObject.models,
                table_selected = $('#js_id_table_rubrique'),
                editurl = 'test.php',
                w = table_selected.parent().width(),
                h = $(document).height() * 0.65;

            set_table_jqgrid(datas,h,table_get_col_model(entetes),table_get_col_model(models,w),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined,false);
        }
    });
}

function table_get_col_model(model,w)
{
    var colModel1 = new Array(),i;
    if(typeof w !== 'undefined')
    {
        for(i = 0;i < model.length; i++)
        {
            var width = 55;
            if(i == 1) width = 250;
            else if(i == 2) width = 30;
            else if(i == 3) width = 70;

            if(i == 0 || i == 3) colModel1.push({ name:model[i].name, index:model[i].name, width: width, hidden:(i == 0), classes:model[i].class, align:model[i].align });
            else if(i == 2) colModel1.push({ name:model[i].name, index:model[i].name, width: width, classes:model[i].class, align:model[i].align, formatter: function () { return '<i class="fa fa-trash-o" aria-hidden="true"></i>'; } });
            else colModel1.push({ name:model[i].name, index:model[i].name, width: width, classes:model[i].class, align:model[i].align, formatter: function (v) { return to_input_text(v) ; } });
        }
    }
    else
    {
        for(i = 0;i < model.length; i++) colModel1.push(model[i]);
    }
    return colModel1;
}

function to_input_text(v)
{
    var texte = (typeof v !== 'undefined') ? v : '';
    return '<input class="input-in-jqgrid" style="text-align:inherit" value="'+texte+'">';
}
