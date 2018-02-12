/**
 * Created by Christophe_2 on 01/03/2016.
 */

//var specie = document.getElementById('espece');
//var selected = specie.options[specie.selectedIndex].value;
//alert(specie.value);


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

function triggerAutoCompleteFarm(event) {
    $('#proprietaireFutur').autocomplete({
        source: "../../libraries/ajax/suggestFarm.php?",
        dataType: "json",
        select: function(event,ui){
            event.preventDefault();
            $('#proprietaireFutur').val(ui.item.nom_elevage);
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
    var animalID = $("#animalID");
    var target1 = $('#animalId');
    var target2 = $('#proprietaireActu');
    animalID.autocomplete({
        source: "../../libraries/ajax/suggestAnimalAndFarm.php?ajaxType=3&sex=0&race="+Race,
        dataType: "json",
        select: function (event,ui){
            event.preventDefault();
            animalID.val(ui.item.value);
            target1.val(ui.item.id);         //on injecte bien la valeur de la bdd dans le champ fatherId
            target2.val(ui.item.nom_elevage);
        },
        focus: function (event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which!=13 && event.which!=9 && event.which!=16){
        if (document.getElementById('animalID').value == ''){
            document.getElementById('animalId').value = '1';
        } else {
            document.getElementById('animalId').value='';
        }
        document.getElementById('proprietaireActu').value = '';
    }
}

/**
 * Lorsqu'on quitte un champ d'autocomplétion (onblur),
 * @param this_element
 * @param target
 */

function check_if_empty(this_element,target){
    if (document.getElementById(this_element).value == '')
        document.getElementById(target).value=1
}