/**
 * Created by SITRAKA on 25/03/2019.
 */
$(document).ready(function(){
    $(document).on('click','.cl_export',function(){
        var dossiers = [];
        if(
            $('#dossier option:selected').text().trim().toUpperCase() !== '' &&
            $('#dossier option:selected').text().trim().toUpperCase() !== 'TOUS'
        ){
            $('#id_table_ecriture').find('tr').each(function(){
                if (!$(this).hasClass('jqgfirstrow'))
                {
                    if (!($(this).find('.c_instruction').find('.js_show_image').length > 0 ||
                        $(this).find('.c_instruction').find('.cl_instruction').length > 0 &&
                        parseInt($(this).find('.c_instruction').find('.cl_instruction').val()) !== 0)
                    ){
                        var page = $(this).find('.c_page').text().trim(),
                            dossier = $(this).find('.c_dossier').text().trim(),
                            date = $(this).find('.c_date').text().trim(),
                            jnl = $(this).find('.c_jnl').text().trim(),
                            compte = $(this).find('.c_compte').text().trim(),
                            piece = $(this).find('.c_piece').text().trim(),
                            libelle = $(this).find('.c_libelle').text().trim(),
                            credit = $(this).find('.c_credit').text().trim(),
                            debit = $(this).find('.c_debit').text().trim(),
                            solde = $(this).find('.c_solde').text().trim();

                        dossiers.push({
                            page: page,
                            dossier: dossier,
                            date: date,
                            jnl: jnl,
                            compte: compte,
                            piece: piece,
                            libelle: libelle,
                            credit: number_fr_to_float(credit),
                            debit: number_fr_to_float(debit),
                            solde: number_fr_to_float(solde)
                        });
                    }
                }
            });
        }

        if (dossiers.length === 0)
        {
            show_info('Vide','Aucune données à exporter','error');
            return;
        }

        var params = ''
                + '<input type="hidden" name="datas" value="'+encodeURI(JSON.stringify(dossiers))+'">'
                + '<input type="hidden" name="exercice" value="'+$('#exercice').val()+'">';
        $('#id_export').attr('action',Routing.generate('drt_ecriture_generate_xls')).html(params).submit();
    });
});

function number_fr_to_float(s)
{
    return parseFloat(s.replace(/&nbsp;/gi, '').replace(/ /g,"").replace(/,/,'.'));
}