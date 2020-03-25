/**
 * Created by INFO on 01/06/2017.
 */



$(document).ready(function () {

    var siteGrid = $('#js_site_liste');
    var lastsel_site;

    var clientId = $("#js_zero_boost").val();

    var url = Routing.generate('info_perdos_site',{clientId: clientId});
    var editUrl = Routing.generate('info_perdos_site_edit', {clientId: clientId});
    siteGrid.jqGrid({

        datatype: 'json',
        url: url,
        loadonce: false,
        sortable: true,
        autowidth: true,
        // height: gridHeight,
        width: 560,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [20, 50, 100],
        pager: '#js_site_pager',
        hidegrid: false,
        editurl: editUrl,
        caption: 'Sites',
        colNames: ['Nom',  '<span class="fa fa-bookmark-o " style="display:inline-block"/> Action'],
        colModel: [
            {
                name: 'site-nom',
                index: 'site-nom',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-site-nom'
            },
            {
                name: 'action', index: 'action', width: 60, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-site" title="Enregistrer"></i>'},
                classes: 'js-banque-action'
            }
        ],

        onSelectRow: function (id) {
            if (id && id !== lastsel_site) {
                siteGrid.restoreRow(lastsel_site);
                lastsel_site = id;
            }
            siteGrid.editRow(id, false);
        },

        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },

        loadComplete: function () {

            if ($("#btn-add-site").length == 0) {
                $('#js_site_liste').closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-site" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }

            siteGrid.jqGrid('setGridWidth', 560);

        },


        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}

    });

    setScrollerHeigt();



    setFirstLoad(clientId);

    $('#date_signature').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: 'fr',
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true
    });

    // $('#date_signature').datepicker({
    //     todayBtn: "linked",
    //     // keyboardNavigation: false,
    //     // forceParse: false,
    //     // calendarWeeks: true,
    //     // autoclose: true,
    //     dateFormat: 'yy-dd-mm',
    // });

    $(document).on('click','.btn-delete-contrat-edit',function(event) {
        event.preventDefault();
        $.confirm({
            icon: 'fa fa-question-circle',
            title: 'Confirmer!',
            content: 'Voulez-vous supprimer le fichier ?',
            type : 'red',
            buttons: {
                confirm:{
                    text: 'OUI',
                    action: function() {
                        $('#file-path').val('');
                        $('#show-container-edit').addClass('hidden');
                        $('#upload-container').removeClass('hidden');
                        $.alert('Fichier suppimer!');
                    }
                },
                cancel: {
                    text: 'ANNULER',
                    action: function() {
                        $.alert('Annuler!');
                    }
                }
            }
        });
    })

    $(document).on('click','.btn-delete-contrat-create',function(event) {
        event.preventDefault();
        $.confirm({
            icon: 'fa fa-question-circle',
            title: 'Confirmer!',
            content: 'Voulez-vous supprimer le fichier ?',
            type : 'red',
            buttons: {
                confirm:{
                    text: 'OUI',
                    action: function(argument) {
                        $('#file-path').val('');
                        $('#show-container-create').addClass('hidden');
                        $('#upload-container-create').removeClass('hidden');
                        $.alert('Fichier suppimer!');
                    }
                },
                cancel:{
                    text: 'ANNULER',
                    action: function() {
                        $.alert('Annuler!');
                    }
                }
            }
        });
    })

    $(document).on('click','.btn-delete-contrat',function(event) {
        event.preventDefault();
        $.confirm({
            title: 'Confirmer!',
            content: 'Voulez-vous supprimer le fichier ?',
            icon: 'fa fa-question-circle',
            type : 'blue',
            buttons: {
                confirm: {
                    text: '<i class="fa fa-check"></i> Oui',
                    btnClass: 'btn-primary',
                    action: function() {
                        $('#file-path').val('');
                        $('#show-container').addClass('hidden');
                        $('#upload-container').removeClass('hidden');
                        $.alert('Fichier suppimer!');
                    }
                },
                cancel: {
                    text: '<i class="fa fa-times"></i> Non',
                    btnClass: 'btn-red',
                    action: function() {
                        $.alert('Annuler!');
                    }
                }
            }
        });
    })

    $(document).on('click','.btn-view-contrat-create',function(event) {
       event.preventDefault();

       filename = $('#file-path').val();

       var data = asset + filename + '.pdf?#scrollbar=1&toolbar=0&navpanes=1';

       var pdf = $('#content-pdf');

       var pdf_object = '<object id="js_embed contrat_embed"width="100%"height="100%"type="application/pdf"title=""data="'+ data +'"><p>Votre  navigateur ne peut pas affichier le fichier PDF. Vous pouvez le télécharger en cliquant <a target="_blank" href="" style="text-decoration: underline;">ICI</a></p></object>';

       pdf.html(pdf_object);

       $('#modal_view_contrat_create').modal('show');
    })




    $(document).on('click','.btn-view-contrat',function(event) {
       event.preventDefault();
       $('#modal_view_contrat').modal('show');
    })


    $(document).on('click','.btn-upload-contrat',function(event) {
       event.preventDefault();
       $('#modal_upload_contrat').modal('show');
       initFileInputContrat('contrat_pj','edit');
    })

    function after_upload_edit(filename) {
        $('#modal_upload_contrat').modal('hide');

        $('#file-path').val(filename);

        $('#contrat_pj').fileinput('reset');
        $('#contrat_pj').fileinput('destroy');
        $('#contrat_pj').val('');

        // 
        $('#upload-container').addClass('hidden');

        $('#show-container-edit').removeClass('hidden');
    }

    // Creation
    $(document).on('click','.btn-upload-contrat-create',function(event) {
       event.preventDefault();
       $('#modal_upload_contrat').modal('show');

       initFileInputContrat('contrat_pj','create');
    });

    function after_upload_create(filename) {
        $('#modal_upload_contrat').modal('hide');

        $('#file-path').val(filename);

        $('#contrat_pj').fileinput('reset');
        $('#contrat_pj').fileinput('destroy');
        $('#contrat_pj').val('');

        // 
        $('#upload-container-create').addClass('hidden');

        $('#show-container-create').removeClass('hidden');

        
    }

    function initFileInputContrat(selecteur, type) {



        var filename = (new Date().getTime()).toString(16);

        $('#'+selecteur).fileinput({
            language: 'fr',
            theme: 'fa',
            uploadAsync: false,
            showPreview: true,
            showUpload: true,
            showRemove: true,
            // showCaption: false, 
            // dropZoneEnabled: false,
            fileTypeSettings: {
                pdf: function(vType, vName) {
                    return typeof vType !== "undefined" && vType.match('pdf');
                }
            },
            allowedFileTypes: ['pdf'],
            uploadUrl: Routing.generate('info_perdos_upload_contrat',{filename: filename}),
        });

        $('#'+selecteur).on('filebatchuploadcomplete', function() {

            var fileCapt = $('#'+selecteur).closest('.input-group').find('.file-caption-name');

            if (type == 'create') {
                after_upload_create(filename);
            } else{
                after_upload_edit(filename)
            }
            // fileCapt.append('<i class="fa fa-check kv-caption-icon"></i>');
            // disableEstEnvoyeAfterEnvoi($('#'+selecteur));
            // console.log('ato');

            // console.log(fileCapt);

            // var path = filename + ".pdf";

            // $('#file-path').val(filename);

            // $('#modal_upload_contrat').modal('hide');


            // // create
            // $('#upload-container-create').addClass('hidden');

            // $('#show-container-create').removeClass('hidden');

            // var data = filename + "?#scrollbar=1&toolbar=0&navpanes=1";

            // $('#contrat_embed').attr('data',data);

            // $('#file-path-create').val(filename);

            // console.log(filename);

        });

        $('#'+selecteur).on('fileuploaderror', function(event, data, msg) {
           //  var form = data.form, files = data.files, extra = data.extra,
           //      response = data.response, reader = data.reader;
           //  console.log('File upload error');
           // // get message
           // alert(msg);
        });
    }

    

   

    $(document).on('click','.btn-edit-client',function () {
        clientId =  $(this).attr('data-id');
        showClientEdit(clientId);
    });

    $(document).on('click', '.show-edit-client', function (e) {

        $(this)
            .closest('.list-group')
            .find('.list-group-item')
            .removeClass('active');
        $(this).addClass('active');


        clientId =  $(this).attr('data-id');
        showClientEdit(clientId);

    });

    $(document).on('click', '#btn-ajouter-client', function() {
        clientId = 0;
        showClientEdit(0);

        $('.list-group').find('.list-group-item').removeClass('active');

    });

   $(document).on('keydown', '#js_siren', function(e){

        var keyCode = e.keyCode || e.which;

        if(keyCode == 13 || keyCode == 9) {
            verifierSirenClientInsee();
        }
    });

    $(document).on('click', '#btn-valider-client', function () {

        // if($('#js_mail_mandataire').val() != ''){
        //     if(!isValidEmailAddress($('#js_mail_mandataire').val())){
        //         show_info('Attention', 'Email du mandataire non valide','warning');
        //
        //         return;
        //     }
        // }
        // if($('#js_mail_secretaire').val() != ''){
        //     if(!isValidEmailAddress($('#js_mail_secretaire').val()))
        //     {
        //         show_info('Attention', 'Email du secretaire non valide','warning');
        //         return;
        //     }
        // }
        // if($('#js_mail_chef_mission').val() !=  ''){
        //     if(!isValidEmailAddress($('#js_mail_chef_mission').val())){
        //         show_info('Attention', 'Email du chef de mission non valide', 'warning');
        //         return;
        //     }
        //
        // }
        // if($('#js_mail_reception_image').val() !=  ''){
        //     if(!isValidEmailAddress($('#js_mail_reception_image').val())){
        //         show_info('Attention', 'Email du reception image non valide', 'warning');
        //         return;
        //     }
        //
        // }

        saveClient(clientId);



    });

    $(document).on('dblclick', '#js_site', function(){

        if(clientId != 0) {

            $('#site-modal').modal('show');

            url = Routing.generate('info_perdos_site', {clientId: clientId});
            editUrl = Routing.generate('info_perdos_site_edit', {clientId: clientId});

            siteGrid.jqGrid('clearGridData');

            siteGrid.jqGrid('setGridParam', {url: url});
            siteGrid.jqGrid('setGridParam', {editurl: editUrl}
            ).trigger('reloadGrid');
        }
    });

    $(document).on('click', '#btn-add-site', function (event) {

        if(canAddRow(siteGrid)) {
            event.preventDefault();
            siteGrid.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
            $("#" + "new_row", "#js_site_liste").effect("highlight", 20000);
        }
    });

    $(document).on('click', '.js-save-site', function (event) {
        var url = Routing.generate('info_perdos_site',{clientId: clientId});
        event.preventDefault();
        event.stopPropagation();
        siteGrid.jqGrid('saveRow', lastsel_site, {
            "aftersavefunc": function() {
                siteGrid.jqGrid('setGridParam', { url: url }, {page:1}).trigger('reloadGrid');

                $.ajax({
                    url: Routing.generate('info_perdos_site_list'),
                    data: {
                        clientId: clientId
                    },
                    async: true,
                    type: 'POST',
                    contentType: "application/x-www-form-urlencoded;charset=utf-8",
                    beforeSend: function (jqXHR) {
                        jqXHR.overrideMimeType('text/html;charset=utf-8');
                    },

                    success: function (data) {
                        $('#js_site').val(data.replace(/"/g , ""));
                    }
                });

            }
        });
    });


    $(document).on('change','#filtre-cl-actif',function() {
       var selected_status = $(this).val(); 
       filter_cl_by_status(selected_status,$('#cl-search').val());
    });


    function filter_cl_by_status(selected_status, search_text) {
        $('.list-group').find('.list-group-item').each(function(index, item) {

           var item_text = $(item).text().toUpperCase();

            if (selected_status == '') {
                $(item).removeClass('hidden');

                if (similar_text != '') {
                    if (similar_text(search_text,item_text) != true) {
                       $(item).addClass('hidden');
                    }
                }


            } else {

                var status = $(item).data('status');
                if (status == selected_status) {
                    $(item).removeClass('hidden');

                    if (similar_text != '') {
                        if (similar_text(search_text,item_text) != true) {
                           $(item).addClass('hidden');
                        }
                    }

                } else {
                    $(item).addClass('hidden');

                    if (similar_text != '') {
                        if (similar_text(search_text,item_text) != true) {
                           $(item).addClass('hidden');
                        }
                    }

                }
                
            }


        });
    }

    var cl_search = document.getElementById('cl-search');

    cl_search.addEventListener('keyup', makeDebounce(function(e) {

        var search_text = e.target.value;

        filter_cl_by_status($('#filtre-cl-actif').val(),search_text)

    }));

     function similar_text(search_text, item_text) {
         
         var in_item = item_text.indexOf(search_text.toUpperCase());
         if (in_item >= 0) {
             return true;
         }
         return false;
    }




});

function saveClient(clientId){

    var nom= $('#js_nom').val(),
        typeClient = $('#js_type_client').val(),
        siren= $('#js_siren').val(),
        formeJuridique = $('#js_forme_juridique').val(),
        adresse = $('#js_adresse').val(),
        tel = $('#js_tel').val(),
        siteWeb = $('#js_site_web').val(),
        nbCaractere = $('#js_nb_carractere').val(),
        signature = $('#signature').val(),
        comment = $('#comment').val(),
        contrat = $('#file-path').val();

    if(!$.isNumeric(nbCaractere)){
        show_info('Attention', 'Le champ "Nombre de caractère" n\'est pas un nombre', 'warning');
        return;
    }
    else{
        if(nbCaractere > 9){
            show_info('Attention', 'Le champ "Nombre de caractère" doit être inferieur à 9', 'warning');
            return;
        }
    }


    var  code = $('#js_code').val();

    if(code.length > 2){
        show_info('Attention', 'Le nombre de caractère dans le champ "Code" être inferieur à 2', 'warning');

        return;
    }

    var  instruction = $('#js_instruction').val();
    var  commentaire = $('#js_commentaire').val();
    var  rsSte = $('#js_raison_social').val();


    var numRue = $('#js_num_rue').val();
    var codePostal = $('#js_code_postal').val();
    var ville = $('#js_ville').val();
    var pays = $('#js_pays').val();

    var  mandataire = $('#js_type_mandataire').val();
    var  nomPrenomMandataire = $('#js_nom_mandataire').val();
    var  telMandataire = $('#js_tel_mandataire').val();
    var  mailMandataire= $('#js_mail_mandataire').val();
    var skypeMandataire = $('#js_skype_mandataire').val();

    var  nomPrenomSecretaire= $('#js_nom_sercretaire').val();
    var  telSecretaire = $('#js_tel_secretaire').val();
    var  mailSecretaire= $('#js_mail_secretaire').val();

    var nomPrenomManager = $('#js_nom_manager').val();
    var mailManager = $('#js_mail_manager').val();

    var nomPrenomChefMission = $('#js_nom_chef_mission').val();
    var mailChefMission = $('#js_mail_chef_mission').val();

    var nomPrenomReceptionImage = $('#js_nom_reception_image').val();
    var mailReceptionImage = $('#js_mail_reception_image').val();


    var societeSupport = $('#js_societe').val();
    var nomPrenomSupport = $('#js_nom_support').val();
    var telSupport = $('#js_tel_support').val();
    var mailSupport = $('#js_mail_support').val();


    var logicielId  = $('#js_logiciel').val();
    var modeTravail = $('#js_mode_travail').val();
    var ip = $('#js_adresse_ip').val();
    var implantation = $('#js_implantation').val();
    var login = $('#js_login').val();
    var password = $('#js_password').val();

    var status = $('#js_status').prop('checked') == true ? 1 : 0;

    var lien = Routing.generate('info_perdos_client_edit');

    $.ajax({
        url: lien,
        data: {
            clientId: clientId,
            nom: nom,
            typeClient: typeClient,
            siren: siren,
            formeJuridique: formeJuridique,
            adresse: adresse,
            tel: tel,
            siteWeb: siteWeb,
            nbCaractere: nbCaractere,
            code: code,
            instruction: instruction,
            commentaire: commentaire,
            rsSte: rsSte,

            numRue: numRue,
            codePostal: codePostal,
            ville: ville,
            pays:pays,

            mandataire: mandataire,
            nomPrenomMandataire: nomPrenomMandataire,
            telMandataire: telMandataire,
            mailMandataire: mailMandataire,
            skypeMandataire: skypeMandataire,

            nomPrenomSecretaire: nomPrenomSecretaire,
            telSecretaire: telSecretaire,
            mailSecretaire: mailSecretaire,


            nomPrenomManager: nomPrenomManager,
            mailManager: mailManager,

            nomPrenomChefMission: nomPrenomChefMission,
            mailChefMission: mailChefMission,
            nomPrenomReceptionImage: nomPrenomReceptionImage,
            mailReceptionImage: mailReceptionImage,

            societeSupport: societeSupport,
            nomPrenomSupport :nomPrenomSupport,
            telSupport: telSupport,
            mailSupport: mailSupport,

            logicielId: logicielId,
            modeTravail: modeTravail,
            ip: ip,
            implantation: implantation,
            login: login,
            password: password,

            signature: signature,
            comment: comment,
            contrat: contrat,
            status : status


        },
        async: true,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },

        success: function (data) {
            var res = parseInt(data);


            if(res == 2){
                show_info('SUCCES', "MODIFICATION DU 'CLIENT' BIEN ENREGISTREE");

            }
            else if(res == 1){
                show_info('SUCCES', "AJOUT DU 'CLIENT' EFFECTUEE");

                reloadClientList();

                setFirstLoad(1);
            }
            else{
                res = JSON.parse(data);
                if(res.estInsere == 0) {
                    show_info('ATTENTION',  res.message , 'warning');
                }
            }
        }
    });

}

// function showClientEdit(clientId){
//     var lien = Routing.generate('info_perdos_client_principale', {json: 1});
//     $.ajax({
//         data: {clientId: clientId},
//         url: lien,
//         type: 'POST',
//         async: true,
//         contentType: "application/x-www-form-urlencoded;charset=utf-8",
//         beforeSend: function (jqXHR) {
//             jqXHR.overrideMimeType('text/html;charset=utf-8');
//         },
//         dataType: 'html',
//         success: function (data) {
//
//             $('#js_form_client').html(data);
//
//             setFirstLoad(clientId);
//
//
//             showGrids(clientId);
//
//
//         }
//     });
// }

activate_js_witch();

function activate_js_witch() {
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function(html) {
        var switchery = new Switchery(html, {
            size: 'small',
            color: '#18a689'
        });
    });
}


function showClientEdit(clientId){
    var lien = Routing.generate('client_show_edit', {clientId:clientId});
    $.ajax({

        url: lien,
        type: 'GET',
        async: false,
        dataType: 'html',
        success: function (data) {


            $('#js_form_client').html(data);

            setFirstLoad(clientId);


            showGrids(clientId);

            activate_js_witch();


        }
    });

}





function reloadClientList(){

    var lien = Routing.generate('info_perdos_client_principale', {json: 2});
    $.ajax({
        url: lien,
        type: 'POST',
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {

            $('#js_client_list').html(data);

        }
    });

}

function isSiren(siren) {
    var estValide;
    if ( (siren.length != 9) || (isNaN(siren)) )
        estValide = false;
    else {
        // Donc le SIREN est un numérique à 9 chiffres
        var somme = 0;
        var tmp;
        for (var cpt = 0; cpt<siren.length; cpt++) {
            if ((cpt % 2) == 1) { // Les positions paires : 2ème, 4ème, 6ème et 8ème chiffre
                tmp = siren.charAt(cpt) * 2; // On le multiplie par 2
                if (tmp > 9)
                    tmp -= 9;	// Si le résultat est supérieur à 9, on lui soustrait 9
            }
            else
                tmp = siren.charAt(cpt);
            somme += parseInt(tmp);
        }

        if ((somme % 10) == 0)
            estValide = true;	// Si la somme est un multiple de 10 alors le SIREN est valide
        else
            estValide = false;
    }
    return estValide;
}

function verifierSirenClientInsee() {
    var siren = $('#js_siren').val().replace(/\s/g, "");

    //1: Verifier-na ny Siren/siret raha valide
    var estSiren = isSiren(siren);

    var returnfalse = false;

    if (!(estSiren)) {
        show_info('Information', "Le SIREN/SIRET n'est pas valide", 'warning');
        returnfalse = true;
    }

    if(returnfalse == true) {

        $('#js_siren').val('');

        $('#js_forme_juridique').val("");
        $('#js_forme_juridique').removeAttr('disabled');


        $('#js_num_rue').val("");
        $('#js_code_postal').val("");
        $('#js_ville').val("");
        $('#js_pays').val("");



        return false;
    }


    var formeJuridiqueId = -1;
    var raisonSocial = "";

    //2: Maka ny information avy any @ base insee
    $.ajax({

        url: Routing.generate('insee', {siren: siren}),
        type: 'GET',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        async: false,
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('application/json;charset=utf-8');
        },
        success: function (data) {

            if (data != -1) {
                res = data;

                raisonSocial = res.raisonSocial;
                $('#js_raison_social').val(res.raisonSocial);

                if (res.formeJuridiqueId != null) {
                    $('#js_forme_juridique').attr('disabled', true);
                    $('#js_forme_juridique').val(res.formeJuridiqueId);
                    formeJuridiqueId = res.formeJuridiqueId;
                }
                else {
                    $('#js_forme_juridique').val("");
                    $('#js_forme_juridique').removeAttr('disabled');
                }


                if(res.numRue != null){
                    $('#js_num_rue').val(res.numRue);
                }

                if(res.codePostal != null){
                    $('#js_code_postal').val(res.codePostal);
                }

                if(res.ville != null) {
                    $('#js_ville').val(res.ville);
                }

                if(res.pays != null) {
                    $('#js_pays').val(res.pays);
                }




            } else {

                //Maka any @ FIRMAPI raha tsy mahita
                verifierSirenSiretClient();
            }

        }
    });


    return true;
}

function verifierSirenSiretClient() {
    var siren = $('#js_siren').val().replace(/\s/g, "");

    //1: Verifier-na ny Siren/siret raha valide


    var lien = 'https://firmapi.com/api/v1/companies/' + siren;
    var formeJuridique = "";
    var activite = "";
    var dateDebutActivite = "";


    var formeJuridiqueId = -1;

    //2: Maka ny information avy any @ firmapi
    $.ajax({
        url: lien,
        type: 'GET',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        async: false,
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        success: function (data) {
            res = JSON.parse(data);
            formeJuridique = res.company.legal_form;

            $('#js_raison_social').val(res.company.names.denomination);

            if (formeJuridique == null) {
                formeJuridique = -1;
            }

        },
        error: function (xhr) {
            formeJuridique = -1;
            activite = -1;
            var jsonResponse = '';
            try {
                jsonResponse = JSON.parse(xhr.responseText);
            }
            catch (err) {}
            $('#js_forme_juridique').val("");
            $('#js_forme_juridique').removeAttr('disabled');


            $('#js_num_rue').val("");
            $('#js_code_postal').val("");
            $('#js_ville').val("");
            $('#js_pays').val("");


            show_info('Information', jsonResponse.message, 'warning');
        }
    });

    //3: Mametaka ny info rehetra azo avy any @ firmapi: code ape, forme juridique, date debut activite
    $.ajax({
        url: Routing.generate('info_perdos_firmapi', {
            formeJuridique: formeJuridique,
            activite: activite,
            dateDebutActivite: dateDebutActivite
        }),
        type: 'GET',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        async: false,
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        success: function (data) {
            res = JSON.parse(data);

            if (res.formeJuridiqueId != null) {
                $('#js_forme_juridique').attr('disabled', true);

                $('#js_forme_juridique').val(res.formeJuridiqueId);

                formeJuridiqueId = res.formeJuridiqueId;
            }
            else {
                $('#js_forme_juridique').val("");

                $('#js_forme_juridique').removeAttr('disabled');
            }
        }
    });

    return true;
}

function setScrollerHeigt() {

    $('#wrapper-content .scroller').css("height", $('#wrapper-content').height() );

}

function canAddRow(jqGrid) {
    var canAdd = true;
    var rows = jqGrid.find('tr');

    rows.each(function () {
        if ($(this).attr('id') == 'new_row') {
            canAdd = false;
        }
    });
    return canAdd;
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}

function setFirstLoad(clientId){

    var firstLoads = $('.first-load');
    if(clientId == 0){
        firstLoads.each(function () {
            $(this).prop('disabled', true);
        });
    }
    else{
        firstLoads.each(function(){
            $(this).removeAttr('disabled');
        });

    }

}

function showGrids(clientId){

    var lastsel_mc;
    var lastsel_ms;

    var lastsel_cmc;
    var lastsel_cms;

    var managerClientGrid = $('#js_manager_client_liste');
    var managerScriptura = $('#js_manager_scriptura_liste');

    managerClientGrid.jqGrid({
        datatype: 'json',
        url: Routing.generate('info_perdos_manager',{clientId:clientId, typecsd:0, typeresponsable: 4}),
        loadonce: true,
        sortable: true,
        autowidth: true,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [20, 50, 100],
        hidegrid: false,
        caption: " ",
        editurl: Routing.generate('info_perdos_manager_edit', {clientId:clientId, typecsd: 0, typeresponsable: 4}),
        colNames: ['Nom', 'Prenom', 'Email', 'Skype',  '<span class="fa fa-bookmark-o " style="display:inline-block"/> Action'],
        colModel: [
            {
                name: 'resp-nom',
                index: 'resp-nom',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-nom'
            },
            {
                name: 'resp-prenom',
                index: 'resp-prenom',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-prenom'
            },
            {
                name: 'resp-mail',
                index: 'resp-mail',
                editable: true,
                sortable: true,
                width: 150,
                align: "center",
                editrules: { custom: true, custom_func: verifier_mail_jqgrid },
                classes: 'js-resp-mail'
            },
            {
                name: 'resp-skype',
                index: 'resp-skype',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-skype'
            },
            {
                name: 'action', index: 'action', width: 60, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-mc-save" title="Enregistrer"></i><i class="fa fa-trash icon-action js-mc-delete" title="Supprimer"></i>'},
                classes: 'js-mc-action'
            }
        ],

        onSelectRow: function (id) {
            if (id && id !== lastsel_mc) {
                managerClientGrid.restoreRow(lastsel_mc);
                lastsel_mc = id;
            }
            managerClientGrid.editRow(id, false);
        },

        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },

        loadComplete: function () {

            if ($("#btn-add-manager-client").length == 0) {
                managerClientGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-manager-client" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }

        },


        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}

    });
    managerScriptura.jqGrid({

        datatype: 'json',
        url: Routing.generate('info_perdos_manager',{clientId:clientId, typecsd:5, typeresponsable: 4}),
        loadonce: true,
        sortable: true,
        autowidth: true,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [20, 50, 100],
        hidegrid: false,
        caption: " ",
        editurl: Routing.generate('info_perdos_manager_edit', {clientId:clientId, typecsd:5, typeresponsable: 4}) ,
        colNames: ['Nom', 'Prenom', 'Email', 'Skype',  '<span class="fa fa-bookmark-o " style="display:inline-block"/> Action'],
        colModel: [
            {
                name: 'resp-nom',
                index: 'resp-nom',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-nom'
            },
            {
                name: 'resp-prenom',
                index: 'resp-prenom',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-prenom'
            },
            {
                name: 'resp-mail',
                index: 'resp-mail',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                editrules: { custom: true, custom_func: verifier_mail_jqgrid },
                classes: 'js-resp-mail'
            },
            {
                name: 'resp-skype',
                index: 'resp-skype',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-skype'
            },
            {
                name: 'action', index: 'action', width: 60, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-ms-save" title="Enregistrer"></i><i class="fa fa-trash icon-action js-ms-delete" title="Supprimer"></i>'},
                classes: 'js-ms-action'
            }
        ],

        onSelectRow: function (id) {
            if (id && id !== lastsel_ms) {
                managerScriptura.restoreRow(lastsel_ms);
                lastsel_ms = id;
            }
            managerScriptura.editRow(id, false);
        },

        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },

        loadComplete: function () {
            if ($("#btn-add-manager-scriptura").length == 0) {
                managerScriptura.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-manager-scriptura" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }
        },


        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}

    });

    var chefMissionClient = $('#js_chef_mission_client_liste');
    var chefMissionScriptura = $('#js_chef_mission_scriptura_liste');

    chefMissionClient.jqGrid({
        datatype: 'json',
        url: Routing.generate('info_perdos_manager',{clientId:clientId, typecsd:0, typeresponsable: 1}),
        loadonce: true,
        sortable: true,
        autowidth: true,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [20, 50, 100],
        hidegrid: false,
        caption: " ",
        editurl: Routing.generate('info_perdos_manager_edit', {clientId:clientId, typecsd:0, typeresponsable: 1}),
        colNames: ['Nom', 'Prenom', 'Email', 'Skype',  '<span class="fa fa-bookmark-o " style="display:inline-block"/> Action'],
        colModel: [
            {
                name: 'resp-nom',
                index: 'resp-nom',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-nom'
            },
            {
                name: 'resp-prenom',
                index: 'resp-prenom',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-prenom'
            },
            {
                name: 'resp-mail',
                index: 'resp-mail',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                editrules: { custom: true, custom_func: verifier_mail_jqgrid },
                classes: 'js-resp-mail'
            },
            {
                name: 'resp-skype',
                index: 'resp-skype',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-skype'
            },
            {
                name: 'action', index: 'action', width: 60, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-cmc-save" title="Enregistrer"></i><i class="fa fa-trash icon-action js-cmc-delete" title="Supprimer"></i>'},
                classes: 'js-cmc-action'
            }
        ],

        onSelectRow: function (id) {
            if (id && id !== lastsel_mc) {
                chefMissionClient.restoreRow(lastsel_cmc);
                lastsel_cmc = id;
            }
            chefMissionClient.editRow(id, false);
        },

        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },

        loadComplete: function () {

            if ($("#btn-add-chef-mission-client").length == 0) {
                chefMissionClient.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-chef-mission-client" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }

        },


        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}

    });
    chefMissionScriptura.jqGrid({
        datatype: 'json',
        url: Routing.generate('info_perdos_manager',{clientId:clientId, typecsd:5, typeresponsable: 1}),
        loadonce: true,
        sortable: true,
        autowidth: true,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [20, 50, 100],
        hidegrid: false,
        caption: " ",
        editurl: Routing.generate('info_perdos_manager_edit', {clientId:clientId, typecsd:5, typeresponsable: 1}) ,
        colNames: ['Nom', 'Prenom', 'Email', 'Skype',  '<span class="fa fa-bookmark-o " style="display:inline-block"/> Action'],
        colModel: [
            {
                name: 'resp-nom',
                index: 'resp-nom',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-nom'
            },
            {
                name: 'resp-prenom',
                index: 'resp-prenom',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-prenom'
            },
            {
                name: 'resp-mail',
                index: 'resp-mail',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                editrules: { custom: true, custom_func: verifier_mail_jqgrid },
                classes: 'js-resp-mail'
            },
            {
                name: 'resp-skype',
                index: 'resp-skype',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-resp-skype'
            },
            {
                name: 'action', index: 'action', width: 60, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-cms-save" title="Enregistrer"></i><i class="fa fa-trash icon-action js-cms-delete" title="Supprimer"></i>'},
                classes: 'js-s-manager-action'
            }
        ],

        onSelectRow: function (id) {
            if (id && id !== lastsel_cms) {
                chefMissionScriptura.restoreRow(lastsel_cms);
                lastsel_cms = id;
            }
            chefMissionScriptura.editRow(id, false);
        },

        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },

        loadComplete: function () {
            if ($("#btn-add-chef-mission-scriptura").length == 0) {
                chefMissionScriptura.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-chef-mission-scriptura" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }
        },


        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}

    });

    $(document).on('click', '#btn-add-manager-client', function(){
        if(canAddRow(managerClientGrid)) {
            event.preventDefault();
            managerClientGrid.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
        }
    });

    $(document).on('click', '.js-mc-save', function (event) {
        event.preventDefault();
        event.stopPropagation();
        managerClientGrid.jqGrid('saveRow', lastsel_mc, {
            "aftersavefunc": function() {

                var url = Routing.generate('info_perdos_manager', {clientId:clientId, typecsd:0, typeresponsable: 4});
                managerClientGrid.jqGrid('setGridParam', { url: url }).trigger('reloadGrid');
            }
        });
    });

    $(document).on('click', '.js-mc-delete', function (event) {

        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');

        if(rowid =='new_row') {
            $(this).closest('tr').remove();
            return;
        }
        managerClientGrid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_manager_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });



    $(document).on('click', '#btn-add-manager-scriptura', function(){
        if(canAddRow(managerScriptura)) {
            event.preventDefault();
            managerScriptura.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
            // $("#" + "new_row", "#js_infoCarac_responsableDossier_liste").effect("highlight", 20000);
        }
    });

    $(document).on('click', '.js-ms-save', function (event) {
        event.preventDefault();
        event.stopPropagation();
        managerScriptura.jqGrid('saveRow', lastsel_ms, {
            "aftersavefunc": function() {
                var url = Routing.generate('info_perdos_manager', {clientId:clientId, typecsd:5, typeresponsable: 4});
                managerScriptura.jqGrid('setGridParam', { url: url }).trigger('reloadGrid');
            }
        });
    });

    $(document).on('click', '.js-ms-delete', function (event) {

        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');

        if(rowid =='new_row') {
            $(this).closest('tr').remove();
            return;
        }
        managerScriptura.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_manager_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });




    $(document).on('click', '#btn-add-chef-mission-client', function(){
        if(canAddRow(chefMissionClient)) {
            event.preventDefault();
            chefMissionClient.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
        }
    });

    $(document).on('click', '.js-cmc-save', function (event) {
        event.preventDefault();
        event.stopPropagation();
        chefMissionClient.jqGrid('saveRow', lastsel_cmc, {
            "aftersavefunc": function() {
                var url = Routing.generate('info_perdos_manager', {clientId:clientId, typecsd:0, typeresponsable: 1});
                chefMissionClient.jqGrid('setGridParam', { url: url }).trigger('reloadGrid');
            }
        });
    });

    $(document).on('click', '.js-cmc-delete', function (event) {

        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');

        if(rowid =='new_row') {
            $(this).closest('tr').remove();
            return;
        }
        chefMissionClient.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_manager_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });



    $(document).on('click', '#btn-add-chef-mission-scriptura', function(){
        if(canAddRow(chefMissionScriptura)) {
            event.preventDefault();
            chefMissionScriptura.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
            // $("#" + "new_row", "#js_infoCarac_responsableDossier_liste").effect("highlight", 20000);
        }
    });

    $(document).on('click', '.js-cms-save', function (event) {
        event.preventDefault();
        event.stopPropagation();
        chefMissionScriptura.jqGrid('saveRow', lastsel_cms, {
            "aftersavefunc": function() {

                var url = Routing.generate('info_perdos_manager', {clientId:clientId, typecsd:5, typeresponsable: 1});
                chefMissionScriptura.jqGrid('setGridParam', { url: url }).trigger('reloadGrid');
            }
        });
    });

    $(document).on('click', '.js-cms-delete', function (event) {

        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');

        if(rowid =='new_row') {
            $(this).closest('tr').remove();
            return;
        }
        chefMissionScriptura.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_manager_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

}

function verifier_mail_jqgrid(posdata, colName) {
    var message = "";
    if (posdata != '' && isValidEmailAddress(posdata))
        return [true, ""];

    else {
        if (posdata == '')
            message = "Le champ " + colName + " est obligatoire";
        else{
            message = posdata + " est un mail invalide";
        }
    }

    setTimeout(function () {
        $("#info_dialog").hide();
    }, 10);

    show_info_perdos('INFORMATION', message, 'warning');


    return [false, ""];
}

