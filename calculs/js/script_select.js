/**
 * Created by Christophe_2 on 01/03/2016.
 */

//var specie = document.getElementById('espece');
//var selected = specie.options[specie.selectedIndex].value;
//alert(specie.value);


/**********************/
/* selectionPedig.php */
/**********************/


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
            $('#fatherID').prop("disabled",false);
            $('#motherID').prop("disabled",false);
            $('#submitSelection').prop("disabled",false);
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
/*
function triggerAutoCompleteFarm(event) {
    $('#elevage').autocomplete({
        source: "../../libraries/ajax/suggestFarm.php?",
        dataType: "json",
        select: function(event,ui){
            event.preventDefault();
            $('#elevage').val(ui.item.nom_elevage);
            $('#hidElevage').val(ui.item.id);
        },
        focus: function(event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which!=13 && event.which!=9 && event.which!=16){
        if (document.getElementById('elevage').value == ''){
            document.getElementById('hidElevage').value='1';
        } else {
            document.getElementById('hidElevage').value='';
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


//lorsqu'on valide le formulaire on envoit une requete ajax qui va sélectionner les données demandées par le formulaire

function validSelect(){
    var formContent = $('#animalSelection').serialize();
    $.ajax({
        method: 'GET',
        url: '../../libraries/ajax/select.php',
        data: formContent,
        dataType: 'json',
        success : function(data){
            if (data.status == 'ok'){
                window.location.href = 'pedUtil1.php?nameReq='+ data.ped +'&nameRef='+ data.ref +'&race_lib='+ data.race + '&race=' + $('#race').val();
            } else {
                alert('Erreur: ' + data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('Le serveur a rencontré l\'erreur suivante : '+xhr.status + " "+ thrownError);
        }
    })
}


/*****************/
/* pedUtil1.php  */
/*****************/

function launchPedutil(race){
    var formContent = $('#launchPedig').serialize();
    document.getElementById('myModal').style.display = 'block';
    document.getElementById('fadeModal').style.display='block';
    $.ajax({
        method: 'GET',
        url: '../../libraries/ajax/ajaxPedUtil.php?race='+ race,
        data: formContent,
        dataType: 'json',
        success : function(data, status){
            if (data.status=='ok') {
                window.location.replace('resultPedUtil.php');
            } else {
                alert('Une erreur s\'est produite lors de l\'exécution de ped_util.exe. L\'erreur suivante a été renvoyée par le programme :\n' + data.error_message);
                document.getElementById('myModal').style.display = 'none';
                document.getElementById('fadeModal').style.display='none';
            }
        },
        error : function(xhr, status, thrownError){
            alert('Une erreur a sans doute doute eu lieu lors de l\'exécution de ped_util.exe... Erreur renvoyée :\n'+ thrownError);
            document.getElementById('myModal').style.display = 'none';
            document.getElementById('fadeModal').style.display='none';
        }
    })
}