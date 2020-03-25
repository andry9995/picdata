/**
 * Ajout d'un contact à la liste
 * @returns {undefined}
 */
function addContactFournisseur() {
    var form = $('#contact-fournisseur-form');
    var nomField = form.find('#nom');
    var prenomField = form.find('#prenom');
    var emailField = form.find('#email');
    var telPortableField = form.find('#tel-portable');

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
        }

        var classname = moment().valueOf();
        var contactLine = '<div class="row '+classname+'">';
        var contactField = '<input type="hidden" class="serialized" name="contacts[]" value="'+form.serialize()+'" />';
        contactLine = contactLine+contactField;
        contactLine = contactLine+'<div class="col-sm-3">'+name+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> '+email+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> '+tel+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><div class="pull-right">' +
            '<span class="glyphicon glyphicon-pencil" aria-hidden="true" onclick="loadEditContactFournisseur(\''+classname+'\')"></span> ' +
            '<span class="glyphicon glyphicon-trash" aria-hidden="true" onclick="removeContactFournisseur(\''+classname+'\')"></span>' +
            '</div></div>';
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
function editContactFournisseur() {
    var form = $('#contact-fournisseur-form');
    var oldClassname = form.find('#old-class-name').val();
    var nomField = form.find('#nom');
    var prenomField = form.find('#prenom');
    var emailField = form.find('#email');
    var telPortableField = form.find('#tel-portable');

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
        }

        var classname = moment().valueOf();
        var contactLine = '<div class="row '+classname+'">';
        var contactField = '<input type="hidden" class="serialized" name="contacts[]" value="'+form.serialize()+'" />';
        contactLine = contactLine+contactField;
        contactLine = contactLine+'<div class="col-sm-3">'+name+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> '+email+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> '+tel+'</div>';
        contactLine = contactLine+'<div class="col-sm-3"><div class="pull-right">' +
            '<span class="glyphicon glyphicon-pencil" aria-hidden="true" onclick="loadEditContactFournisseur(\''+classname+'\')"></span> ' +
            '<span class="glyphicon glyphicon-trash" aria-hidden="true" onclick="removeContactFournisseur(\''+classname+'\')"></span>' +
            '</div></div>';
        contactLine = contactLine+'</div>';

        //Supprime l'ancien
        removeContactFournisseur(oldClassname);
        //Ajoute le nouveau ou celui modifié
        $('#contact-row .contact-list').append(contactLine);
        //Ferme le modal
        closeModal();
    }
}



/**
 * Remplit le formalaire d'édtion par les valeurs stockées
 * @param {string} serialized
 * @returns {undefined}
 */
function fillEditForm(serialized) {
    //nom=Castellan&prenom=&email=&tel-portable=&tel-pro=&tel-perso=&adresse-1=&adresse-2=&ville=&code-postal=&pays=130&note=&service=&fonction=
    var form = $('#contact-fournisseur-form');
    var keyvalues = serialized.split('&');
    $.each(keyvalues, function(key, value) {
        var k = value.split('=')[0];
        var v = value.split('=')[1];
        if (k === 'id') form.find('#id').val(v.replace('+', ' '));
        if (k === 'nom') form.find('#nom').val(v.replace('+', ' '));
        if (k === 'prenom') form.find('#prenom').val(v);
        if (k === 'email') form.find('#email').val(v);
        if (k === 'tel-portable') form.find('#tel-portable').val(v);
        if (k === 'adresse') form.find('#adresse').val(v);
        if (k === 'code-postal') form.find('#code-postal').val(v);
        if (k === 'ville') form.find('#ville').val(v);
        if (k === 'pays') form.find('#pays').val(v);
    });
}


/**
 * Charge le formualaire d'édition d'un contact
 * @param {string} classname
 * @returns {undefined}
 */
function loadEditContactFournisseur(classname) {
    $.ajax({
        url: Routing.generate('one_contact_fournisseur_edit'),
        type: 'POST',
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            $('#contact-fournisseur-form').append('<input type="hidden" id="old-class-name" value="'+classname+'">');
            var serialized = $('.'+classname+' .serialized').val();
            fillEditForm(serialized);
            openModal();
        }
    });
}



/**
 * Charge le formulaire d'ajout de contact fournisseur
 * @returns {undefined}
 */
function loadNewContactFournisseur() {
    $.ajax({
        url: Routing.generate('one_contact_fournisseur_new'),
        type: 'POST',
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            openModal();
        }
    });
}


function removeContactFournisseur(classname) {
    $('#contact-row .contact-list .'+classname).remove();
}
