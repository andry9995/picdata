$(document).ready(function(){
    $(document).on('click','.cl_picdata',function(){
        var l = $(this).attr('data-lien');
        $.ajax({
            data: { },
            url: Routing.generate('app_operateur_by_utilisateur'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                if (parseInt(data) === 0) show_info('Operateur','Pas de correspondance sur INTRANET','error');
                else
                {
                    // var url = 'http://intranet.mg/app_dev.php/login_auto?l='+data+'&ln='+l;
                    // var url = 'http://192.168.0.5/login_auto?l='+data+'&ln='+l;
                    // var url = 'http://192.168.0.5/login_auto?l='+data+'&ln='+l;
                    var url = 'http://intranet.lesexperts.biz/login_auto?l='+data+'&ln='+l;
                    var win = window.open(url, '_blank');
                    win.focus();
                }
            }
        });
    });
});