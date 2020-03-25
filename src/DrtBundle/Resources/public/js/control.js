/**
 * Created by SITRAKA on 27/03/2019.
 */
function control()
{
    $.ajax({
        data: {
            client: $('#client').val(),
            dossier: $('#dossier').val(),
            exercice: $('#exercice').val(),
            echange_type: $('input[name="show-filter-item"]:checked').val()
        },
        url: Routing.generate('drt_ecriture_controle'),
        type: 'POST',
        dataType: 'json',
        success: function(stats){
            //$('#test_controle').html(data);
            $('#id_control_container').html('<span id="id_control">Statistique</span>');

            $('#id_control').qtip({
                content: {
                    text: function (event, api) {

                        /*'lettres' => $lettres,
                        'repondues' => $repondues,
                        'imagesALettres' => $imagesALettres,
                        'pms' => $pms*/
                        var dossier_text = $('#dossier').find('option:selected').text().trim().toUpperCase();
                        var html = '',
                            total = stats.imagesALettres.length + stats.lettres.length + stats.repondues.length + stats.pms.length;
                        html += '<table class="table">';

                        html += '<tr>';
                        html += '<td>Drt non cloturé</td>';
                        html += '<td class="text-right"></td>';
                        html += '</tr>';

                        html += '<tr>';
                        html += '<td>Lettrées</td>';
                        html += '<td class="text-right">'+number_format(stats.lettres.length, 0, ',', ' ')+'</td>';
                        html += '</tr>';

                        html += '<tr>';
                        html += '<td>Répondues</td>';
                        html += '<td class="text-right">'+number_format(stats.repondues.length, 0, ',', ' ')+'</td>';
                        html += '</tr>';

                        html += '<tr>';
                        html += '<td>Pièces à valider</td>';
                        html += '<td class="text-right">'+number_format(stats.imagesALettres.length, 0, ',', ' ')+'</td>';
                        html += '</tr>';

                        html += '<tr>';
                        html += '<td>PM</td>';
                        html += '<td class="text-right">'+number_format(stats.pms.length, 0, ',', ' ')+'</td>';
                        html += '</tr>';

                        html += '<tr>';
                        html += '<td>Total</td>';
                        html += '<td class="text-right"><strong>'+number_format(total, 0, ',', ' ')+'</strong></td>';
                        html += '</tr>';

                        html += '</table>';
                        return html;
                    }
                },
                position: { my: 'top center', at: 'bottom center' },
                style: { classes: 'qtip-dark qtip-shadow' }
            });
        }
    });
}
