/**
 * Created by SITRAKA on 12/02/2019.
 */
var controle = {
    tot_debit: 0,
    tot_credit: 0,
    total_512: 0,
    total_contre: 0
};


function control()
{
    var el_banque_compte = $('#js_banque_compte'),
        banque_compte_text = '';
    if (el_banque_compte.length > 0)
        banque_compte_text = el_banque_compte.find('option:selected').text().trim().toUpperCase();

    if (banque_compte_text !== '' && banque_compte_text !== 'TOUS')
    {
        $.ajax({
            data: {
                dossier: $('#dossier').val(),
                banque: $('#js_zero_boost').val(),
                banqueCompte: el_banque_compte.val(),
                exercice: $('#exercice').val()
            },
            type: 'POST',
            url: Routing.generate('banque2_control'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                set_control($.parseJSON(data));
            }
        });
    }
}

function set_control(datas)
{
    var s = (typeof datas === 'undefined') ? 5 : parseInt(datas.s);

    if (controle.tot_debit.toFixed(2) !== controle.tot_credit.toFixed(2) ||
        controle.total_512.toFixed(2) !== (-controle.total_contre).toFixed(2))
    {s = 1;}

    $('#id_control_container')
        .html('<span class="btn '+((s === 0) ? 'btn-white' : 'btn-danger')+'" id="id_control">'+((s === 0) ? 'OK' : 'KO')+'</span>');

    var position = { my: 'top right', at: 'bottom left' };
    $('#id_control').qtip({
        content: {
            text: function (event, api) {
                if (typeof datas === 'undefined')
                    return '' +
                        '<table class="table table-bordered">' +
                            '<tr>' +
                                '<th>Contrôle</th>' +
                            '</tr>' +
                        '</table>';

                var res = datas,
                    controls = res.res,
                    html = '';
                for (var i = 0; i < controls.length; i++)
                {
                    var v = controls[i],
                        ecart = parseFloat((v.sf - v.sd - v.m).toFixed(2));

                    html += '' +
                        '<table class="table table-bordered">' +
                            '<tr>' +
                                '<th colspan="2">' +v.bc+ '</th>' +
                            '</tr>';

                    html +=
                        '<tr>' +
                            '<th>SI </th>' +
                            '<td align="right">' +number_format(v.sd, 2, ',', ' ',true)+ '</td>' +
                        '</tr>';
                    html +=
                        '<tr>' +
                            '<th>MVTS </th>' +
                            '<td align="right">' +number_format(v.m, 2, ',', ' ',true)+ '</td>' +
                        '</tr>';

                    html +=
                        '<tr>' +
                            '<th>SF </th>' +
                            '<td align="right">' +number_format(v.sf, 2, ',', ' ',true)+ '</td>' +
                        '</tr>';

                    html +=
                        '<tr>' +
                            '<th>Ecart </th>' +
                            '<td align="right">' +number_format(ecart, 2, ',', ' ')+ '</td>' +
                        '</tr>';

                    html +=
                        '<tr>' +
                            '<th>Tot Débit </th>' +
                            '<td align="right">' +number_format(controle.tot_debit, 2, ',', ' ',true)+ '</td>' +
                        '</tr>' +
                        '<tr>' +
                            '<th>Tot Crédit </th>' +
                            '<td align="right">' +number_format(controle.tot_credit, 2, ',', ' ',true)+ '</td>' +
                        '</tr>' +
                        '<tr>' +
                            '<th>Tot Banque </th>' +
                            '<td align="right">' +number_format(controle.total_512, 2, ',', ' ',true)+ '</td>' +
                        '</tr>'+
                        '<tr>' +
                            '<th>Tot Contrepartie </th>' +
                            '<td align="right">' +number_format(controle.total_contre, 2, ',', ' ',true)+ '</td>' +
                        '</tr>';

                    var rm = '';
                    $.each(v.rm, function( index, value ) {
                        rm += value + ', ';
                    });

                    if (rm.trim() !== '')
                    {
                        html += '' +
                            '<tr>' +
                                '<th>Mois manquants</th>' +
                                '<td>'+rm+'</td>' +
                            '</tr>';
                    }

                    html += '</table>';
                }

                return html;
            }
        },
        position: position,
        style: {
            classes: 'qtip-dark qtip-shadow'
        }
    });
}
