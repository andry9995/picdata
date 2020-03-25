/*************************
 *      EVENEMENTS
*************************/
//ready
$(document).ready(function(){
   charger_dossier_table(); 
});
//change site
$(document).on('change','#site',function(){
    charger_dossier_table();
});
//change status
$(document).on('click','#js_table_activation tbody tr td span.js_activation',function(){
   change_status($(this));
});


/*************************
 *      FONCTIONS
*************************/
//function charger dossier activation dossier
function charger_dossier_table()
{
   site = parseInt($('#site').val().trim());
   lien = Routing.generate('dossier_dossiers')+'/'+site;   
   
   $.ajax({
       data: {},
       url: lien,
       contentType: "application/x-www-form-urlencoded;charset=utf-8",
       beforeSend: function(jqXHR) {
           jqXHR.overrideMimeType('text/html;charset=utf-8');
       },
       dataType: 'html',
       success: function(data){
           $('#js_activation_dossier').html(data);
           gerer_height();
       }
   });
}

//function change status
function change_status(span)
{
   $('.tr_edit').removeClass('tr_edit');
   active = !span.hasClass(get_class_activation(0,false));
   status = parseInt(span.attr('data-status'));
   
   if(active || span.parent().parent().find('span.'+get_class_activation(3,true)).length > 0) return;

   span.parent().parent().addClass('tr_edit');
   clear_tr();
   span.removeClass(get_class_activation(status,active)).addClass(get_class_activation(status,!active));
}

//function get class
function get_class_activation(status,active)
{
   if(!active) return 'non-actif';
   if(status == 1) return 'btn-primary';
   if(status == 2) return 'btn-warning';
   if(status == 3) return 'btn-danger';
}
//function clear span in tr
function clear_tr()
{
   $('.tr_edit span')
      .removeClass(get_class_activation(1,true))
      .removeClass(get_class_activation(2,true))
      .removeClass(get_class_activation(3,true))
      .addClass(get_class_activation(0,false));
      
   $('.tr_edit').removeClass('tr_edit');
}