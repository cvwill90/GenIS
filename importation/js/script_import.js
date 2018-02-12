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
            if (document.getElementById('espece').value === "1"){
                $('#race').prop("disabled", true);
                $('#fileSelect').hide();
            } else {
                $('#race').prop("disabled",false);
                $('#fileSelect').show();
            }
            
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
/*
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
    if (event.which!=13 && event.which!=9 && event.which!=16){
        if (document.getElementById('birthFarm').value == ''){
            document.getElementById('farmId').value='1';
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
/*
function triggerAutocompleteMale(event) {
    var Race = $('#race').val();
    var fatherID = $("#fatherID" );
    var target1 = $('#fatherId');
    var target2 = $('#lignee');
    fatherID.autocomplete({
        source: "../../libraries/ajax/suggestAnimal.php?sex=1&race="+Race,
        dataType: "json",
        select: function (event,ui){
            event.preventDefault();
            fatherID.val(ui.item.value);
            target1.val(ui.item.id);         //on injecte bien la valeur de la bdd dans le champ fatherId
            target2.val(ui.item.lignee);
        },
        focus: function (event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which!=13 && event.which!=9 && event.which!=16){
        if (document.getElementById('fatherID').value == ''){
            document.getElementById('fatherId').value='1';
        } else {
            document.getElementById('fatherId').value='';
            document.getElementById('lignee').value='';
        }
    }
}

/**
 * Fonction d'autocomplétion proposant les femelles existantes dans la bdd
 * gère également l'autoremplissage du champ "famille"
 * @param event
 */
/*
function triggerAutocompleteFemale(event) {
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
}

/**
 * Lorsqu'on quitte un champ d'autocomplétion (onblur),
 * @param this_element
 * @param target
 */
/*
function check_if_empty(this_element,target){
    if (document.getElementById(this_element).value == '') {
        document.getElementById(target).value = 1;
    }
}

function checkForm(){
    if (document.getElementById('fatherID').value !='' && document.getElementById('fatherId').value ==''){
        alert('Le nom du père est invalide.');
        return false;
    }
    else if (document.getElementById('motherID').value !='' && document.getElementById('motherId').value ==''){
        alert('Le nom de la mère est invalide.');
        return false;
    }
    else{
        return true;
    }
}*/