/**
 * Created by TEFY on 13/02/2017.
 */

$(function() {
   setTimeout(function() {
        var login_url = Routing.generate('login');
        window.location = login_url;
   }, 5000);
});
