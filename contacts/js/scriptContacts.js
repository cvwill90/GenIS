function autocompleteTown(event){
    var ville = $('#ville');
    ville.autocomplete({
        source: '../../libraries/ajax/suggestTown.php?',
        dataType: "json",
        select: function(event,ui){
            event.preventDefault();
            $('#ville').val(ui.item.label);
            $('#idVille').val(ui.item.value);
            $('#codePostal').prop('disabled',true);
            $('#codePostal').val(ui.item.zip);
        },
        focus: function(event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which !== 13 && event.which !== 9 && event.which !== 16){  //si je modifie le champ
        if(document.getElementById('idVille').value !== 1){            //si la ville est préexistante
            document.getElementById('idVille').value = 1;                 //alors la ville devient non préexistante
            $('#codePostal').val('');                                   //et on supprime le code postal
        }                                                           //sinon la ville n'est pas préexistante
        $('#codePostal').prop('disabled',false);                        //alors il faut rendre le champ cp disponible pour puvoir renseigner un nouv cp
    }
}

function nonexistingFarm(){
    $('#idDbElevage').val('');
    $('#idElevage').val('');
    $('#nomElevage').val('');
    var checkboxes = $('input[type=checkbox]');
    checkboxes.attr('checked', false);
    $('#idElevage').attr('disabled',false);
}

function existingFarm(type = false){
    if (!type){
        $('#idElevage').attr('disabled',true);
    }
    var checkboxes = $('input[type=checkbox]');
    checkboxes.attr('checked', false);
}

function triggerAutoCompleteFarm(event, type = false) {
    var nomElevage = $('#nomElevage');
    if ($('#nouvelElevage').prop('checked') === false){
        nomElevage.autocomplete({
            source: "../../libraries/ajax/suggestFarm.php?",
            dataType: "json",
            select: function(event,ui){
                event.preventDefault();
                $('#nomElevage').val(ui.item.nom_elevage);
                $('#idElevage').val(ui.item.no_elevage);
                $('#idDbElevage').val(ui.item.id);
                var racesNumber = ui.item.races.length, i=0;
                while (i < racesNumber){
                    var index = ui.item.races[i];
                    document.getElementById(index).checked = true;
                    i++;
                }
            },
            focus: function(event,ui){
                event.preventDefault();
            },
            minLength: 2
        });
        nomElevage.on('input',function(){
            if (type === false){
                document.getElementById('idDbElevage').value = '';
                $('#idElevage').val('');
            }
        });
    }
}

function insertContactDB(){
    if (document.getElementById('ville').value === '') {
        alert('Veuillez renseigner une ville.');
    } else {
        if($('#codePostal').val() === ''){                       //si cp non renseigné
            alert('Veuillez renseigner un code postal.');           //alors il faut le renseigner
        } else {
            var formContent = $('#newContact').serialize();
            if (document.getElementById('idVille').value === 1) { // si on a renseigné une ville non préexistante                                              //si si le code postal a été renseigné, on soumet le formulaire
                if (confirm('Le lieu '+ document.getElementById('ville').value.toUpperCase() +' ('+ document.getElementById('codePostal').value +') va être ajouté à la base de données.')){
                    ajaxContact(formContent, 'ajaxNouvContact');
                }
            } else { //Si on a renseigné un nom de ville préexistante
                ajaxContact(formContent, 'ajaxNouvContact');
            }
        }
    }
}

/**
 * Fonction remplissant la liste de sélection des départements en fonction de ce qui est choisi comme région
 */

function fillup_dep(activeDepartment) {
    var str='';
    var region = $("#region").find(":selected").val();
    $.when(
        $.ajax({
            method: 'GET',
            url: '../../libraries/ajax/getDepartements.php?region='+ region,
            dataType: "json",
            success : function(data, status){
                $.each(data, function (i,region) {
                    str = str + '<option value="'+ region.value +'">'+ region.label +'</option>';
                });
                $('#departement').html(str);
                $('#departement').prop("disabled",false);
                $('#departement').val(activeDepartment);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('Le serveur a rencontré l\'erreur suivante : '+xhr.status + " "+ thrownError);
            }
        })
    ).then(function(){});
}

function radioChange() {
    if ($('#eleveur1').prop('checked')) {
        $('.fieldsetEleveur').show();
    }
    if ($('#eleveur2').prop('checked')){
        $('.fieldsetEleveur').hide();
    }
}

/*
    Modify Contact part
*/

function autocompleteContact(event){
    var contact = $('#chooseContact');
    contact.autocomplete({
        source: "../../libraries/ajax/suggestContactInfo.php?",
        dataType: "json",
        select: function(event,ui){
            event.preventDefault();
            console.log(ui.item)
            contact.val(ui.item.nom + ', ' + ui.item.prenom);
            $('#idDbContact').val(ui.item.id_contact);
            $('#nomElevage').val(ui.item.nom_elevage);
            $('#idElevage').val(ui.item.no_elevage);
            $('#idDbElevage').val(ui.item.id_elevage);
            $('#nom').val(ui.item.nom);
            $('#prenom').val(ui.item.prenom);
            $('#adresse').val(ui.item.adresse);
            $('#adresseCompl').val(ui.item.adresse2);
            $('#region').val(ui.item.no_region);
            fillup_dep(ui.item.no_dpt);
            $('#ville').val(ui.item.lib_commune);
            $('#codePostal').val(ui.item.cp_commune);
            $('#mail').val(ui.item.mail);
            $('#tel1').val(ui.item.tel);
            $('#tel2').val(ui.item.tel2);
            $('#notes').val(ui.item.notes);
            $('#idVille').val(ui.item.id_commune);
            var racesNumber = ui.item.races.length, i=0;
            console.log(ui.item.races)
            while (i < racesNumber){
                var index = ui.item.races[i];
                console.log('index' + index)
                document.getElementById(index).checked = true;
                i++;
            }
            
            if (ui.item.id_elevage !== ""){
                $('#eleveur1').prop("checked", true);
            } else {
                $('#eleveur2').prop("checked", true);
            }
            radioChange();
            $('#contactValid').prop("disabled", false);
        },
        focus: function(event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which !== 13 && event.which !== 9 && event.which !== 16){        
        $('#idDbContact').val('');
        $('#idDbElevage').val('');
        $('#nomElevage').val('');
        $('#idElevage').val('');
        $('#nom').val('');
        $('#prenom').val('');
        $('#adresse').val('');
        $('#adresseCompl').val('');
        $('#region').val(2);
        fillup_dep("");
        $('#ville').val('');
        $('#codePostal').val('');
        $('#mail').val('');
        $('#tel1').val('');
        $('#tel2').val('');
        $('#notes').val('');
        $('#idVille').val('');
        if (document.getElementById('idDbContact').value === '') {
            $('#eleveur2').prop("checked", true);
            radioChange();
            $('#contactValid').prop("disabled", true);
        }
        
        var checkboxes = $('input[type=checkbox]');
        checkboxes.attr('checked', false);
    }
}

function modifyContactDB(){
    if (document.getElementById('ville').value === '') {
        alert('Veuillez renseigner une ville.');
    } else {
        if($('#codePostal').val() === ''){                       //si cp non renseigné
            alert('Veuillez renseigner un code postal.');           //alors il faut le renseigner
        } else {
            var formContent = $('#modifyContact').serialize();
            if (document.getElementById('idVille').value === 1) { // si on a renseigné une ville non préexistante                                              //si si le code postal a été renseigné, on soumet le formulaire
                if (confirm('Le lieu '+ document.getElementById('ville').value.toUpperCase() +' ('+ document.getElementById('codePostal').value +') va être ajouté à la base de données.')){
                    ajaxContact(formContent, 'ajaxModifyContact');
                }
            } else { //Si on a renseigné un nom de ville préexistante
                ajaxContact(formContent, 'ajaxModifyContact');
            }
        }
    }
}

function ajaxContact(formContent, ajaxFile){
    $.ajax({
        method: 'GET',
        url: '../../libraries/ajax/' + ajaxFile + '.php',
        data: formContent,
        dataType: 'json',
        success : function(data, status){
            if(data.statusMsg === 'ok'){
                window.location.replace('resultContact.php');
            }else{
                alert('L\'erreur suivante a été retournée par le programme : '+ data.response.errorMsg);
            }
        },
        error : function(xhr, status, thrownError){
            alert('Une erreur globale a eu lieu lors de l\'insertion ou de la modification du contact dans la base de données. Erreur renvoyée :\n'+ thrownError);
        }
    });
}
