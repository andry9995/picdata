/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Charge le formulaire d'ajout de contact
 * @returns {undefined}
 */
function loadNewContactClient() {
    $.ajax({
        url: Routing.generate('one_contact_client_new'),
        type: 'POST',
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            openModal();
        }
    });
}

/**
 * Charge le formualaire d'édition d'un contact
 * @param {string} classname
 * @returns {undefined}
 */
function loadEditContactClient(classname) {
    $.ajax({
        url: Routing.generate('one_contact_client_edit'),
        type: 'POST',
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            $('#contact-client-form').append('<input type="hidden" id="old-class-name" value="'+classname+'">');
            var serialized = $('.'+classname+' .serialized').val();
            fillEditForm(serialized);
            openModal();
        }
    });
}

/**
 * Remplit le formalaire d'édtion par les valeurs stockées
 * @param {string} serialized
 * @returns {undefined}
 */
function fillEditForm(serialized) {
    //nom=Castellan&prenom=&email=&tel-portable=&tel-pro=&tel-perso=&adresse-1=&adresse-2=&ville=&code-postal=&pays=130&note=&service=&fonction=
    var form = $('#contact-client-form');
    var keyvalues = serialized.split('&');
    $.each(keyvalues, function(key, value) {
        var k = value.split('=')[0];
        var v = value.split('=')[1];
        if (k === 'id') form.find('#id').val(v.replace('+', ' '));
        if (k === 'nom') form.find('#nom').val(v.replace('+', ' '));
        if (k === 'prenom') form.find('#prenom').val(v);
        if (k === 'email') form.find('#email').val(v);
        if (k === 'tel-portable') form.find('#tel-portable').val(v);
        if (k === 'tel-pro') form.find('#tel-pro').val(v);
        if (k === 'tel-perso') form.find('#tel-perso').val(v);
        if (k === 'adresse-1') form.find('#adresse-1').val(v);
        if (k === 'adresse-2') form.find('#adresse-2').val(v);
        if (k === 'ville') form.find('#ville').val(v);
        if (k === 'code-postal') form.find('#code-postal').val(v);
        if (k === 'pays') form.find('#pays').val(v);
        if (k === 'note') form.find('#note').val(v);
        if (k === 'service') form.find('#service').val(v);
        if (k === 'fonction') form.find('#fonction').val(v);
    });
}

/**
 * Ajout d'un contact à la liste
 * @returns {undefined}
 */
function addContactClient() {
    var form = $('#contact-client-form');
    var nomField = form.find('#nom');
    var prenomField = form.find('#prenom');
    var emailField = form.find('#email');
    var telPortableField = form.find('#tel-portable');
    var telProField = form.find('#tel-pro');
    var telPersoField = form.find('#tel-perso');
    var valid = validateField(nomField);
    if (valid) {
        //Nom et prénom
        var name = nomField.val();
        if (prenomField.val() !== '') {
            name = prenomField.val() + ' ' + name;
        }
        //Email
        var email = "Pas d'email";
        if (emailField.val() !== '') {
            email = emailField.val();
        }
        //Telephone
        var tel = "Pas de téléphone";
        if (telPortableField.val() !== '') {
            tel = telPortableField.val();
        } else if (telProField.val() !== '') {
            tel = telProField.val();
        } else if (telPersoField.val() !== '') {
            tel = telPersoField.val();
        }
        
        //var classname = name.toLowerCase().replace(' ', '-');
        var classname = moment().valueOf();
        var contactLine = '<div class="row '+classname+'">';
        var contactField = '<input type="hidden" class="serialized" name="contacts[]" value="'+form.serialize()+'" />';
        contactLine = contactLine+contactField;
        contactLine = contactLine+'<div class="col-sm-3">'+name+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> '+email+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> '+tel+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><div class="pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true" onclick="loadEditContactClient(\''+classname+'\')"></span> <span class="glyphicon glyphicon-trash" aria-hidden="true" onclick="removeContactClient(\''+classname+'\')"></span></div></div>';
        contactLine = contactLine+'</div>';
        
        //Ajoute le nouveau ou celui modifié
        $('#contact-row .contact-list').append(contactLine);
        //Ferme le modal
        closeModal();
    }
}

/**
 * Edition d'un contact de la liste
 * @returns {undefined}
 */
function editContactClient() {
    var form = $('#contact-client-form');
    var oldClassname = form.find('#old-class-name').val();
    var nomField = form.find('#nom');
    var prenomField = form.find('#prenom');
    var emailField = form.find('#email');
    var telPortableField = form.find('#tel-portable');
    var telProField = form.find('#tel-pro');
    var telPersoField = form.find('#tel-perso');
    var valid = validateField(nomField);
    if (valid) {
        //Nom et prénom
        var name = nomField.val();
        if (prenomField.val() !== '') {
            name = prenomField.val() + ' ' + name;
        }
        //Email
        var email = "Pas d'email";
        if (emailField.val() !== '') {
            email = emailField.val();
        }
        //Telephone
        var tel = "Pas de téléphone";
        if (telPortableField.val() !== '') {
            tel = telPortableField.val();
        } else if (telProField.val() !== '') {
            tel = telProField.val();
        } else if (telPersoField.val() !== '') {
            tel = telPersoField.val();
        }
        
        //var classname = name.toLowerCase().replace(' ', '-');
        var classname = moment().valueOf();
        var contactLine = '<div class="row '+classname+'">';
        var contactField = '<input type="hidden" class="serialized" name="contacts[]" value="'+form.serialize()+'" />';
        contactLine = contactLine+contactField;
        contactLine = contactLine+'<div class="col-sm-3">'+name+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> '+email+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> '+tel+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><div class="pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true" onclick="loadEditContactClient(\''+classname+'\')"></span> <span class="glyphicon glyphicon-trash" aria-hidden="true" onclick="removeContactClient(\''+classname+'\')"></span></div></div>';
        contactLine = contactLine+'</div>';
        
        //Supprime l'ancien
        removeContactClient(oldClassname);
        //Ajoute le nouveau ou celui modifié
        $('#contact-row .contact-list').append(contactLine);
        //Ferme le modal
        closeModal();
    }
}

function removeContactClient(classname) {
    $('#contact-row .contact-list .'+classname).remove();
}
