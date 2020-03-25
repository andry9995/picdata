/**
 * Created by SITRAKA on 17/10/2019.
 */

$(document).ready(function(){
    $(document).on('click','.cl_base',function(){
        if (!$(this).hasClass('active'))
        {
            $('.cl_base_libelle').text($(this).text());
            $('.cl_base').removeClass('active');
            $(this).addClass('active');
            charger_tresorerie();

            if (parseInt($(this).attr('data-base')) === 2)
            {
                $('.cl_mois_libelle').removeClass('hidden');
                $('#id_container_mois').removeClass('hidden');
            }
            else
            {
                $('.cl_mois_libelle').addClass('hidden');
                $('#id_container_mois').addClass('hidden');
            }
        }
    });

    $(document).on('click','.cl_treso_mois',function(){
        if (!$(this).hasClass('active'))
        {
            $('.cl_treso_mois').removeClass('active');
            $(this).addClass('active');
            libelle_mois();
            charger_tresorerie();
        }
    });
});

function charger_mois()
{
    if (!dossier_selected()) return;
    $.ajax({
        data: {
            dossier: $('#dossier').val()
        },
        url: Routing.generate('treso_mois'),
        type: 'POST',
        dataType: 'html',
        async: false,
        success: function(data){
            test_security(data);
            $('#id_container_mois').html(data);
            libelle_mois();
        }
    });
}

function libelle_mois()
{
    $('.cl_mois_libelle').text($('#id_container_mois').find('.cl_treso_mois.active').text());
}

function charger_tresorerie()
{
    $('#id_container_treso').html(
        '<div id="high_chart_treso" style="min-width: 310px; height: 400px; margin: 0 auto"></div>'
    );

    if (!dossier_selected()) return;

    var base = parseInt($('.cl_base.active').attr('data-base'));

    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            base: base,
            exercice : parseInt($('#exercice').val()),
            mois: $('#id_container_mois').find('.cl_treso_mois.active').attr('data-mois')
        },
        url: Routing.generate('treso_tresorerie'),
        type: 'POST',
        dataType: 'html',
        success: function(data){
            test_security(data);

            /*$('#high_chart_treso').html(data);
            return;*/

            var result = $.parseJSON(data),
                series = result.series,
                categories = result.categories,
                serieSolde = result.solde;

            serieSolde.marker = {
                lineWidth: 2,
                lineColor: Highcharts.getOptions().colors[3],
                fillColor: 'white'
            };
            series.push(serieSolde);


            Highcharts.chart('high_chart_treso', {
                title: {
                    text: ''
                },
                xAxis: {
                    categories: categories
                },
                plotOptions: {
                    column: {
                        stacking: 'normal'
                    }
                },
                series: series
            });
        }
    });
}
