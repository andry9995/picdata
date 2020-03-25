$(function () {
    var window_height = window.innerHeight;
    $('#param-ecriture').find('.panel-body').height(window_height);
    $('#prestation-list').height(window_height - 240);
    $('#parametre-list').height(window_height);
    var client = $('#client'),
        loader = $('#loader'),
        table_critere = $('#table-critere'),
        selected_prestation = null;

    loader.hide();
    table_critere.hide();

    client.on('change', function () {
        $('#prestation-list').find('.list-group').empty();
        loader.show();
        var url = Routing.generate('fact_prestation_client', {client: client.val(), jqgrid: 0});
        fetch(url, {
            method: 'GET',
            credentials: 'include'
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            var liste = '';
            data.forEach(function (item) {
                liste += '<li data-id="' + item.id + '" class="list-group-item prestation-item">' +
                    '<span class="label label-default">' + item.code + '</span> ' + item.prestation +
                    '</li>';
            });
            $('#prestation-list').find('.list-group').html(liste);
            loader.hide();
        }).catch(function (error) {
            console.log(error);
            loader.hide();
        })
    });

    client.trigger('change');

    // OLD
    // $(document).on('click', '.prestation-item', function () {
    //     $(this).closest('.list-group')
    //         .find('.prestation-item')
    //         .removeClass('active');
    //     $(this).addClass('active');
    //     selected_prestation = $(this).attr('data-id');

    //     loadCritere(selected_prestation);
    // });



    //Ajouter une critère
    $('#btn-add-param').on('click', function () {
        var critere_select = $('<div>').append($('#liste-critere').clone().removeAttr('id').removeClass('hidden')).html();
        var row = '<tr><td><input value="TITRE" class="param-input critere-nom"></td>';
        row += '<td>' + critere_select + '</td>';
        row += '<td><input placeholder="valeur" class="param-input critere-value"></td>';
        row += '<td><input placeholder="exclure" class="param-input critere-exclure"></td>';
        row += '<td><i class="fa fa-trash pointer remove-critere"></i></td></tr>';
        $('#table-critere').find('tbody').append(row);
    });

    //Supprimer une critère
    $(document).on('click', '.remove-critere', function() {
       $(this).closest('tr').remove();
    });

    //Enregistrer critères
    // $('#btn-save-param').on('click', function() {
    //     loader.show();
    //     var criteres = [];
    //     $('#table-critere').find('tbody').find('tr').each(function(index, item) {
    //        criteres.push({
    //            'critere_id': $(item).attr('data-id') ? $(item).attr('data-id') : 0,
    //            'critere_nom': $(item).find('.critere-nom').val().toUpperCase(),
    //            'critere_code' : $(item).find('.critere-select').val(),
    //            'critere_value': $(item).find('.critere-value').val(),
    //            'critere_exclure': $(item).find('.critere-exclure').val()
    //        });
    //     });

    //     var url = Routing.generate('fact_param_import_edit', { prestation: selected_prestation }),
    //         formData = new FormData();
    //     formData.append('criteres', JSON.stringify(criteres));
    //     fetch(url, {
    //         method: 'POST',
    //         credentials: 'include',
    //         body: formData
    //     }).then(function(response) {
    //         return response.json();
    //     }).then(function(data) {
    //         data = JSON.parse(data);
    //         showCritereList(data);
    //         loader.hide();
    //     }).catch(function(error) {
    //         console.log(error);
    //         loader.hide();
    //     })
    // });

    function loadCritere(selected_prestation) {
        loader.show();
        table_critere.hide();
        $('#table-critere').find('tbody').empty();

        var url = Routing.generate('fact_param_import_list', {prestation: selected_prestation});
        fetch(url, {
            method: 'GET',
            credentials: 'include'
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            data = JSON.parse(data);
            showCritereList(data);
            loader.hide();
            table_critere.show();
        }).catch(function (error) {
            console.log(error);
            loader.hide();
            table_critere.hide();
        });
    }

    function showCritereList(data) {
        $('#table-critere').find('tbody').empty();
        if (data && data instanceof Array) {
            data.forEach(function (item) {
                var critere_code = item.factCritere.code,
                    critere_id = item.id,
                    critere_titre = item.nom,
                    critere_value = item.value.join(';'),
                    critere_exclure = item.exclure.join(';');
                var critere_select = $('<div>').append(
                    $('#liste-critere').clone().removeAttr('id').removeClass('hidden')
                ).html();
                var row = '<tr data-id="' + critere_id + '"><td><input value="' + critere_titre + '" class="param-input critere-nom"></td>';
                row += '<td>' + critere_select + '</td>';
                row += '<td><input value="' + critere_value + '" class="param-input critere-value"></td>';
                row += '<td><input value="' + critere_exclure + '" class="param-input critere-exclure"></td>';
                row += '<td><i class="fa fa-trash pointer remove-critere"></i></td></tr>';
                var new_row = $(row);
                $('#table-critere').find('tbody').append(new_row);
                new_row.find('.critere-select').val(critere_code);
            });
        }
    }

    // by ANDRY
    $(document).on('click', '.prestation-item', function () {
        $(this).closest('.list-group')
            .find('.prestation-item')
            .removeClass('active');
        $(this).addClass('active');
        selected_prestation = $(this).data('id');
        loadParam(selected_prestation);

    });

    function loadParam(selected_prestation) {
        var url = Routing.generate('billing_final_load_param');

        var data = {
            prestation_id : selected_prestation,
            client_id: client.val()
        };

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            datatype: 'json',
            success: function(data) {
                $('#param-container').html(data);
                active_plugs();
                get_dossiers();
            }
        })
    }

    function active_plugs() {
        $(".js-example-basic-multiple").select2();

        $('#param-mot-clef').tagsinput({
          tagClass: 'big'
        });
    }

    function save_param() {
        
        var url = Routing.generate('billing_final_save_param');

        var data = {
            journals     : $('#journal-param').val(),
            sources      : $('#source-param').val(),
            mot_clef     : $('#param-mot-clef').val(),
            prestation_id: $('#prestation_id').val(),
            client_id    : $('#client_id').val(),
            param_id     : $('#param_id').val(),
            unite        : $('#unite-param').val()
        };

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            datatype: 'json',
            success: function(res) {
                $('#param_id').val(res);
            }
        })
    }

    $(document).on('click','#btn-save-param',function(event) {
        event.preventDefault();
        save_param();
    });



    var exercice_selector = $('#exercice-param');
    var client_selector = $('#client');
    var dossier_selector = $('#dossier-param');

   // setTimeout(function() {
   //      $('#client').trigger('change');
   //  }, 500);

    // $(document).on('change','#client',function() {
    //     get_dossiers();
    //     $('#nb-mois-traite').text("");
    // });

    function get_dossiers() {
            var url = Routing.generate('app_dossiers', {
                client: client_selector.val(), 
                site: 'UmFTMUZqbjRsVlVoNzBVbUZUTVVacWJqUnNWbFZvTnc9PQ==', 
                conteneur: 1, 
                tdi: 0,
                tous: 2
            });

            $.ajax({
                url : url,
                type : 'GET',
                data : {
                    exercice : exercice_selector.val(),
                    tous: 2
                },
                success : function(data) {

                    console.log('test');

                    data = $.parseJSON(data);
                    var tous = '<option value="0">Tous</option>';
                    var single = false;

                    $('#dossier-param').closest('.form-group')
                        .find('.label.label-warning')
                        .text(data.length.toString());

                    if (data.length <= 1) {
                        single = true;
                    } else {
                        $('#dossier-param').html(tous);
                    }

                    var options = '';

                    if (data instanceof Array) {
                        $.each(data, function (index, item) {
                            if (single) {
                                options += '<option value="' + item.idCrypter + '" selected>' + item.nom + '</option>';
                            } else {
                                options += '<option value="' + item.idCrypter + '">' + item.nom + '</option>';
                            }
                        });
                        $('#dossier-param').append(options);

                        console.log($('#dossier-param'));
                    } else {
                        return 0;
                    }
                        
                }

            });
        }

        $(document).on('click','.btn-go-simulation',function(event) {

            console.log('ok');

            event.preventDefault();

            var url = Routing.generate('billing_final_nb_prestation');

            var data = {
                param_id : $('#param_id').val(),
                dossier : $('#dossier-param').val(),
                exercice : $('#exercice-param').val()
            };

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                datatype : 'json',
                success: function(response) {
                    $('#nb-mois-traite').html(response)
                }
            });

        })



});