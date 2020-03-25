$(document).on('click','#js_signer',function(){
    $.ajax({
        data: {  },
        type: 'POST',
        url: Routing.generate('img_signer'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            alert(data);
        }
    });
});