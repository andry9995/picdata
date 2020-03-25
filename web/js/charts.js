/**
 * Created by SITRAKA on 30/09/2016.
 */
var HCDefaults = $.extend(true, {}, Highcharts.getOptions(), {}),
    defaultOptions = Highcharts.getOptions();

function ResetOptions() {
    for (var prop in defaultOptions) {
        if (typeof defaultOptions[prop] !== 'function') delete defaultOptions[prop];
    }
    Highcharts.wrap(Highcharts.Chart.prototype, 'getContainer', function (proceed) {
        proceed.call(this);
        this.container.style.background = 'none';
    });
    Highcharts.setOptions(HCDefaults);
}

function to_chart_V2(element,code_graphe,result,theme)
{
    if(code_graphe === 'HISTO') to_chart_column_V2(element,result,theme);
    else if(code_graphe === 'COURBE') to_chart_area_V2(element,result,theme);
    else if(code_graphe === 'CAME') to_chart_pie_V2(element,result,theme);
    else if(code_graphe === 'LINE') to_chart_line_V2(element,result,theme);
    else if(code_graphe === 'AGAUGE') to_angular_gauge_V2(element,result,theme);
    else if(code_graphe === 'SGAUGE') to_solide_gauge_V2(element,result,theme);
    else if(code_graphe === 'SCIRCLE') return;// to_semi_circle(element,result,theme);
}

function to_chart_pie_V2(element,result,theme)
{
    var titre = result.titre,
        arrondirA = result.arrondirA,
        unite = result.unite,
        datas = result.datas;

    var opt = {
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            },
            renderTo: element.attr('id')
        },
        title: {
            text: titre
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y:,.'+arrondirA+'f}'+unite+'</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series:datas
        /*[{
         type: 'pie',
         name: 'Browser share',
         data: [
         ['Firefox', 45.0],
         ['IE', 26.8],
         {
         name: 'Chrome',
         y: 12.8,
         sliced: true,
         selected: true
         },
         ['Safari', 8.5],
         ['Opera', 6.2],
         ['Others', 0.7]
         ]
         }]*/
    };
    set_theme(theme);
    new Highcharts.Chart(opt);
}

function to_chart_column_V2(element,result,theme)
{
    var html_x = typeof result.html_x !== 'undefined' ? result.html_x : '',
        unite_x = typeof result.unite_x !== 'undefined' ? result.unite_x : '',
        html_y = typeof result.html_y !== 'undefined' ? result.html_y : '',
        unite_y = typeof result.unite_y !== 'undefined' ? result.unite_y : '',
        arrondirA = typeof result.arrondirA !== 'undefined' ? parseInt(result.arrondirA) : 2,
        categories = result.categories,
        series = result.datas,
        titre = typeof result.titre !== 'undefined' ? result.titre : '',
        sous_titre = typeof result.sousTitre !== 'undefined' ? result.sousTitre : '';
    var opt = {
        chart: {
            type: 'column',
            renderTo: element.attr('id')
            /*options3d: {
             enabled: true,
             alpha: 10,
             beta: 25,
             depth: 70
             }*/
        },
        title: {
            text: titre
        },
        subtitle: {
            text: sous_titre
        },
        xAxis: {
            title: {
                text: html_y
            },
            categories: categories,
            crosshair: true
        },
        yAxis: {
            title: {
                text: html_y
            }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y:,.'+arrondirA+'f}</b>',
            /*headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:,.'+arrondirA+'f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true*/
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            },
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function(e) {
                            if (typeof function_click_in_chart !== "undefined") {
                                var element = $(e.target),
                                    container = element.closest('div.highcharts-container'),
                                    category = this.category,
                                    name = e.point.series.name;
                                function_click_in_chart(container,category,name);
                            }
                        }
                    }
                }
            }
        },
        series: series
    };
    set_theme(theme);
    new Highcharts.Chart(opt);
}

function to_chart_line_V2(element,result,theme)
{
    var categories = result.categories,
        series = result.datas,
        arrondirA = result.arrondirA,
        titre = typeof result.titre !== 'undefined' ? result.titre : '',
        sous_titre = typeof result.sousTitre !== 'undefined' ? result.sousTitre : '',
        html_x = typeof result.html_x !== 'undefined' ? result.html_x : '',
        unite_x = typeof result.unite_x !== 'undefined' ? result.unite_x : '',
        html_y = typeof result.html_y !== 'undefined' ? result.html_y : '',
        unite_y = typeof result.unite_y !== 'undefined' ? result.unite_y : '',
        unite = typeof result.unite !== 'undefined' ? result.unite : '';

    var opt = {
        chart: {
            renderTo: element.attr('id'),
        },
        title: {
            text: titre,
            x: -20 //center
        },
        subtitle: {
            text: sous_titre,
            x: -20
        },
        xAxis: {
            categories: categories
        },
        yAxis: {
            title: {
                text: html_y
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y:,.'+arrondirA+'f}</b>',
        },
        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function(e) {
                            if (typeof function_click_in_chart !== "undefined") {
                                var element = $(e.target),
                                    container = element.closest('div.highcharts-container'),
                                    category = this.category,
                                    name = e.point.series.name;
                                function_click_in_chart(container,category,name);
                            }
                        }
                    }
                }
            }
        },
        series: series
    };
    set_theme(theme);
    new Highcharts.Chart(opt);
}

function to_chart_area_V2(element,result,theme)
{
    var categories = result.categories,
        series = result.datas,
        arrondirA = result.arrondirA,
        titre = typeof result.titre !== 'undefined' ? result.titre : '',
        sous_titre = typeof result.sousTitre !== 'undefined' ? result.sousTitre : '',
        html_x = typeof result.html_x !== 'undefined' ? result.html_x : '',
        unite_x = typeof result.unite_x !== 'undefined' ? result.unite_x : '',
        html_y = typeof result.html_y !== 'undefined' ? result.html_y : '',
        unite_y = typeof result.unite_y !== 'undefined' ? result.unite_y : '';

    var opt = {
        chart: {
            type: 'area',
            renderTo: element.attr('id')
        },
        title: {
            text: titre
        },
        subtitle: {
            text: sous_titre
        },
        xAxis: {
            categories: categories,
            title: {
                text:html_x
            },
            allowDecimals: false
        },
        yAxis: {
            title: {
                text: html_y
            }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y:,.'+arrondirA+'f}</b>',
        },
        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function(e) {
                            if (typeof function_click_in_chart !== "undefined") {
                                var element = $(e.target),
                                    container = element.closest('div.highcharts-container'),
                                    category = this.category,
                                    name = e.point.series.name;
                                function_click_in_chart(container,category,name);
                            }
                        }
                    }
                }
            }
        },
        series:series
    };
    set_theme(theme);
    new Highcharts.Chart(opt);
}

function set_theme(theme)
{
    ResetOptions();
    if(theme == 0) return;
    else if(theme == 1)
    {
        Highcharts.theme = {
            colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
                '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
            chart: {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
                    stops: [
                        [0, '#2a2a2b'],
                        [1, '#3e3e40']
                    ]
                },
                style: {
                    fontFamily: '\'Unica One\', sans-serif'
                },
                plotBorderColor: '#606063'
            },
            title: {
                style: {
                    color: '#E0E0E3',
                    textTransform: 'uppercase',
                    fontSize: '20px'
                }
            },
            subtitle: {
                style: {
                    color: '#E0E0E3',
                    textTransform: 'uppercase'
                }
            },
            xAxis: {
                gridLineColor: '#707073',
                labels: {
                    style: {
                        color: '#E0E0E3'
                    }
                },
                lineColor: '#707073',
                minorGridLineColor: '#505053',
                tickColor: '#707073',
                title: {
                    style: {
                        color: '#A0A0A3'

                    }
                }
            },
            yAxis: {
                gridLineColor: '#707073',
                labels: {
                    style: {
                        color: '#E0E0E3'
                    }
                },
                lineColor: '#707073',
                minorGridLineColor: '#505053',
                tickColor: '#707073',
                tickWidth: 1,
                title: {
                    style: {
                        color: '#A0A0A3'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                style: {
                    color: '#F0F0F0'
                }
            },
            plotOptions: {
                series: {
                    dataLabels: {
                        color: '#B0B0B3'
                    },
                    marker: {
                        lineColor: '#333'
                    }
                },
                boxplot: {
                    fillColor: '#505053'
                },
                candlestick: {
                    lineColor: 'white'
                },
                errorbar: {
                    color: 'white'
                }
            },
            legend: {
                itemStyle: {
                    color: '#E0E0E3'
                },
                itemHoverStyle: {
                    color: '#FFF'
                },
                itemHiddenStyle: {
                    color: '#606063'
                }
            },
            credits: {
                style: {
                    color: '#666'
                }
            },
            labels: {
                style: {
                    color: '#707073'
                }
            },

            drilldown: {
                activeAxisLabelStyle: {
                    color: '#F0F0F3'
                },
                activeDataLabelStyle: {
                    color: '#F0F0F3'
                }
            },

            navigation: {
                buttonOptions: {
                    symbolStroke: '#DDDDDD',
                    theme: {
                        fill: '#505053'
                    }
                }
            },

            // scroll charts
            rangeSelector: {
                buttonTheme: {
                    fill: '#505053',
                    stroke: '#000000',
                    style: {
                        color: '#CCC'
                    },
                    states: {
                        hover: {
                            fill: '#707073',
                            stroke: '#000000',
                            style: {
                                color: 'white'
                            }
                        },
                        select: {
                            fill: '#000003',
                            stroke: '#000000',
                            style: {
                                color: 'white'
                            }
                        }
                    }
                },
                inputBoxBorderColor: '#505053',
                inputStyle: {
                    backgroundColor: '#333',
                    color: 'silver'
                },
                labelStyle: {
                    color: 'silver'
                }
            },

            navigator: {
                handles: {
                    backgroundColor: '#666',
                    borderColor: '#AAA'
                },
                outlineColor: '#CCC',
                maskFill: 'rgba(255,255,255,0.1)',
                series: {
                    color: '#7798BF',
                    lineColor: '#A6C7ED'
                },
                xAxis: {
                    gridLineColor: '#505053'
                }
            },

            scrollbar: {
                barBackgroundColor: '#808083',
                barBorderColor: '#808083',
                buttonArrowColor: '#CCC',
                buttonBackgroundColor: '#606063',
                buttonBorderColor: '#606063',
                rifleColor: '#FFF',
                trackBackgroundColor: '#404043',
                trackBorderColor: '#404043'
            },

            // special colors for some of the
            legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
            background2: '#505053',
            dataLabelsColor: '#B0B0B3',
            textColor: '#C0C0C0',
            contrastTextColor: '#F0F0F3',
            maskColor: 'rgba(255,255,255,0.3)'
        };
    }
    else if(theme == 2)
    {
        Highcharts.wrap(Highcharts.Chart.prototype, 'getContainer', function (proceed) {
            proceed.call(this);
            this.container.style.background = 'url(http://www.highcharts.com/samples/graphics/sand.png)';
        });
        Highcharts.theme = {
            colors: ['#f45b5b', '#8085e9', '#8d4654', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
                '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
            chart: {
                backgroundColor: null,
                style: {
                    fontFamily: 'Signika, serif'
                }
            },
            title: {
                style: {
                    color: 'black',
                    fontSize: '16px',
                    fontWeight: 'bold'
                }
            },
            subtitle: {
                style: {
                    color: 'black'
                }
            },
            tooltip: {
                borderWidth: 0
            },
            legend: {
                itemStyle: {
                    fontWeight: 'bold',
                    fontSize: '13px'
                }
            },
            xAxis: {
                labels: {
                    style: {
                        color: '#6e6e70'
                    }
                }
            },
            yAxis: {
                labels: {
                    style: {
                        color: '#6e6e70'
                    }
                }
            },
            plotOptions: {
                series: {
                    shadow: true
                },
                candlestick: {
                    lineColor: '#404048'
                },
                map: {
                    shadow: false
                }
            },

            // Highstock specific
            navigator: {
                xAxis: {
                    gridLineColor: '#D0D0D8'
                }
            },
            rangeSelector: {
                buttonTheme: {
                    fill: 'white',
                    stroke: '#C0C0C8',
                    'stroke-width': 1,
                    states: {
                        select: {
                            fill: '#D0D0D8'
                        }
                    }
                }
            },
            scrollbar: {
                trackBorderColor: '#C0C0C8'
            },
            // General
            background2: '#E0E0E8'
        };
    }
    else if(theme == 3)
    {
        Highcharts.theme = {
            colors: ['#7cb5ec', '#f7a35c', '#90ee7e', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
                '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
            chart: {
                backgroundColor: null,
                style: {
                    fontFamily: 'Dosis, sans-serif'
                }
            },
            title: {
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold',
                    textTransform: 'uppercase'
                }
            },
            tooltip: {
                borderWidth: 0,
                backgroundColor: 'rgba(219,219,216,0.8)',
                shadow: false
            },
            legend: {
                itemStyle: {
                    fontWeight: 'bold',
                    fontSize: '13px'
                }
            },
            xAxis: {
                gridLineWidth: 1,
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yAxis: {
                minorTickInterval: 'auto',
                title: {
                    style: {
                        textTransform: 'uppercase'
                    }
                },
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            plotOptions: {
                candlestick: {
                    lineColor: '#404048'
                }
            },


            // General
            background2: '#F0F0EA'

        };

    }
    else if(theme == 4)
    {
        Highcharts.theme = {
            "colors": ["#d35400", "#2980b9", "#2ecc71", "#f1c40f", "#2c3e50", "#7f8c8d"],
            "chart": {
                "style": {
                    "fontFamily": "Roboto"
                }
            },
            "title": {
                "align": "left",
                "style": {
                    "fontFamily": "Roboto Condensed",
                    "fontWeight": "bold"
                }
            },
            "subtitle": {
                "align": "left",
                "style": {
                    "fontFamily": "Roboto Condensed"
                }
            },
            "legend": {
                "align": "right",
                "verticalAlign": "bottom"
            },
            "xAxis": {
                "gridLineWidth": 1,
                "gridLineColor": "#F3F3F3",
                "lineColor": "#F3F3F3",
                "minorGridLineColor": "#F3F3F3",
                "tickColor": "#F3F3F3",
                "tickWidth": 1
            },
            "yAxis": {
                "gridLineColor": "#F3F3F3",
                "lineColor": "#F3F3F3",
                "minorGridLineColor": "#F3F3F3",
                "tickColor": "#F3F3F3",
                "tickWidth": 1
            },
            "plotOptions": {
                "line": {
                    "marker": {
                        "enabled": false
                    }
                },
                "spline": {
                    "marker": {
                        "enabled": false
                    }
                },
                "area": {
                    "marker": {
                        "enabled": false
                    }
                },
                "areaspline": {
                    "marker": {
                        "enabled": false
                    }
                }
            }
        };
    }
    else if(theme == 5)
    {
        Highcharts.theme = {
            "colors": ["#A9CF54", "#C23C2A", "#FFFFFF", "#979797", "#FBB829"],
            "chart": {
                "backgroundColor": "#242F39"
            },
            "legend": {
                "enabled": true,
                "align": "right",
                "verticalAlign": "bottom",
                "itemStyle": {
                    "color": "#C0C0C0"
                },
                "itemHoverStyle": {
                    "color": "#C0C0C0"
                },
                "itemHiddenStyle": {
                    "color": "#444444"
                }
            },
            "title": {
                "text": {},
                "style": {
                    "color": "#FFFFFF"
                }
            },
            "tooltip": {
                "backgroundColor": "#1C242D",
                "borderColor": "#1C242D",
                "borderWidth": 1,
                "borderRadius": 0,
                "style": {
                    "color": "#FFFFFF"
                }
            },
            "subtitle": {
                "style": {
                    "color": "#666666"
                }
            },
            "xAxis": {
                "gridLineColor": "#2E3740",
                "gridLineWidth": 1,
                "labels": {
                    "style": {
                        "color": "#525252"
                    }
                },
                "lineColor": "#2E3740",
                "tickColor": "#2E3740",
                "title": {
                    "style": {
                        "color": "#FFFFFF"
                    },
                    "text": {}
                }
            },
            "yAxis": {
                "gridLineColor": "#2E3740",
                "gridLineWidth": 1,
                "labels": {
                    "style": {
                        "color": "#525252"
                    },
                    "lineColor": "#2E3740",
                    "tickColor": "#2E3740",
                    "title": {
                        "style": {
                            "color": "#FFFFFF"
                        },
                        "text": {}
                    }
                }
            }
        };
    }
    else if(theme == 6)
    {
        Highcharts.theme = {
            "colors": ["#FF2700", "#008FD5", "#77AB43", "#636464", "#C4C4C4"],
            "chart": {
                "backgroundColor": "#F0F0F0",
                "plotBorderColor": "#606063",
                "style": {
                    "fontFamily": "Roboto",
                    "color": "#3C3C3C"
                }
            },
            "title": {
                "align": "left",
                "style": {
                    "fontWeight": "bold"
                }
            },
            "subtitle": {
                "align": "left"
            },
            "xAxis": {
                "gridLineWidth": 1,
                "gridLineColor": "#D7D7D8",
                "labels": {
                    "style": {
                        "fontFamily": "Unica One, sans-serif",
                        "color": "#3C3C3C"
                    }
                },
                "lineColor": "#D7D7D8",
                "minorGridLineColor": "#505053",
                "tickColor": "#D7D7D8",
                "tickWidth": 1,
                "title": {
                    "style": {
                        "color": "#A0A0A3"
                    }
                }
            },
            "yAxis": {
                "gridLineColor": "#D7D7D8",
                "labels": {
                    "style": {
                        "fontFamily": "Unica One, sans-serif",
                        "color": "#3C3C3C"
                    }
                },
                "lineColor": "#D7D7D8",
                "minorGridLineColor": "#505053",
                "tickColor": "#D7D7D8",
                "tickWidth": 1,
                "title": {
                    "style": {
                        "color": "#A0A0A3"
                    }
                }
            },
            "tooltip": {
                "backgroundColor": "rgba(0, 0, 0, 0.85)",
                "style": {
                    "color": "#F0F0F0"
                }
            },
            "legend": {
                "itemStyle": {
                    "color": "#3C3C3C"
                },
                "itemHiddenStyle": {
                    "color": "#606063"
                }
            },
            "credits": {
                "style": {
                    "color": "#666"
                }
            },
            "labels": {
                "style": {
                    "color": "#D7D7D8"
                }
            },
            "legendBackgroundColor": "rgba(0, 0, 0, 0.5)",
            "background2": "#505053",
            "dataLabelsColor": "#B0B0B3",
            "textColor": "#C0C0C0",
            "contrastTextColor": "#F0F0F3",
            "maskColor": "rgba(255,255,255,0.3)"
        };
    }
    else if(theme == 7)
    {
        Highcharts.theme = {
            "colors": ["#6794a7", "#014d64", "#76c0c1", "#01a2d9", "#7ad2f6", "#00887d", "#adadad", "#7bd3f6", "#7c260b", "#ee8f71", "#76c0c1", "#a18376"],
            "chart": {
                "backgroundColor": "#d5e4eb",
                "style": {
                    "fontFamily": "Droid Sans",
                    "color": "#3C3C3C"
                }
            },
            "title": {
                "align": "left",
                "style": {
                    "fontWeight": "bold"
                }
            },
            "subtitle": {
                "align": "left"
            },
            "yAxis": {
                "gridLineColor": "#FFFFFF",
                "lineColor": "#FFFFFF",
                "minorGridLineColor": "#FFFFFF",
                "tickColor": "#D7D7D8",
                "tickWidth": 1,
                "title": {
                    "style": {
                        "color": "#A0A0A3"
                    }
                }
            },
            "tooltip": {
                "backgroundColor": "#FFFFFF",
                "borderColor": "#76c0c1",
                "style": {
                    "color": "#000000"
                }
            },
            "legend": {
                "itemStyle": {
                    "color": "#3C3C3C"
                },
                "itemHiddenStyle": {
                    "color": "#606063"
                }
            },
            "credits": {
                "style": {
                    "color": "#666"
                }
            },
            "labels": {
                "style": {
                    "color": "#D7D7D8"
                }
            },
            "drilldown": {
                "activeAxisLabelStyle": {
                    "color": "#F0F0F3"
                },
                "activeDataLabelStyle": {
                    "color": "#F0F0F3"
                }
            },
            "navigation": {
                "buttonOptions": {
                    "symbolStroke": "#DDDDDD",
                    "theme": {
                        "fill": "#505053"
                    }
                }
            },
            "legendBackgroundColor": "rgba(0, 0, 0, 0.5)",
            "background2": "#505053",
            "dataLabelsColor": "#B0B0B3",
            "textColor": "#C0C0C0",
            "contrastTextColor": "#F0F0F3",
            "maskColor": "rgba(255,255,255,0.3)"
        };
    }
    else if(theme == 8)
    {
        Highcharts.theme = {
            "colors": ["#00AACC", "#FF4E00", "#B90000", "#5F9B0A", "#CD6723"],
            "chart": {
                "backgroundColor": {
                    "linearGradient": [
                        0,
                        0,
                        0,
                        150
                    ],
                    "stops": [
                        [
                            0,
                            "#CAE1F4"
                        ],
                        [
                            1,
                            "#EEEEEE"
                        ]
                    ]
                },
                "style": {
                    "fontFamily": "Open Sans"
                }
            },
            "title": {
                "align": "left"
            },
            "subtitle": {
                "align": "left"
            },
            "legend": {
                "align": "right",
                "verticalAlign": "bottom"
            },
            "xAxis": {
                "gridLineWidth": 1,
                "gridLineColor": "#F3F3F3",
                "lineColor": "#F3F3F3",
                "minorGridLineColor": "#F3F3F3",
                "tickColor": "#F3F3F3",
                "tickWidth": 1
            },
            "yAxis": {
                "gridLineColor": "#F3F3F3",
                "lineColor": "#F3F3F3",
                "minorGridLineColor": "#F3F3F3",
                "tickColor": "#F3F3F3",
                "tickWidth": 1
            }
        };
    }
    else if(theme == 9)
    {
        Highcharts.theme = {
            "colors": [
                "#737373",
                "#D8D7D6",
                "#B2B0AD",
                "#8C8984"
            ],
            "chart": {
                "style": {
                    "fontFamily": "Cardo"
                }
            },
            "xAxis": {
                "lineWidth": 0,
                "minorGridLineWidth": 0,
                "lineColor": "transparent",
                "tickColor": "#737373"
            },
            "yAxis": {
                "lineWidth": 0,
                "minorGridLineWidth": 0,
                "lineColor": "transparent",
                "tickColor": "#737373",
                "tickWidth": 1,
                "gridLineColor": "transparent"
            },
            "legend": {
                "enabled": false
            }
        };
    }
    Highcharts.setOptions(Highcharts.theme);
}

function to_chart(element,code_graphe,series,html_x,unite_x,html_y,unite_y,theme)
{
    if(code_graphe == 'HISTO') to_chart_column(element,series,html_x,unite_x,html_y,unite_y,theme);
    else if(code_graphe == 'COURBE') to_chart_area(element,series,html_x,unite_x,html_y,unite_y,theme);
    else if(code_graphe == 'CAME') to_chart_pie(element,series,html_x,unite_x,html_y,unite_y,theme);
    else if(code_graphe == 'LINE') to_chart_line(element,series,html_x,unite_x,html_y,unite_y,theme);
    else if(code_graphe == 'AGAUGE') to_angular_gauge(element,series,html_x,unite_x,html_y,unite_y,theme);
    else if(code_graphe == 'SGAUGE') to_solide_gauge(element,series,html_x,unite_x,html_y,unite_y,theme);
    else if(code_graphe == 'SCIRCLE') to_semi_circle(element,series,html_x,unite_x,html_y,unite_y,theme);
}

/**
 *
 * @param element
 * @param series
 * @param html_x
 * @param unite_x
 * @param html_y
 * @param unite_y
 * @param theme
 */
function to_semi_circle(element,series,html_x,unite_x,html_y,unite_y,theme)
{
    var data = series.series,
        titre = typeof series.titre !== 'undefined' ? series.titre : '',
        arrondirA = typeof series.arrondirA !== 'undefined' ? series.arrondirA : 0;
    data.push({
        name: 'Proprietary or Undetectable',
        y: 0.2,
        dataLabels: {
            enabled: false
        }
    });
    var opt = {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false,
            renderTo: element.attr('id')
        },
        title: {
            text: titre,
            align: 'center',
            verticalAlign: 'bottom',
            y: 40
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y:,.'+arrondirA+'f}</b>'
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '75%']
            }
        },
        series: [{
            type: 'pie',
            name: titre,
            innerSize: '50%',
            data: series.series/*[
                ['Firefox',   10.38],
                ['IE',       56.33],
                ['Chrome', 24.03],
                ['Safari',    4.77],
                ['Opera',     0.91],
                {
                    name: 'Proprietary or Undetectable',
                    y: 0.2,
                    dataLabels: {
                        enabled: false
                    }
                }
            ]*/
        }]
    };
    set_theme(theme);
    new Highcharts.Chart(opt);
}

function to_chart_line(element,data,html_x,unite_x,html_y,unite_y,theme)
{
    var categories = data.categories,
        series = data.series,
        arrondirA = data.arrondirA,
        titre = typeof data.titre !== 'undefined' ? data.titre : '',
        sous_titre = typeof data.sousTitre !== 'undefined' ? data.sousTitre : '';

    html_x = typeof html_x !== 'undefined' ? html_x : '';
    unite_x = typeof unite_x !== 'undefined' ? unite_x : '';
    html_y = typeof html_y !== 'undefined' ? html_y : '';
    unite_y = typeof unite_y !== 'undefined' ? unite_y : '';

    var opt = {
        chart: {
            renderTo: element.attr('id')
        },
        title: {
            text: titre,
            x: -20 //center
        },
        subtitle: {
            text: sous_titre,
            x: -20
        },
        xAxis: {
            categories: categories
        },
        yAxis: {
            title: {
                text: html_y
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: unite_y
        },
        series: series
    };
    set_theme(theme);
    new Highcharts.Chart(opt);
}

function to_solide_gauge(element,series,html_x,unite_x,html_y,unite_y,theme)
{
    var val = series['result'],
        parametres = series['parametre'],
        unite = typeof parametres.unite !== 'undefined' ? parametres.unite : '',
        min = typeof parametres.min !== 'undefined' ? parametres.min : 0,
        max = typeof parametres.max !== 'undefined' ? parametres.max : 100,
        plots = typeof parametres.plots !== 'undefined' ? parametres.plots :
            [{ from: 0, to: 70, color: '#d3d3d3' },
                { from: 70, to: 80, color: '#55BF3B' },
                { from: 80, to: 90, color: '#DDDF0D' },
                { from: 90, to: 100, color: '#DF5353' }],
        titre = typeof parametres.titre !== 'undefined' ? parametres.titre : '',
        sous_titre = typeof parametres.sousTitre !== 'undefined' ? parametres.sousTitre : '',
        description = typeof parametres.description !== 'undefined' ? parametres.description : '',
        arrondirA = typeof parametres.arrondirA !== 'undefined' ? parametres.arrondirA : 0;
    var opt = {
        chart: {
            type: 'solidgauge',
            renderTo: element.attr('id')
        },
        title: null,
        pane: {
            center: ['50%', '85%'],
            size: '140%',
            startAngle: -90,
            endAngle: 90,
            background: {
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },
        tooltip: {
            enabled: false
        },
        yAxis: {
            stops: [
                [0.1, '#55BF3B'],
                [0.5, '#DDDF0D'],
                [0.9, '#DF5353']
            ],
            lineWidth: 0,
            minorTickInterval: null,
            tickAmount: 2,
            title: {
                y: -70,
                text: titre
            },
            labels: {
                y: 16
            },
            min: min,
            max: max
        },
        plotOptions: {
            solidgauge: {
                dataLabels: {
                    y: 5,
                    borderWidth: 0,
                    useHTML: true
                }
            }
        },

        credits: {
            enabled: false
        },

        series: [{
            name: description,
            data: [val],
            dataLabels: {
                format:
                '<div style="text-align:center"><span style="font-size:18px;color:' +
                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{point.y:,.'+arrondirA+'f}</span><br/>' +
                '<span style="font-size:12px;color:silver">'+unite+'</span>'+
                '</div>'
            },
            tooltip: {
                valueSuffix: ' km/h'
            }
        }]
    };
    set_theme(theme);
    new Highcharts.Chart(opt);
}

function to_chart_pie(element,param,html_x,unite_x,html_y,unite_y,theme)
{
    param = param.series;
    var titre = param.titre,
        arrondirA = param.arrondirA,
        unite = param.unite,
        datas = param.series;

    var opt = {
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            },
            renderTo: element.attr('id')
        },
        title: {
            text: titre
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:,.'+arrondirA+'f}'+unite+'</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series:datas /*[{
            type: 'pie',
            name: 'Browser share',
            data: [
                ['Firefox', 45.0],
                ['IE', 26.8],
                {
                    name: 'Chrome',
                    y: 12.8,
                    sliced: true,
                    selected: true
                },
                ['Safari', 8.5],
                ['Opera', 6.2],
                ['Others', 0.7]
            ]
        }]*/
    };
    set_theme(theme);
    new Highcharts.Chart(opt);
}

function to_angular_gauge(element,series,html_x,unite_x,html_y,unite_y,theme)
{
    var val = series['result'],
        parametres = series['parametre'],
        unite = typeof parametres.unite !== 'undefined' ? parametres.unite : '',
        min = typeof parametres.min !== 'undefined' ? parametres.min : 0,
        max = typeof parametres.max !== 'undefined' ? parametres.max : 100,
        plots = typeof parametres.plots !== 'undefined' ? parametres.plots :
            [{ from: 0, to: 70, color: '#d3d3d3' },
             { from: 70, to: 80, color: '#55BF3B' },
             { from: 80, to: 90, color: '#DDDF0D' },
             { from: 90, to: 100, color: '#DF5353' }],
        titre = typeof parametres.titre !== 'undefined' ? parametres.titre : '',
        sous_titre = typeof parametres.sousTitre !== 'undefined' ? parametres.sousTitre : '',
        description = typeof parametres.description !== 'undefined' ? parametres.description : '';
    var opt = {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                renderTo: element.attr('id')
            },

            title: {
                text: titre
            },

            pane: {
                startAngle: -150,
                endAngle: 150,
                background: [{
                    backgroundColor: {
                        linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                        stops: [
                            [0, '#FFF'],
                            [1, '#333']
                        ]
                    },
                    borderWidth: 0,
                    outerRadius: '109%'
                }, {
                    backgroundColor: {
                        linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                        stops: [
                            [0, '#333'],
                            [1, '#FFF']
                        ]
                    },
                    borderWidth: 1,
                    outerRadius: '107%'
                }, {
                    // default background
                }, {
                    backgroundColor: '#DDD',
                    borderWidth: 0,
                    outerRadius: '105%',
                    innerRadius: '103%'
                }]
            },

            // the value axis
            yAxis: {
                min: min,
                max: max,

                minorTickInterval: 'auto',
                minorTickWidth: 1,
                minorTickLength: 10,
                minorTickPosition: 'inside',
                minorTickColor: '#666',

                tickPixelInterval: 30,
                tickWidth: 2,
                tickPosition: 'inside',
                tickLength: 10,
                tickColor: '#666',
                labels: {
                    step: 2,
                    rotation: 'auto'
                },
                title: {
                    text: unite
                },
                plotBands: plots
            },
            series: [{
                name: description,
                data: [val],
                tooltip: {
                    valueSuffix: ' ' + unite
                }
            }]
        };

    set_theme(theme);
    new Highcharts.Chart(opt);
}

/**
 *
 * @param element
 * @param data
 * @param html_x
 * @param unite_x
 * @param html_y
 * @param unite_y
 * @param theme
 */
function to_chart_column(element,data,html_x,unite_x,html_y,unite_y,theme)
{
    html_x = typeof html_x !== 'undefined' ? html_x : '';
    unite_x = typeof unite_x !== 'undefined' ? unite_x : '';
    html_y = typeof html_y !== 'undefined' ? html_y : '';
    unite_y = typeof unite_y !== 'undefined' ? unite_y : '';

    var arrondirA = parseInt(data.arrondirA),
        categories = data.categories,
        series = data.series,
        titre = typeof data.titre !== 'undefined' ? data.titre : '',
        sous_titre = typeof data.sousTitre !== 'undefined' ? data.sousTitre : '';
    var opt = {
            chart: {
                type: 'column',
                renderTo: element.attr('id')
                /*options3d: {
                    enabled: true,
                    alpha: 10,
                    beta: 25,
                    depth: 70
                }*/
            },
            title: {
                text: titre
            },
            subtitle: {
                text: sous_titre
            },
            xAxis: {
                title: {
                    text: html_y
                },
                categories: categories,
                    crosshair: true
            },
            yAxis: {
                title: {
                    text: html_y
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:,.'+arrondirA+'f}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                        borderWidth: 0
                }
            },
            series: series
        };
    set_theme(theme);
    new Highcharts.Chart(opt);
}

/**
 *
 * @param element
 * @param data
 * @param html_x
 * @param unite_x
 * @param html_y
 * @param unite_y
 * @param theme
 */
function to_chart_area(element,data,html_x,unite_x,html_y,unite_y,theme)
{
    var categories = data.categories,
        series = data.series,
        arrondirA = data.arrondirA,
        titre = typeof data.titre !== 'undefined' ? data.titre : '',
        sous_titre = typeof data.sousTitre !== 'undefined' ? data.sousTitre : '';

    html_x = typeof html_x !== 'undefined' ? html_x : '';
    unite_x = typeof unite_x !== 'undefined' ? unite_x : '';
    html_y = typeof html_y !== 'undefined' ? html_y : '';
    unite_y = typeof unite_y !== 'undefined' ? unite_y : '';

    var opt = {
        chart: {
            type: 'area',
            renderTo: element.attr('id')
        },
        title: {
            text: titre
        },
        subtitle: {
            text: sous_titre
        },
        xAxis: {
            categories: categories,
            title: {
                text:html_x
            },
            allowDecimals: false
        },
        yAxis: {
            title: {
                text: html_y
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:,.'+arrondirA+'f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        series:series
    };
    set_theme(theme);
    new Highcharts.Chart(opt);
}

/**
 *
 * @param element
 * @param datas
 * @param html_x
 * @param unite_x
 * @param html_y
 * @param unite_y
 * @param theme
 */
function to_chart_donut(element,datas,html_x,unite_x,html_y,unite_y,theme)
{
    var categories = datas.categories,
        data = datas.series,
        arrondirA = datas.arrondirA,
        colors = Highcharts.getOptions().colors,
        titre = typeof datas.titre !== 'undefined' ? datas.titre : '',
        sous_titre = typeof datas.sousTitre !== 'undefined' ? datas.sousTitre : '';

    for(i = 0; i < data.length; i++)
    {
        data[i].color = colors[i];
        data[i].drilldown.color = colors[i];
    }

    var
        browserData = [],
        versionsData = [],
        i,
        j,
        dataLen = data.length,
        drillDataLen,
        brightness;

    for (i = 0; i < dataLen; i += 1) {
        browserData.push({
            name: categories[i],
            y:  data[i].y,
            color: data[i].color
        });

        drillDataLen = data[i].drilldown.data.length;
        for (j = 0; j < drillDataLen; j += 1) {
            brightness = 0.2 - (j / drillDataLen) / 5;
            versionsData.push({
                name: data[i].drilldown.categories[j],
                y: data[i].drilldown.data[j],
                color: Highcharts.Color(data[i].color).brighten(brightness).get()
            });
        }
    }

    var opt = {
        chart: {
            type: 'pie',
            renderTo: element.attr('id'),
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        title: {
            text: titre
        },
        subtitle: {
            text: sous_titre
        },
        yAxis: {
            title: {
                text: html_y
            }
        },
        plotOptions: {
            pie: {
                shadow: false,
                center: ['50%', '50%']
            }
        },
        tooltip: {
            valueSuffix: ''
        },
        series: [{
            name: 'Exercice',
            data: browserData,
            size: '60%',
            dataLabels: {
                formatter: function () {
                    return this.y > 5 ? this.point.name : null;
                },
                color: '#ffffff',
                distance: -30
            }
        }, {
            name: 'Mois',
            data: versionsData,
            size: '80%',
            innerSize: '60%',
            dataLabels: {
                formatter: function () {
                    return this.y > 1 ? '<b>' + this.point.name + ':</b> ' + number_format(this.y,arrondirA,',',' ') + unite_y : null;
                }
            }
        }]
    };
    set_theme(theme);
    new Highcharts.Chart(opt);
}