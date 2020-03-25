/**************************************
 *          EVENEMENTS
**************************************/
//ready
$(document).ready(function(){
   charger_etat();
   gerer_height();
   activer_combow('.selectpicker','btn-white');
});

//change regime
$(document).on('change','#regime_fiscal',function(){
   charger_etat();
});

//change site
$(document).on('change','#site',function(){
   charger_dossier();
});

//change dossier
$(document).on('change','#dossier',function(){
   charger_etat(); 
});


/**************************************
 *          FONCTIONS
**************************************/
//charger etat financier
function charger_etat()
{
   set_panel_2_empty();
   
   etat = parseInt($('#etat_select').val().trim());   
   
   regime = ($('#regime_fiscal').length > 0) ? $('#regime_fiscal').val() : 0;
   dossier = ($('#dossier').length > 0) ? $('#dossier').val() : 0;
   lien = Routing.generate('etat_financier_show')+'/'+etat+'/'+dossier+'/'+regime;

   if(regime != 0 && $('#regime_fiscal option:selected').text().trim() == '' || dossier != 0 && $('#dossier option:selected').text().trim() == '')
   {
      message = ($('#regime_fiscal').length > 0) ? 'REGIME FISCAL' : 'DOSSIER';
      show_info('Notice','Choisir le ' + message,'warning');
      return;
   }

   verrou_fenetre(true);
   $.ajax({
      data: {},
      url: lien,
      contentType: "application/x-www-form-urlencoded;charset=utf-8",
      beforeSend: function(jqXHR) {
         jqXHR.overrideMimeType('text/html;charset=utf-8');
      },
      dataType: 'html',
      success: function(data){
         test_security(data);
         $('#etat').html(data);
         menu_context();
      }
   });
}

//activer menu context
function menu_context()
{  
   $(function(){
      $('#etat table tbody tr td a').contextMenu('destroy');
      $.contextMenu({
         trigger: 'left',
         selector: '#etat table tbody tr td a.js_menu_context', 
         callback: function(key, options){
            edit_etat_item(parseInt(key),$(this));
         },
         items:{
            "0": {name: "&nbsp;&nbsp;Ajouter&nbsp;avant", icon: "fa-level-up", accesskey: ""},
            "1": {name: "&nbsp;&nbsp;Ajouter&nbsp;apr&egrave;s", icon: "fa-level-down", accesskey: ""},
            "2": {name: "&nbsp;&nbsp;Ajouter&nbsp;fille", icon:"fa-sitemap", acceskey:""},
            "3": {name: "&nbsp;&nbsp;Modifier", icon: "fa-pencil-square-o", accesskey: ""},
            "4": {name: "&nbsp;&nbsp;Supprimer", icon: "fa-trash", accesskey: ""}
         },
         events: {
            show : function(){
               $(this).addClass('label-primary');
            },
            hide : function(){
               $(this).removeClass('label-primary');
            }
         }
      });
      
      $('.context-menu-one').on('click', function(e){
         console.log('clicked', this);
      });
   });

   $('.context-menu-list').addClass('dropdown-menu animated fadeInLeft');
}

//reinitialiser panel 2
function set_panel_2_empty()
{
   $('#etat').empty();
   $('#compte_brut').empty();
   $('#compte_amort').empty();

   for(i=1;i<5;i++)
   {
      $('#etat_'+i).empty();
   }
}