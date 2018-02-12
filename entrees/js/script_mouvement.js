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
            $('#animalID').prop("disabled",false);
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
 */

function autoCompleteDestinationFarm(event) {
    $('#destinationFarm').autocomplete({
        source: "../../libraries/ajax/suggestFarm.php?",
        dataType: "json",
        select: function(event,ui){
            event.preventDefault();
            $('#destinationFarm').val(ui.item.nom_elevage);
            $('#dFarmId').val(ui.item.id);
        },
        focus: function(event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which!=13 && event.which!=9 && event.which!=16){
        document.getElementById('dFarmId').value='';
    }
}

/**
 * Fonction d'autocomplétion proposant les animaux existants dans la bdd
 * @param event
 */

function triggerAutocompleteAnimal(event) {
    var Race = $('#race').val();
    var animalID = $("#animalID" );
    var target1 = $('#animalId');
    var target2 = $('#origineFarm');
    var target3 = $('#oFarmId');
    animalID.autocomplete({
        source: "../../libraries/ajax/suggestAnimalAndFarm.php?ajaxType=2&sex=0&race="+Race,
        dataType: "json",
        select: function (event,ui){
            event.preventDefault();
            animalID.val(ui.item.value);
            target1.val(ui.item.id);         //on injecte bien la valeur de la bdd dans le champ
            target2.val(ui.item.nom_elevage);
            target3.val(ui.item.id_elevage)
        },
        focus: function (event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which!=13 && event.which!=9 && event.which!=16){
        document.getElementById('animalId').value='';
        document.getElementById('oFarmId').value='';
        document.getElementById('origineFarm').value=''
    }
}

/**
 * Fonction d'autocomplétion proposant les femelles existantes dans la bdd
 * gère également l'autoremplissage du champ "famille"
 * @param event
 */

/*function triggerAutocompleteFemale(event) {
    var Race=$('#race').val();
    var motherID = $('#motherID');
    var target1 = $('#motherId');
    var target2 = $('#famille');
    motherID.autocomplete({
        source: "../../libraries/ajax/suggestAnimal.php?sex=2&race="+Race,
        dataType: "json",
        select: function (event,ui){
            event.preventDefault();
            motherID.val(ui.item.value);
            target1.val(ui.item.id);
            target2.val(ui.item.famille);
        },
        focus: function (event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which!=13 && event.which!=9 && event.which!=16){
       if (document.getElementById('motherID').value == ''){
           document.getElementById('motherId').value='1';
       } else {
           document.getElementById('motherId').value='';
           document.getElementById('famille').value='';
       }
    }
}*/

/**
 * Lorsqu'on quitte un champ d'autocomplétion (onblur),
 * @param this_element
 * @param target
 */

function check_if_empty(this_element,target){
    if (document.getElementById(this_element).value == '')
        document.getElementById(target).value=1
}