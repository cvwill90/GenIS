/**
 * Created by Christophe_2 on 01/03/2016.
 */

/**
 * Fonction remplissant la liste de sélection des races en fonction de ce qui est choisi comme espèce
 */

function fillup_race() {
    var str='';
    var specie = $("#espece").find(":selected").val();
    $.ajax({
        method: 'GET',
        url: '../../libraries/ajax/getRaces.php?espece='+specie,
        dataType: "json",
        success : function(data, status){
            $.each(data, function (i,espece) {
                str = str + '<option value="'+ espece.value +'">'+ espece.label +'</option>';
            });
            $('#race').html(str);
            $('#race').prop("disabled",false);
            $('#chooseAnimal').prop("disabled",false);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('Le serveur a rencontré l\'erreur suivante : '+xhr.status + " "+ thrownError);
        },
        complete : function(data, status){
        }
    });
}

/**
 * Fonction d'autocomplétion proposant les animaux existants dans la bdd
 * @param event
 */

function triggerAutocompleteAnimal(event) {
    var race = $('#race').val();
    var animal = $('#chooseAnimal');
    var pere = $('#father');
    var mere = $('#mother');
    var animalNo = $('#animalID');
    var animalName = $('#animalName');
    var animalMale = $('#animalMale');
    var animalFemale = $('#animalFemale');
    var animalCastre = $('#animalCastre');
    var birthDate = $('#birthDate');
    var deathDate = $('#deathDate');
    var animalDead = $('#animal_dead');
    var birthFarm = $('#birthFarm');
    var conserv1 = $('#conserv1');
    var conserv2 = $('#conserv2');
    var animalId = $("#IDanimalChoisi");
    var pereId = $('#fatherId');
    var mereId = $('#motherId');
    var elevageId = $('#farmId');
    var livre_gene = $('#livre_gene');
    animal.autocomplete({
        source: "../../libraries/ajax/ajaxModifierAnimal.php?type=1&race="+race,
        dataType: "json",
        select: function (event,ui){
            event.preventDefault();
            animal.val(ui.item.value);
            pere.val(ui.item.nom_p);
            mere.val(ui.item.nom_m);
            animalNo.val(ui.item.no);
            animalName.val(ui.item.value);
            birthDate.val(ui.item.date_naiss);
            birthFarm.val(ui.item.nom_elev);
            deathDate.val(ui.item.date_mort);
            animalId.val(ui.item.id);
            pereId.val(ui.item.id_p);
            mereId.val(ui.item.id_m);
            livre_gene.val(ui.item.livre === "" ? "NULL" : ui.item.livre);
            
            if (ui.item.id_elev !== '') {
                elevageId.val(ui.item.id_elev);
            }
            
            if (ui.item.sexe === 1){
                animalMale.prop("checked", true);
            } else if (ui.item.sexe === 3) {
                animalCastre.prop("checked", true);
            } else {
                animalFemale.prop("checked", true);
            }
            
            if (ui.item.cons) {
                conserv2.prop("checked", true);
            } else {
                conserv1.prop("checked", true);
            }
            
            if (ui.item.date_mort !== '') {
                animalDead.prop("checked", true);
                animalDead.prop("disabled", false);
                deathDate.prop("disabled", false);
            } else {
                animalDead.prop("checked", false);
                animalDead.prop("disabled", true);
                deathDate.prop("disabled", true);
            }
            
            if (livre_gene) {
                
            }
        },
        focus: function (event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which!==13 && event.which!==9 && event.which!==16){
        event.preventDefault();
        pere.val('');
        mere.val('');
        animalNo.val('');
        animalName.val('');
        birthDate.val('');
        birthFarm.val('');
        animalId.val('');
        pereId.val(1);
        mereId.val(2);
        elevageId.val(0);
    }
}

/**
 * Fonction d'autocomplétion proposant les fermes existantes dans la BDD
 * @param event
 */

function triggerAutoCompleteFarm(event) {
    $('#birthFarm').autocomplete({
        source: "../../libraries/ajax/suggestFarm.php?",
        dataType: "json",
        select: function(event,ui){
            event.preventDefault();
            $('#birthFarm').val(ui.item.nom_elevage);
            $('#farmId').val(ui.item.id);
        },
        focus: function(event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which !== 13 && event.which !== 9 && event.which !== 16){
        if (document.getElementById('birthFarm').value === ''){
            document.getElementById('farmId').value='0';
        } else {
            document.getElementById('farmId').value='';
        }
    }
}

/**
 * Fonction d'autocomplétion proposant les mâles existants dans la bdd
 * gère également l'autoremplissage du champ "lignee"
 * @param event
 */

function triggerAutocompleteMale(event) {
    var Race = $('#race').val();
    var father = $("#father" );
    var fatherId = $('#fatherId');
    father.autocomplete({
        source: "../../libraries/ajax/suggestAnimal.php?sex=1&race="+Race,
        dataType: "json",
        select: function (event,ui){
            event.preventDefault();
            father.val(ui.item.value);
            fatherId.val(ui.item.id);         //on injecte bien la valeur de la bdd dans le champ fatherId
        },
        focus: function (event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which !== 13 && event.which !== 9 && event.which !== 16){
        if (document.getElementById('father').value === ''){
            document.getElementById('fatherId').value = '1';
        } else {
            document.getElementById('fatherId').value='';
            //document.getElementById('lignee').value='';
        }
    }
}

/**
 * Fonction d'autocomplétion proposant les femelles existantes dans la bdd
 * gère également l'autoremplissage du champ "famille"
 * @param event
 */

function triggerAutocompleteFemale(event) {
    var Race=$('#race').val();
    var mother = $('#mother');
    var motherId = $('#motherId');
    mother.autocomplete({
        source: "../../libraries/ajax/suggestAnimal.php?sex=2&race="+Race,
        dataType: "json",
        select: function (event,ui){
            event.preventDefault();
            mother.val(ui.item.value);
            motherId.val(ui.item.id);
            //target2.val(ui.item.ancetre);
        },
        focus: function (event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which!==13 && event.which!==9 && event.which!==16){
       if (document.getElementById('mother').value === ''){
           document.getElementById('motherId').value='2';
       } else {
           document.getElementById('motherId').value='';
           //document.getElementById('famille').value='';
       }
    }
}

function animal_death(event) {
    if (event.target.checked){
        $('#deathDate').prop("disabled", false);
    } else {
        $('#deathDate').prop("disabled", true);
        $('#deathDate').val("");
    }
    
}

/**
 * Lorsqu'on quitte un champ d'autocomplétion (onblur),
 * @param this_element
 * @param target
 */

function check_if_empty(this_element,target){
    if (document.getElementById(this_element).value === '') {
        document.getElementById(target).value = 1;
    }
}

function modifAnimal(){
    var formContent = $('#formModif').serialize();
    if ($('#fatherId').val() !== '' && $('#motherId').val() !== '' && $('#farmId').val() !== '') {
        $.ajax({
            method: "GET",
            dataType: "json",
            data: formContent,
            url: "../libraries/ajax/ajaxModifierAnimal.php?type=2",
            success: function (data) {
              if (data.status == 'ok'){
                window.location.replace('resultModif.php');
              } else {
                alert(data.statusMsg);
              }
            },
            error: function () {
              alert('Erreur globale ! (script PHP erroné...)');
            }
        });
    } else {
        alert('Tous les champs requis n\'ont pas été remplis !');
    }
    
}