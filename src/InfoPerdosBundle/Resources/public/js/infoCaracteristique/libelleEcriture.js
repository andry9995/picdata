$(document).ready(function () {

   $(document).on('click', '.btn-validation-meth-libelle', function(){
        var items = [];

        var libelleType = $(this).attr('data-id');

        $(this).closest('.ibox').find('.libelle .ibox-content .form-group').each(function(){
            items.push({
                id: $(this).attr('data-id'),
                nbcar: $(this).find('input[type="number"]').val(),
                position: $(this).find('select').val()
            });
        });

        var pan = $(this).closest('.ibox').find('.libelle').closest('.panel');
        if(pan.hasClass('panel-danger')){
            show_info('Attention', 'Nombre de caractère supérieur au libelle', 'error');
            return;
        }

        //Enregistrement any @base
        $.ajax({
            url: Routing.generate('info_perdos_libelle_save'),
            type: 'POST',
            data: {
                dossierid: dossier_id,
                items: items,
                libelletype: libelleType
            },
            success: function (data) {
                show_info('', data['message'], data['type']);
            }

        })

    });

    $(document).on('input', '.libelle input[type="number"]', function () {

        var somme = 0;
        $(this).closest('.libelle').find('input[type="number"]').each(function(){
            if($(this).val() !== ''){
                somme += parseInt($(this).val());
            }
        });

        var nbcarLogiciel = parseInt($('#js_instr_logiciel option:selected').attr("nb-caractere"));

        var pan = $(this).closest('.panel');
        if(somme <= nbcarLogiciel){
            pan.removeClass('panel-danger');
            if(!pan.hasClass('panel-primary'))
                pan.addClass('panel-primary');
        }
        else{
            pan.removeClass('panel-primary');
            if(!pan.hasClass('panel-danger'))
                pan.addClass('panel-danger');
        }
    });

});
