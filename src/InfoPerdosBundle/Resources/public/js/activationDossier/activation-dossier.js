/**
 * Created by TEFY on 16/05/2017.
 */
$(function () {
    var client = $('#client');
    var site = $('#site');
    var grid_suspendre_dossier = $('#js_suspendre_dossier');
    var window_height = window.innerHeight;
    var grid_suspendre_dossier_width = grid_suspendre_dossier.closest("div.row").width();
    var grid_suspendre_dossier_height = window_height - 250;

    //jqGrid suspendre dossiers
    grid_suspendre_dossier.jqGrid({
        datatype: 'local',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: grid_suspendre_dossier_height,
        width: grid_suspendre_dossier_width,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 2000,
        rowList: [2000, 3000, 5000],
        pager: '#pager_suspendre_dossier',
        hidegrid: false,
        rownumbers: true,
        rownumWidth: 45,
        caption: 'statuts des dossiers',
        colNames: ['Site', 'Dossier', 'SB', 'Accès client', 'Stop Saisie', 'Statut_Code', 'Statut', 'Status_Debut', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Modifier'],
        colModel: [
            {name: 'sus-site', index: 'sus-site', editable: false, sortable: true, width: 100, align: "center", classes: 'js-sus-site'},
            {name: 'sus-dossier', index: 'sus-dossier', editable: false, sortable: true, width: 200, classes: 'js-sus-dossier'},
            {name: 'sus-sb', index: 'sus-sb', editable: false, sortable: true, width: 100, align: 'center',
                title: false, classes: 'js-sus-sb'},
            {name: 'sus-user', index: 'sus-user', editable: false, sortable: true, width: 100, align: 'center',
                title: false, classes: 'js-sus-user'},
            {name: 'sus-stop', index: 'sus-stop', editable: false, sortable: true, width: 150, fixed: true, align: 'center',
                formatter: 'date', formatoptions: { srcformat: 'd/m/Y', newformat: 'd/m/Y'}, classes: 'js-sus-stop'},
            {name: 'sus-statut-code', index: 'sus-statut-code', hidden: true, classes: 'js-sus-statut-code'},
            {name: 'sus-statut', index: 'sus-statut', editable: false, sortable: true, width: 200, fixed: true, align: "center",
                cellattr: statusAttr, formatter: statusFormatter, classes: 'js-sus-statut'},
            {name: 'sus-statut-debut', index: 'sus-statut-debut', hidden: true, classes: 'js-sus-statut-debut'},
            {name: 'sus-action', index: 'sus-action', width: 80, fixed: true, align: "center", sortable: false, classes: 'js-sus-action'}
        ],
        rowattr: function(rowData, currentObj, rowId) {
            if (currentObj['sus-stop'] !== null) {
                return { 'data-stop': '1' };
            }
            return {};

        },
        onSelectRow: function (id) {

        },
        beforeSelectRow: function (rowid, e) {

        },
        loadComplete: function() {
            grid_suspendre_dossier.jqGrid ('setLabel', 'sus-dossier', 'Dossier', {'text-align':'left'});

            var filtre = '<div id="filtre-status" class="pull-left" style="margin-left: 100px;padding-top: 12px;">';
            filtre += '<div class="radio radio-inline">';
            filtre += '<input type="radio" id="filtre-status-tous" value="0" name="filtre-status-choice" checked="">';
            filtre += '<label for="filtre-status-tous">Tous</label>';
            filtre += '</div>';
            filtre += '<div class="radio radio-inline">';
            filtre += '<input type="radio" id="filtre-status-actif" value="1" name="filtre-status-choice">';
            filtre += '<label for="filtre-status-actif">Actifs</label>';
            filtre += '</div>';
            filtre += '<div class="radio radio-inline">';
            filtre += '<input type="radio" id="filtre-status-suspendu" value="2" name="filtre-status-choice">';
            filtre += '<label for="filtre-status-suspendu">Suspendus</label>';
            filtre += '</div>';
            filtre += '<div class="radio radio-inline">';
            filtre += '<input type="radio" id="filtre-status-radie" value="3" name="filtre-status-choice">';
            filtre += '<label for="filtre-status-radie">Radiés</label>';
            filtre += '</div>';
            filtre += '<div class="radio radio-inline">';
            filtre += '<input type="radio" id="filtre-status-en-creation" value="4" name="filtre-status-choice">';
            filtre += '<label for="filtre-status-en-creation">En création</label>';
            filtre += '</div>';
            filtre += '<div class="checkbox checkbox-inline" style="margin-left:15px;">';
            filtre += '<input type="checkbox" id="filtre-status-stop-saisie">';
            filtre += '<label for="filtre-status-stop-saisie">Stop Saisie</label>';
            filtre += '</div>';
            filtre += '</div>';

            if (grid_suspendre_dossier.closest('.ui-jqgrid').find('#filtre-status').length === 0) {
                grid_suspendre_dossier.closest('.ui-jqgrid').find('.ui-jqgrid-title').after(filtre);
            }
            window_height = window.innerHeight;
            filterByDossierStatus();

            grid_suspendre_dossier.find('.user-list-details').qtip({
                content: {
                    text: function() {
                        return $(this).find('.user-list-content').html();
                    }
                },
                position: { my: 'bottom center', at: 'top center' },
                style: {
                    classes: 'qtip-dark qtip-shadow',
                    tip: {
                        corner: true
                    }
                }
            });

            grid_suspendre_dossier.find('.sb-list-details').qtip({
                content: {
                    text: function() {
                        return $(this).find('.sb-list-content').html();
                    }
                },
                position: { my: 'bottom center', at: 'top center' },
                style: {
                    classes: 'qtip-dark qtip-shadow',
                    tip: {
                        corner: true
                    }
                }
            });


        }
    });

    client.on('change', function (event) {
        event.preventDefault();
        var grid_selector = [grid_suspendre_dossier];

        getSites(client, site, grid_selector);
    });

    site.on('change', function () {
        var grid_selector = [grid_suspendre_dossier];
        getDossiers(client, site, grid_selector);
    });

    client.trigger('change');

    $('.navbar-minimalize').click(function () {
        updateGridSize();
    });

    $(window).resize(function() {
        updateGridSize();
    });

    $('#status-debut').datepicker({
        format:'yyyy',
        language: 'fr',
        autoclose:true,
        clearBtn: true,
        viewMode: "years",
        minViewMode: "years",
        startView: 'decade',
        minView: 'decade',
        viewSelect: 'decade'
    });

    $('#stop-saisie-date').datepicker({
        format:'dd/mm/yyyy',
        language: 'fr',
        autoclose:true,
        clearBtn: true
    });

    //Filtrer dossiers affichés
    $(document).on('change', 'input[name="filtre-status-choice"]', function(event) {
       event.preventDefault();
       filterByDossierStatus();
    });

    $(document).on('change', '#filtre-status-stop-saisie', function(event) {
        event.preventDefault();
        filterByDossierStatus();
    });

    //Modification statut dossier
    $(document).on('click', '.js-sus-action', function(event) {
        event.preventDefault();
        var rowId = $(this).closest('tr').attr('id'),
            dossier = $(this).closest('tr').find('td.js-sus-dossier').text(),
            status_code = $(this).closest('tr').find('td.js-sus-statut-code').text(),
            status_debut = $(this).closest('tr').find('td.js-sus-statut-debut').text(),
            stop_saisie_date = $(this).closest('tr').find('td.js-sus-stop').text();

        $('#status-dossier-nom').text('Dossier : ' + dossier);
        $('#status-debut').val(status_debut);
        $('#dossier-id').val(rowId);
        $('#dossier-status-modal').find('input[name="status-value"][value="' + status_code + '"]')
            .click();
        if (stop_saisie_date.length === 10 ) {
            $('#check-stop-saisie').prop('checked', true);
            $('#stop-saisie-date').removeAttr('disabled')
                .val(stop_saisie_date);
        } else {
            $('#check-stop-saisie').prop('checked', false);
            $('#stop-saisie-date').attr('disabled', '')
                .val('');
        }

        $('#dossier-status-modal').modal('show');
    });

    //Activer stop sasie datepicker
    $(document).on('change', '#check-stop-saisie', function() {
       if ($(this).prop('checked') === true) {
           $('#stop-saisie-date').removeAttr('disabled')
               .focus();
       } else {
           $('#stop-saisie-date').val('')
               .attr('disabled', '')
       }
    });

    //Afficher/Cacher debut-status
    $(document).on('change', 'input[name="status-value"]', function(event) {
        event.preventDefault();
        var id = $(this).attr('id');
        if (id === 'status-actif') {
            $('#status-debut-container').addClass('hidden');
        } else {
            $('#status-debut-container').removeClass('hidden');
        }
    });

    //Enregistrer status dossier
    $(document).on('click', '#btn-save-status', function(event) {
        event.preventDefault();
        var status = $(document).find('input:radio[name="status-value"]:checked').val(),
            status_debut = $('#status-debut').val().trim(),
            stop_saisie = $('#check-stop-saisie').prop('checked') ? 1 : 0,
            stop_saisie_date = '',
            dossier_id = $('#dossier-id').val();

        if($('#stop-saisie-date').length > 0) {
            stop_saisie_date = $('#stop-saisie-date').val().trim()
        }

        if (status !== '1' && status_debut === '') {
            show_info('', "Séléctionner l'année de début.", 'warning');
        } else if (stop_saisie === 1 && stop_saisie_date === '') {
            show_info('', "Renseigner la date de stop de la saisie", 'warning');
            setTimeout(function() {
                $('#stop-saisie-date').focus();
            }, 1000);
        } else {
            console.log(status, status_debut);
            $.ajax({
                url: Routing.generate('info_perdos_activation_dossier_edit', {dossier: dossier_id}),
                type: 'POST',
                data: {
                    status: status,
                    status_debut: status_debut,
                    stop_saisie_date: stop_saisie_date
                },
                success: function() {
                    $('#dossier-status-modal').modal('hide');
                    getDossiers(client, site, grid_suspendre_dossier);
                }
            })
        }
    });
});
