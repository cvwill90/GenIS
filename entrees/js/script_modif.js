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
    if ($('#pourcentage_sang_animal').val() !== '' && $('#farmId').val() !== '') {
        $.ajax({
            method: "POST",
            dataType: "json",
            data: formContent,
            url: "../libraries/ajax/ajaxModifierAnimal.php",
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
            url: "../../libraries/ajax/suggestAnimal.php",
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
                        "text": data[i].label
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
        
        var animalAndParentsInformationRequest = getAnimalAnimalAndParentsInformation(event.params.data.id);
        
        animalAndParentsInformationRequest.done(function(animalAndParentsInformation) {
            
            if (animalAndParentsInformation.father_information !== null) {
                var father_data = {
                    id: animalAndParentsInformation.id_pere,
                    text: animalAndParentsInformation.father_information.no_identification + ' - ' + animalAndParentsInformation.nom_pere
                };

                var newFatherOption = new Option(father_data.text, father_data.id, false, false);
                $('#father').append(newFatherOption).trigger('change');
                $('#father').val(animalAndParentsInformation.id_pere);
                $('#lignee').val(animalAndParentsInformation.lignee);
                $('#pourcentage_sang_pere').val(animalAndParentsInformation.father_information.pourcentage_sang_race);
            }
            
            if (animalAndParentsInformation.mother_information !== null) {
                var mother_data = {
                    id: animalAndParentsInformation.id_mere,
                    text: animalAndParentsInformation.mother_information.no_identification + ' - ' + animalAndParentsInformation.nom_mere
                };

                var newMotherOption = new Option(mother_data.text, mother_data.id, false, false);
                $('#mother').append(newMotherOption).trigger('change');
                $('#mother').val(animalAndParentsInformation.id_mere);
                $('#famille').val(animalAndParentsInformation.famille);
                $('#pourcentage_sang_mere').val(animalAndParentsInformation.mother_information.pourcentage_sang_race);
            }
            
            
            $('#animalID').val(animalAndParentsInformation.no_identification);
            $('#animalName').val(animalAndParentsInformation.nom_animal);
            $('#birthDate').val(animalAndParentsInformation.date_naiss);
            if (animalAndParentsInformation.father_information !== null && animalAndParentsInformation.mother_information !== null) {
                $('#pourcentage_sang_animal').val(animalAndParentsInformation.pourcentage_sang_race);
                $('#pourcentage_sang_animal').prop('disabled', true);
            }
            $('#deathDate').val(animalAndParentsInformation.date_mort);
            $('#birthFarm').val(animalAndParentsInformation.nom_elevage_naiss);
            $("#IDanimalChoisi").val(animalAndParentsInformation.id_animal);
            $('#fatherId').val(animalAndParentsInformation.id_pere);
            $('#motherId').val(animalAndParentsInformation.id_mere);
            $('#farmId').val(animalAndParentsInformation.id_elevage_naiss);
            
            if (animalAndParentsInformation.sexe === 1){
                $('#animalMale').prop("checked", true);
            } else if (animalAndParentsInformation.sex === 3) {
                $('#animalCastre').prop("checked", true);
            } else {
                $('#animalFemale').prop("checked", true);
            }
            
            if (animalAndParentsInformation.conservatoire) {
                $('#conserv2').prop("checked", true);
            } else {
                $('#conserv1').prop("checked", true);
            }

            if (animalAndParentsInformation.date_mort !== null) {
                $('#animal_dead').prop("checked", true);
                $('#animal_dead').prop("disabled", false);
                $('#deathDate').prop("disabled", false);
            } else {
                $('#animal_dead').prop("checked", false);
                $('#animal_dead').prop("disabled", true);
                $('#deathDate').prop("disabled", true);
            }
        });
    });
    
    $('#chooseAnimal').on('select2:clear', function() {
        $('#father').val(null).trigger('change');
        $('#lignee').val('');
        $('#pourcentage_sang_pere').val('');
        $('#mother').val(null).trigger('change');
        $('#famille').val('');
        $('#pourcentage_sang_mere').val('');
        $('#animalID').val('');
        $('#animalName').val('');
        $('#birthDate').val('');
        $('#deathDate').val('');
        $('#birthFarm').val('');
        $("#IDanimalChoisi").val('');
        $('#farmId').val(0);
        $(':radio').prop("checked",false);
        $(':checkbox').prop("checked",false);
    });
    
    $('.parent').select2({
        ajax: {
            url: "../../libraries/ajax/suggestAnimal.php",
            data: function (params) {
                var gender = (this[0].id === "father" ? 1 : 2);
                var query = {
                    term: params.term,
                    race: $('#race').val(),
                    sex: gender,
                };
                return query;
            },
            dataType: 'json',
            processResults: function (data) {
                var options = {
                    "results": []
                }
                $.each(data, function (i) {
                    options.results[i] = {
                        "id": data[i].id,
                        "text": data[i].label
                    }
                });
                return options;
            }
        },
        minimumInputLength: 2,
        placeholder: "Rechercher un parent",
        allowClear: true,
        language: "fr"
    });
    
    $('.parent').on('select2:select', function(event) {
        var animalGeneticInformationRequest = getAnimalGeneticInformation(event.params.data.id);
        animalGeneticInformationRequest.done(function(animalGeneticInformation) {
            var parentGender = event.target.id;
            if (parentGender === 'father') {
                $('#lignee').val(animalGeneticInformation.lignee);
                $('#pourcentage_sang_pere').val(animalGeneticInformation.pourcentage_sang_race);
            } else if (parentGender === 'mother') {
                $('#famille').val(animalGeneticInformation.famille);
                $('#pourcentage_sang_mere').val(animalGeneticInformation.pourcentage_sang_race);
            }
            
            if ($('#father').find(':selected')[0] !== undefined && $('#mother').find(':selected')[0] !== undefined) {
                var pourcentageSangPere = parseFloat($('#pourcentage_sang_pere').val());
                var pourcentageSangMere = parseFloat($('#pourcentage_sang_mere').val());
                $('#pourcentage_sang_animal').prop('disabled', true);
                $('#pourcentage_sang_animal').val((pourcentageSangPere + pourcentageSangMere)/ 2);
            }
            
        });
    });
    
    $('.parent').on('select2:clear', function(event) {
        var parentGender = event.target.id;
        if (parentGender === 'father') {
            $('#lignee').val("");
            $('#pourcentage_sang_pere').val(null);
        } else if (parentGender === 'mother') {
            $('#famille').val("");
            $('#pourcentage_sang_mere').val(null);
        }
        $('#pourcentage_sang_animal').val("");
        $('#pourcentage_sang_animal').prop('disabled', false);
    });
});