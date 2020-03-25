var window_height = window.innerHeight,
    rappel_image_client = $('#client-config'),
    rappel_image_site = $('#site-config'),
    tableau_rappel_image = $('#table_config_3'),
    email_content = $('#email-content'),
    activer_envoi = $('#activer-envoi'),
    lastsel_rappel_image,
    now = new Date(),
    rappel_image_grid_height = $(window).height() - 80;



var rappel_periode = {
    'M': 'Mensuel',
    'T': 'Trimestriel',
    'Q': 'Quadrimestriel',
    'S': 'Semestriel',
    'A': 'Annuel'
};

var rappel_frequence_select = '<select id="rappel-frequence-all" style="width:90px;height:22px;line-height:22px;" onclick="checkHeaderClick(event)">' +
    '<option value=""></option>' +
    '<option value="M">Mensuel</option>' +
    '<option value="T">Trimestriel</option>' +
    '<option value="Q">Quadrimestriel</option>' +
    '<option value="S">Semestriel</option>' +
    '<option value="A">Annuel</option>' +
    '</select>';
$(function() {
    var changeCount = 0;
    HTMLTextAreaElement.prototype.insertAtCaret = function (text) {
        text = text || '';
        if (document.selection) {
            // IE
            this.focus();
            var sel = document.selection.createRange();
            sel.text = text;
        } else if (this.selectionStart || this.selectionStart === 0) {
            // Others
            var startPos = this.selectionStart;
            var endPos = this.selectionEnd;
            this.value = this.value.substring(0, startPos) +
                text +
                this.value.substring(endPos, this.value.length);
            this.selectionStart = startPos + text.length;
            this.selectionEnd = startPos + text.length;
        } else {
            this.value += text;
        }
    };

    $('.i-checks').iCheck();

    /** IGNORER FORM SUBMIT */
    $('form').on('submit', function(event) {
        event.preventDefault();
        return false;
    });

    /** MODIFICATION STATUS ENVOI */
    activer_envoi.on('change', function() {
        var envoi_status = '0';
       if (activer_envoi.prop('checked')) {
           // $('#activer-envoi-label').text("Envoi Actif");
           envoi_status = '1';
       } else {
           // $('#activer-envoi-label').text("Envoi Inactif");
       }

        var client = rappel_image_client.val();
        var url = Routing.generate('rappel_image_status_envoi_edit', { client: client });
        var formData = new FormData();
        formData.append('envoi_status', envoi_status);
        fetch(url, {
            method: 'POST',
            credentials: 'include',
            body: formData
        }).then(function(response) {
            return response.json();
        }).then(function(data) {

        }).catch(function(error) {

        });
    });

    setTimeout(function() {
        updateGridSizeRappelImage();
    }, 500);

    /** SAVE MODIF PARAM */
    $(document).on('click', '.js-save-rappel-image', function(event) {
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        saveRowEdited(rowid);
    });

    tableau_rappel_image.on('change', 'input, select', function(event) {
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        saveRowEdited(rowid);
    });

    /** MODIF DESTINATAIRES */
    $(document).on('click', '.js-rappel-img-dest', function(event) {
        event.preventDefault();
        var rowId = $(this).closest('tr').attr('id'),
            nom_dossier = $(this).closest('tr')
                .find('.js-rappel-img-dossier')
                .text(),
            nom = $(this).closest('tr')
                .find('.js-rappel-img-nom')
                .text(),
            titre = $(this).closest('tr')
                .find('.js-rappel-img-titre')
                .text(),
            destinataire = $(this).closest('tr')
                .find('.js-rappel-img-dest')
                .text(),
            copie = $(this).closest('tr')
                .find('.js-rappel-img-copie')
                .text();
        $('#notification-dest-id').val(rowId);
        $('#titre-contact').val(titre);
        $('#nom-contact').val(nom);
        $('#list-destinataire').val(destinataire);
        $('#list-copie').val(copie);
        $('#rappel-img-dest-modal-title').text(nom_dossier + " - Destinataires");
        $('#rappel-img-dest-modal').modal('show');
    });

    $('#btn-save-rappel-img-destinataire').on('click', function(event) {
        event.preventDefault();
        var btn = $(this),
            btn_normal = 'Enregistrer',
            btn_save_running = '<i class="fa fa-spinner fa-pulse fa-fw"></i> Enregistrer',
            titre = $('#titre-contact').val(),
            nom = $('#nom-contact').val().trim(),
            destinataire = $('#list-destinataire').val().trim(),
            copie = $('#list-copie').val().trim(),
            notification = $('#notification-dest-id').val();

        if (nom !== '' && destinataire !== '') {
            var url = Routing.generate('rappel_image_destinataire_edit', {notification: notification}),
                formData = new FormData();
            formData.append('titre', titre);
            formData.append('nom', nom);
            formData.append('destinataire', destinataire);
            formData.append('copie', copie);
            btn.html(btn_save_running);
            fetch(url, {
                method: 'POST',
                credentials: 'include',
                body: formData
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                console.log(data);
                $('#rappel-img-dest-modal').modal('hide');
                btn.html(btn_normal);
                reloadGridRappelImage();
            }).catch(function (error) {
                show_info('Erreur', 'Une erreur est survenue.', 'error');
                btn.html(btn_normal);
                console.log(error);
            });
        } else {
            show_info('Erreur', 'Le nom du contact et le mail de(s) destinataire(s) ne doivent pas être vides.', 'error');
        }
    });

    /** MODIF CONTENU */
    $(document).on('click', '.js-rappel-img-contenu', function(event) {
        event.preventDefault();
        var rowId = $(this).closest('tr').attr('id'),
            nom_dossier = $(this).closest('tr')
                .find('.js-rappel-img-dossier')
                .text(),
            contenu = $(this).closest('tr')
                .find('.js-rappel-img-contenu-full')
                .text(),
            objet  = $(this).closest('tr')
                .find('.js-rappel-img-objet')
                .text();

        $('#rappel-img-contenu-modal-title').text(nom_dossier + " - Contenu du mail");
        $('#notification-contenu-id').val(rowId);
        $('#check-content-all').prop('checked', false);
        $('#email-content').val(contenu);
        $('#rappel-img-contenu-modal').modal('show');
        $('#notification-objet').val(objet);

    });

    $('#rappel-img-contenu-modal').on('shown.bs.modal', function() {

    });

    /** SAVE MAIL CONTENU */
    $('#btn-save-rappel-img-contenu').on('click', function(event) {
        event.preventDefault();
        var btn_save = $(this),
            btn_normal = 'Enregistrer',
            btn_save_running = '<i class="fa fa-spinner fa-pulse fa-fw"></i> Enregistrer',
            contenu = email_content.val(),
            client = $('#client-rappel-image').val(),
            site = $('#site-rappel-image').val(),
            notification = $('#notification-contenu-id').val(),
            objet = $('#notification-objet').val(),
            tous = $('#check-content-all').prop('checked') ? 1 : 0;
        var url = Routing.generate('rappel_image_email_content_edit', { tous: tous });
        var formData = new FormData();
        formData.append('contenu', contenu);
        formData.append('client', client);
        formData.append('site', site);
        formData.append('notification', notification);
        formData.append('tous', tous);
        formData.append('objet', objet);

        btn_save.html(btn_save_running);
        fetch(url, {
            method: 'POST',
            credentials: 'include',
            body: formData
        }).then(function(response) {
            return response.json();
        }).then(function(data) {
            console.log(data);
            btn_save.html(btn_normal);
            $('#rappel-img-contenu-modal').modal('hide');
            reloadGridRappelImage();
            show_info('', 'Contenu enregistré', 'success', 200);
        }).catch(function(error) {
            console.log(error);
            btn_save.html(btn_normal);
            show_info('', 'Une erreur est survenue. Merci de réessayer.', 'error');
        });
    });

    /** DEFAULT CONTENT */
    $('#default-content').on('click', function() {
       var btn = $(this),
           btn_normal = '<i class="fa fa-file-text-o"></i> Utiliser le contenu par défaut',
           btn_load_running = '<i class="fa fa-spinner fa-pulse fa-fw"></i> Utiliser le contenu par défaut',
           editor = $('#email-content'),
           url = Routing.generate('rappel_image_default_content');
       btn.html(btn_load_running);
       fetch(url, {
           method: 'GET',
           credentials: 'include'
       }).then(function(response) {
           return response.text();
       }).then(function(data) {
           editor.val(data);
           btn.html(btn_normal);
       }).catch(function(error) {
           btn.html(btn_normal);
           console.log(error);
        });
    });

    /** INSERT BALISE IMAGE MANQUANTE DANS MAIL */
    $('#image-manquante').on('click', function() {
        var editor = document.getElementById('email-content');
        editor.insertAtCaret("[[image]]");
    });

    /** INSERT BALISE IMAGE MANQUANTE DANS MAIL */
    $('#frequence-envoi').on('click', function() {
        var editor = document.getElementById('email-content');
        editor.insertAtCaret("[[frequence]]");
    });

    /** UPDATE ENVOI N-1 ALL */
    $(document).on('change', '#envoi-n-1-all', function() {
        var field = 'EnvoiN1',
            value = $(this).prop('checked') ? 1 : 0;
        editParamAll(field, value);
    });

    /** UPDATE FREQUENCE ALL */
    $(document).on('change', '#rappel-frequence-all', function() {
        var field = 'Periode',
            value = $(this).val() !== '' ? $(this).val() : 'M';
        editParamAll(field, value);
    });

    /** UPDATE ENVOI N ALL */
    $(document).on('change', '#envoi-n-all', function() {
        var field = 'EnvoiN',
            value = $(this).prop('checked') ? 1 : 0;
        editParamAll(field, value);
    });

    $(document).on("click", ".jqgrid-tabs a", function () {
        updateGridSizeRappelImage();
    });

    $('#tab-param-rappel').on('click', function() {
        updateGridSizeRappelImage();
    });

    $(window).bind('resize',function () {
        updateGridSizeRappelImage();
    });
});

function set_table_rappel_img(tableau_rappel_image, rappel_image_grid_height, lastsel_rappel_image) {
    tableau_rappel_image.jqGrid({
        datatype: 'local',
        loadonce: true,
        sortable: false,
        height: rappel_image_grid_height,
        autowidth: true,
        shrinkToFit: true,
        viewrecords: true,
        hidegrid: true,
        rownumbers: true,
        rownumWidth: 35,
        rowNum: 2000,
        rowList: [2000, 3000, 5000],
        colNames: [
            'Dossiers',
            'Status',
            'Stop Saisie',
            'Envoi N-1<br><input type="checkbox" id="envoi-n-1-all" onclick="checkHeaderClick(event)">',
            'Envoi N<br><input type="checkbox" id="envoi-n-all" onclick="checkHeaderClick(event)">',
            'Destinataires', 'Copie', 'Titre', 'Nom_Contact',
            'Fréquence',
            'Début Envoi<br><input id="debut-envoi-all" style="width:90px;" onclick="checkHeaderClick(event)">',
            'Contenu', 'Contenu_Complet', 'Objet',
            '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'
        ],
        colModel: [
            {
                name: 'rappel-img-dossier', index: 'rappel-img-dossier', editable: false, sortable: true, width: 180, classes: 'js-rappel-img-dossier'
            },
            {
                name: 'rappel-img-dossier-status', index: 'rappel-img-dossier-status', editable: false, sortable: true, width: 140, classes: 'js-rappel-img-dossier-status', align: 'center'
            },
            {
                name: 'rappel-img-dossier-stop', index: 'rappel-img-dossier-stop', editable: false, sortable: true, width: 100, classes: 'js-rappel-img-dossier-stop', align: 'center'
            },
            {
                name: 'rappel-img-n-1', index: 'rappel-img-n-1', editable: true, sortable: true, width: 70, fixed: true, align: 'center',
                formatter: 'checkbox', edittype: 'checkbox', editoptions: {value:"1:0"}, classes: 'js-rappel-img-n-1'
            },
            {
                name: 'rappel-img-n', index: 'rappel-img-n', editable: true, sortable: true, width: 70, fixed: true, align: 'center',
                formatter: 'checkbox', edittype: 'checkbox', editoptions: {value:"1:0"}, classes: 'js-rappel-img-n'
            },
            {
                name: 'rappel-img-dest', index: 'rappel-img-dest', editable: false, sortable: true, width: 120, classes: 'js-rappel-img-dest'
            },
            {
                name: 'rappel-img-copie', index: 'rappel-img-copie', hidden: true, classes: 'js-rappel-img-copie'
            },
            {
                name: 'rappel-img-titre', index: 'rappel-img-titre', hidden: true, classes: 'js-rappel-img-titre'
            },
            {
                name: 'rappel-img-nom', index: 'rappel-img-nom', hidden: true, classes: 'js-rappel-img-nom'
            },
            {
                name: 'rappel-img-freq', index: 'rappel-img-freq', editable: false, sortable: true, width: 100, fixed: true, align: 'center',
                edittype:'select', editoptions: {value:rappel_periode}, classes: 'js-rappel-img-freq'
            },
            {
                name: 'rappel-img-debut', index: 'rappel-img-debut', editable: true, sortable: true, width: 100, fixed: true, align: 'center',
                sorttype: 'date', formatter: 'date', formatoptions: {newformat: "d/m/Y"}, datefmt: 'Y-m-d',
                editoptions : {
                    dataInit: function (el) {
                        $(el).css('text-align', 'center');
                        $(el).datepicker({format:'dd/mm/yyyy', language: 'fr', autoclose:true, todayHighlight: true, clearBtn: true})
                            .on('changeDate', function() {
                                var rowid = $(el).closest('tr').attr('id');
                                setTimeout(function() {
                                    console.log(rowid, $(el).val());
                                }, 0);
                            })
                            .on('clearDate', function() {
                                var rowid = $(el).closest('tr').attr('id');
                                setTimeout(function() {
                                    console.log(rowid, $(el).val());
                                }, 0);
                            });
                    }
                },
                classes: 'js-rappel-img-debut'
            },
            {
                name: 'rappel-img-contenu', index: 'rappel-img-contenu', editable: false, sortable: true, width: 120, classes: 'js-rappel-img-contenu'
            },
            {
                name: 'rappel-img-contenu-full', index: 'rappel-img-contenu-full', editable: false, hidden: true, classes: 'js-rappel-img-contenu-full'
            },
            {
                name: 'rappel-img-objet', index: 'rappel-img-objet', editable: false, hidden: true, classes: 'js-rappel-img-objet'
            },
            {
                name: 'action', index: 'action', width: 60, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-rappel-image" title="Enregistrer"></i>'},
                classes: 'js-rappel-img-action'
            }
        ],
        ajaxRowOptions: { async: true },
        onSelectRow: function (id) {
            if (id && id !== lastsel_rappel_image) {
                tableau_rappel_image.restoreRow(lastsel_rappel_image);
                lastsel_rappel_image = id;
            }
            tableau_rappel_image.editRow(id, true);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);
            var periode_col = target.closest('td').hasClass('js-rappel-img-dest');
            var contenu_col = target.closest('td').hasClass('js-rappel-img-contenu');

            return (!item_action && !periode_col && !contenu_col);
        },
        loadComplete: function() {
            $(document).find('#debut-envoi-all')
                .datepicker({
                    format:'dd/mm/yyyy',
                    language: 'fr',
                    autoclose:true,
                    todayHighlight: true,
                    clearBtn: true
                })
                .on('changeDate', function() {
                    var field = 'DebutEnvoi',
                        value = $(this).val();
                    editParamAll(field, value);
                })
                .on('clearDate', function() {
                    var field = 'DebutEnvoi',
                        value = $(this).val();
                    editParamAll(field, value);
                });
            setCheckAllEnvoi();
        }
    });
}

/** RECHARGE GRID RAPPEL IMAGE */
function reloadGridRappelImage() {
    var client = $('#client-config').val(),
        site = $('#site-config').val(),
        tableau_rappel_image = $('#table_config_3');
    tableau_rappel_image.jqGrid('setGridParam', {
        url: Routing.generate('rappel_image_param_liste', {client: client, site: site}),
        datatype: 'json'
    }).trigger('reloadGrid', [{page: 1, current: true}]);
}

function setCheckAllEnvoi() {
    setCheckAll($(document).find('#envoi-n-1-all'), '.js-rappel-img-n-1');
    setCheckAll($(document).find('#envoi-n-all'), '.js-rappel-img-n');
}

function updateGridSizeRappelImage() {
    setTimeout(function() {
        window_height = window.innerHeight;
        rappel_image_grid_height = $(window).height() - 80;
        tableau_rappel_image.jqGrid("setGridWidth", $("#rappel-image").width() - 50);
        tableau_rappel_image.jqGrid("setGridHeight", rappel_image_grid_height);
    }, 0);
}

function saveRowEdited(rowid)
{
    tableau_rappel_image.jqGrid('setGridParam', {
        editurl: Routing.generate('rappel_image_param_update', {notification:rowid})
    }).jqGrid('saveRow', rowid, {
        "aftersavefunc": function() {
            // reloadGridRappelImage();
        }
    });
}

function editParamAll(field, value)
{
    swal({
        title: 'Attention',
        text: "Voulez-vous modifier le paramètre pour tous les dossiers ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1ab394',
        cancelButtonColor: '#f8ac59',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                var client = rappel_image_client.val(),
                    site = rappel_image_site.val(),
                    url = Routing.generate('dossier_config_rappel_image_param_edit_all'),
                    formData = new FormData();
                formData.append('client', client);
                formData.append('site', site);
                formData.append('field', field);
                formData.append('value', value);

                fetch(url, {
                    method: 'POST',
                    credentials: 'include',
                    body: formData
                }).then(function (response) {
                    return response.json();
                }).then(function (data) {
                    resolve();
                    reloadGridRappelImage();
                }).catch(function (error) {
                    show_info('', 'Une erreur est survenue. Merci de réessayer.', 'error');
                });
            })
        }
    });
}