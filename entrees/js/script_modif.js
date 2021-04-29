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
              if (data.status === 'ok'){
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

$(document).ready(function (){
    $('#chooseAnimal').select2({
        ajax: {
            url: "../../libraries/ajax/ajaxModifierAnimal.php",
            data: function (params) {
                var query = {
                    term: params.term,
                    race: $('#race').val(),
                    type: 1
                };
                return query;
            },
            dataType: 'json',
            processResults: function (data) {
                var options = {
                    "results": []
                };
                $.each(data, function (i) {
                    options.results[i] = {
                        "id": data[i].id,
                        "text": data[i].label,
                        "animalName": data[i].value,
                        "sex": data[i].sexe,
                        "identificationNumber": data[i].no,
                        "birthDate": data[i].date_naiss,
                        "deathDate": data[i].date_mort,
                        "conservatoire": data[i].cons,
                        "fatherId": data[i].id_p,
                        "motherId": data[i].id_m,
                        "fatherName": data[i].nom_p,
                        "motherName": data[i].nom_m,
                        "farmName": data[i].nom_elev,
                        "farmId": data[i].id_elev
                    };
                });
                return options;
            }
        },
        minimumInputLength: 2,
        placeholder: "Rechercher un parent",
        allowClear: true,
        language: "fr"
    });
    
    $('#chooseAnimal').on('select2:select', function(event) {
        $('#father').val(event.params.data.fatherName);
        $('#mother').val(event.params.data.motherName);
        $('#animalID').val(event.params.data.identificationNumber);
        $('#animalName').val(event.params.data.animalName);
        $('#birthDate').val(event.params.data.birthDate);
        $('#deathDate').val(event.params.data.deathDate);
        $('#birthFarm').val(event.params.data.farmName);
        $("#IDanimalChoisi").val(event.params.data.id);
        $('#fatherId').val(event.params.data.fatherId);
        $('#motherId').val(event.params.data.motherId);
        $('#farmId').val(event.params.data.farmId);
        
        if (event.params.data.sex === 1){
            $('#animalMale').prop("checked", true);
        } else if (event.params.data.sex === 3) {
            $('#animalCastre').prop("checked", true);
        } else {
            $('#animalFemale').prop("checked", true);
        }
        
        if (event.params.data.cons) {
            $('#conserv2').prop("checked", true);
        } else {
            $('#conserv1').prop("checked", true);
        }
        
        if (event.params.data.deathDate !== '') {
            $('#animal_dead').prop("checked", true);
            $('#animal_dead').prop("disabled", false);
            $('#deathDate').prop("disabled", false);
        } else {
            $('#animal_dead').prop("checked", false);
            $('#animal_dead').prop("disabled", true);
            $('#deathDate').prop("disabled", true);
        }
    });
    
    $('#chooseAnimal').on('select2:clear', function() {
        $('#father').val('');
        $('#mother').val('');
        $('#animalID').val('');
        $('#animalName').val('');
        $('#birthDate').val('');
        $('#deathDate').val('');
        $('#birthFarm').val('');
        $("#IDanimalChoisi").val('');
        $('#fatherId').val(1);
        $('#motherId').val(2);
        $('#farmId').val(0);
        $(':radio').prop("checked",false);
        $(':checkbox').prop("checked",false);
    });
});