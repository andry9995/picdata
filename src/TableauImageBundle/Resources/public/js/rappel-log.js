$(function() {
    var window_height = window.innerHeight,
        rappel_log_container = $('#rappel-log'),
        rappel_log_client = $('#client-rappel-log'),
        rappel_log_site = $('#site-rappel-log'),
        rappel_log_dossier = $('#dossier-rappel-log'),
        rappel_log_exercice = $('#exercice-rappel-log'),
        tableau_rappel_log = $('#js_rappel_log1');
    rappel_log_container.height(window_height - 200);
    var rappel_log_grid_width = rappel_log_container.width(),
        rappel_log_grid_height = rappel_log_container.height() - 100;

    tableau_rappel_log.jqGrid({
        datatype: 'local',
        loadonce: true,
        sortable: false,
        height: rappel_log_grid_height,
        width: rappel_log_grid_width,
        shrinkToFit: true,
        viewrecords: true,
        hidegrid: true,
        rownumbers: true,
        rownumWidth: 35,
        rowNum: 2000,
        rowList: [2000, 3000, 5000],
        pager: '#pager_rappel_log',
        colNames: ['Dossiers', 'Date Envoi', 'Sujet', 'Statut'],
        colModel: [
            {
                name: 'rappel-log-dossier', index: 'rappel-log-dossier', editable: false, sortable: true, width: 200, classes: 'js-rappel-log-dossier'
            },
            {
                name: 'rappel-log-date', index: 'rappel-log-date', editable: false, sortable: true, width: 100, fixed: true, align: 'center',
                formatter: 'date', formatoptions: { srcformat: 'Y-m-d', newformat: 'd/m/Y'}, classes: 'js-rappel-log-date'
            },
            {
                name: 'rappel-log-sujet', index: 'rappel-log-sujet', editable: false, sortable: true, width: 350, fixed: true, classes: 'js-rappel-log-sujet'
            },
            {
                name: 'rappel-log-status', index: 'rappel-log-status', editable: false, sortable: true, width: 120, align: 'center', fixed: true, classes: 'js-rappel-log-status'
            }
        ]
    });

    setTimeout(function() {
        updateSizeGridRappelLog();
    }, 500);

    /** SELECTION CLIENT */
    rappel_log_client.on('change', function(event) {
        event.preventDefault();
        getSites(rappel_log_client, rappel_log_site, rappel_log_dossier, rappel_log_exercice, function() {
            reloadGridRappelLog();
        });
    });

    rappel_log_client.trigger('change');

    /** SELECTION SITE */
    rappel_log_site.on('change', function() {
        getDossiers(rappel_log_client.val(), rappel_log_site, rappel_log_dossier, rappel_log_exercice, rappel_log_dossier.attr('id'), function() {
            rappel_log_dossier.trigger('change');
        });
    });

    /** SELECTION DOSSIER */
    rappel_log_dossier.on('change', function() {
        reloadGridRappelLog();
    });

    $('#tab-log-rappel').on('click', function() {
        updateSizeGridRappelLog();
    });

    tableau_rappel_log.on('click', 'tr', function () {
        previewEmail($(this));
    });

    $(document).on("click", ".jqgrid-tabs a", function () {
        updateSizeGridRappelLog();
    });

    function updateSizeGridRappelLog() {
        setTimeout(function() {
            window_height = window.innerHeight;
            rappel_log_container.height(window_height - 200);
            rappel_log_grid_width = rappel_log_container.width();
            rappel_log_grid_height = rappel_log_container.height() - 100;
            tableau_rappel_log.jqGrid("setGridWidth", rappel_log_grid_width);
            tableau_rappel_log.jqGrid("setGridHeight", rappel_log_grid_height);
        }, 0);
    }

    /** RECHARGE GRID RAPPEL LOG */
    function reloadGridRappelLog() {
        var client = rappel_log_client.val(),
            site = rappel_log_site.val(),
            dossier = rappel_log_dossier.val();
        var url = Routing.generate('rappel_image_log', {client: client, site: site, dossier: dossier});

        tableau_rappel_log.jqGrid('setGridParam', {
            url: url,
            datatype: 'json'
        }).trigger('reloadGrid', [{page: 1, current: true}]);
    }





});


function previewEmail(tr) {
    $('#preview-status').html('');
    $('#preview-dossier').html('');
    $('#preview-dest').text('');
    $('#preview-copie').text('');
    $('#preview-sujet').text('');
    $('#preview-contenu').html('');
    $('#preview-date').html('');

    var email = tr.attr('id');
    var url = Routing.generate('rappel_image_email_envoye', { email: email });
    fetch(url, {
        method: 'GET',
        credentials: 'include'
    }).then(function(response) {
        return response.json();
    }).then(function(data) {
        $('#preview-status').html(data.status);
        $('#preview-dossier').html(data.dossier);
        $('#preview-dest').text(data.destinataire);
        $('#preview-copie').text(data.copie);
        $('#preview-sujet').text(data.sujet);
        $('#preview-contenu').html(data.contenu);
        $('#preview-date').html(data.date_envoi);
    }).catch(function(error) {
        console.log(error);
    });

    $('#rappel-log-modal').modal('show');
}

