/**
 * Created by SITRAKA on 16/01/2017.
 */
var lastsel = null,
    isReady = false,
    chargement = true;

$(document).ready(function(){
    show_loading(false);
    isReady = true;
    initialize_contenu_aide(Routing.generate('aide_miniature'), 1, '');

    var hostname_list = ["lesexperts.biz"];

    function check_hostname() {
        var current_hostname = window.location.hostname;
        var checked =  hostname_list.includes(current_hostname);
        if (checked) {
            return true
        } else {
            return false;
        }
    }

    var check = check_hostname();

    if (check) {
        if (typeof zE == 'function') {
            if (navigator.onLine) {
                console.log('connexion ok');
                $.ajax({
                    url: Routing.generate('app_infos_user'),
                    type: 'GET',
                    success: function(data) {

                        let infos = {};

                        if (is_exist(data.prenom)) {

                             infos.name = data.prenom;

                             if (is_exist(data.nom)) {
                                // infos.name.concat(' ', data.nom);
                                infos.name += ' ' + data.nom;
                             }

                             if (is_exist(data.tel)) {
                                 infos.phone = data.tel;
                             }

                             if (is_exist(data.email)) {
                                 infos.email = data.email;
                             }

                             ze_identify(infos);
                        } else {

                            if (is_exist(data.nom)) {

                               infos.name = data.nom;

                               if (is_exist(data.tel)) {
                                     infos.phone = data.tel;
                               }

                               if (is_exist(data.email)) {
                                   infos.email = data.email;
                               }

                               ze_identify(infos);
                            }
                        }

                        delete infos; 

                    }
                })
            }
        } else {
            console.log('conexion local');
        }
    } 

    function is_exist(argument) {
        if (argument && argument != undefined && argument != '') {
            return true;
        } else {
            return false;
        }
    }

    function ze_identify(infos) {

        console.log(infos);

        zE(function() {
            zE.identify(infos);
        });
    }

});

function show_loading(actif)
{
    actif = typeof actif !== 'undefined' ? actif : true;
    if (actif && chargement) $('body').loadingModal({text: 'Chargement...'});
    else $('body').loadingModal('destroy');
}

/**
 * verrou et progressbar
 */
$(document).ajaxStart(function(){
    show_loading(true);
});
$(document).ajaxStop(function(){
    show_loading(false);
});
function verrou_fenetre(verrou)
{
    if(verrou) show_loading(true);
    else show_loading(false);
}

function initialize_contenu_aide(lien, typeMiniature, searchText){
    $.ajax({
        url: lien,
        async: true,
        type: 'POST',
        dataType: 'html',
        data: { searchText: searchText, url: $(location).attr('href') },
        success: function (data) {
            $('#js_aide').html(data);
            ready_inspinia();
            set_aide_size(typeMiniature);
        }
    });
}


/**
 *
 * @param type
 */
function set_aide_size(type) {

    $('#js_aide > div.heading > div > div > input').css({color: '#676a6c'});

    switch (type){
        //1: index
        case 1:
            $(".small-chat-box").css({height: '350px'});
            $('#js_aide .slimScrollDiv').css({height: '200px'});
            $('#js_aide .content').css({height: '200px'});
            break;
        //2: mail, aide
        case 2:
            $(".small-chat-box").css({height: '450px'});
            $('#js_aide .slimScrollDiv').css({height: '340px'});
            $('#js_aide .content').css({height: '340px'});
            break;
        //3: aide
        case 3:
            $(".small-chat-box").css({height: '450px'});
            $('#js_aide .slimScrollDiv').css({height: '340px'});
            $('#js_aide .content').css({height: '340px'});

            var imgs = $('#js_aide .content img');

            imgs.each(function () {
                if($(this).width() > 340){
                    $(this).css({width: '300px'});
                    $(this).css({height: ''});
                }
            });

            break;

        default:
            $(".small-chat-box").css({height: '350px'});
            $('#js_aide .slimScrollDiv').css({height: '250px'});
            $('#js_aide .content').css({height: '250px'});
            break;
    }

}
