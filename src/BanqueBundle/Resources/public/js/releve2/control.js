/**
 * Created by SITRAKA on 21/08/2018.
 */

function charger_control()
{
    $('#js_id_control').empty();
    /*if ($('#js_banque_compte option:selected').text().toUpperCase() === 'TOUS')
    {
        $('#js_banque_compte').closest('.form-group').addClass('has-warning');
        $('#js_id_control').html(
            '<span class="simple_tag white-bg">' +
                '<span class="badge badge-danger">Contrôle&nbsp;impossible!!!</span>&nbsp;' +
                '<span>Choisir&nbsp;un&nbsp;compte</span>' +
            '</span>'
        );
        show_info('NOTICE','Contrôles impossible sans choisir un NUMERO DE COMPTE','warning');
        return;
    }
    else $('#js_banque_compte').closest('.form-group').removeClass('has-warning');*/

    var banque_compte = $('#js_banque_compte').val(),
        banque = $('#js_banque').val();

    $.ajax({
        data: {
            dossier:$('#dossier').val(),
            banque:banque,
            banqueCompte:banque_compte,
            exercice:$('#exercice').val()
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
                //'dc' => $clotureMois->c,
            //$('#js_id_control').html(data);return;
            var span =
                '<span class="simple_tag white-bg" id="id_import_historique">Imports</span>' +
                //'<span class="simple_tag white-bg" id="id_cloture_dossier" style="margin-left:4px" data-cloture=""></span>' +
                '<span class="simple_tag white-bg" id="id_status_control" style="margin-left:4px"></span>';


            $('#js_id_control').html(span);
            var res = $.parseJSON(data),
                status = parseInt(res.s),
                controls = res.res,
                exercice = parseInt($('#exercice').val()),
                import_html = '<table class="table table-bordered">';

            import_html += '' +
                '<tr>' +
                    '<th>Ex.</th>' +
                    '<th>Clôture</th>' +
                    '<th>Import</th>' +
                    '<th>Statut</th>' +
                '</tr>';
            var statusN = 'Pas d import';
            if (res.importN_1 !== null)
            {
                if (parseInt(res.importN_1.s) === 1) statusN = 'Cloturé';
                else if (res.importN_1.dv !== null) statusN = 'Projet ' + res.importN_1.dv;
            }
            import_html += '' +
                '<tr>' +
                    '<td>'+(exercice - 1)+'</td>' +
                    '<td>'+res.dcN_1+'</td>' +
                    '<td>'+((res.importN_1 !== null && res.importN_1.du !== null) ? res.importN_1.du : '')+'</td>' +
                    '<td>'+statusN+'</td>' +
                '</tr>';
            statusN = 'Pas d import';
            if (res.importN !== null)
            {
                if (parseInt(res.importN.s) === 1) statusN = 'Cloturé';
                else if (res.importN.dv !== null) statusN = 'Projet ' + res.importN.dv;
            }
            import_html += '' +
                '<tr>' +
                    '<td>'+exercice+'</td>' +
                    '<td>'+res.dc+'</td>' +
                    '<td>'+((res.importN !== null && res.importN.du !== null) ? res.importN.du : '')+'</td>' +
                    '<td>'+statusN+'</td>' +
                '</tr>';
            import_html += '</table>';

            $('#id_cloture_dossier').html(
                '<span>Cloture</span>&nbsp;<span class="badge badge-info">'+res.dc+'</span>'
            ).attr('data-cloture',res.cl).addClass('hidden');

            var position = { my: 'top right', at: 'bottom left' };
            $('#id_import_historique').qtip({
                content: {
                    text: function (event, api) {
                        return import_html;
                    }
                },
                position: position,
                style: {
                    classes: 'qtip-dark qtip-shadow'
                }
            });

            $('#id_status_control').html(
                '<span class="badge '+((status === 0) ? 'badge-primary' : 'badge-danger')+'">'+((status === 0) ? 'banque à jour' : 'non a jour')+'</span>'
            );

            $('#id_status_control').qtip({
                content: {
                    text: function (event, api) {
                        var html = '';

                        for (var i = 0; i < controls.length; i++)
                        {
                            var v = controls[i],
                                ecart = parseFloat((v.sf - v.sd - v.m).toFixed(2)),
                                status = '';

                            if (parseInt(v.status) === 0) status = 'OK';
                            else if (parseInt(v.status) === 1) status = 'ABSCENCE TOTAL';
                            else status = 'RM';

                            html += '' +
                                '<table class="table table-bordered">' +
                                    '<tr>' +
                                        '<th colspan="2">' +v.bc+ '</th>' +
                                    '</tr>';

                            if (parseInt(v.status) === 1)
                            {
                                html += '<tr>' +
                                    '<th colspan="2">ABSCENCE TOTAL</th>' +
                                    '</tr>';
                            }

                            if (parseFloat(parseFloat(v.m).toFixed(2)) === 0)
                            {
                                html += '<tr>' +
                                            '<th colspan="2">Aucune ligne</th>' +
                                        '</tr>';
                                '</table>';
                                continue;
                            }

                            //if (ecart !== 0)
                            html +=
                                '<tr>' +
                                    '<th>Ecart </th>' +
                                    '<td>' +number_format(ecart, 2, ',', ' ')+ '</td>' +
                                '</tr>';

                            if (parseInt(v.status) !== 1)
                            {
                                if (parseInt(v.aJourA) === 0)
                                {
                                    html += '' +
                                        '<tr>' +
                                        '<th colspan="2">A jour</th>' +
                                        '</tr>';
                                }
                                else
                                {
                                    html += '' +
                                        '<tr>' +
                                        '<th>A jour à </th>' +
                                        '<td>M' +(parseInt(v.aJourA) < 0 ? v.aJourA : '')+ '</td>' +
                                        '</tr>';
                                }

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
                            }

                            html += '' +
                                '</table>';
                        }

                        return html;
                    }
                },
                position: position,
                style: {
                    classes: 'qtip-dark qtip-shadow'
                }
            });

            /*$('.tooltip-demo').tooltip({
                selector: "[data-toggle=tooltip]",
                container: "body",
                html: true
            });*/
        }
    });
}
