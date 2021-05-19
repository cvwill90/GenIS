/**
 * Created by Christophe_2 on 01/03/2016.
 */

/**
 * Fonction remplissant la liste de sélection des races en fonction de ce qui est choisi comme espèce
 */

function fillup_race() {
    var str = '';
    var espece = $("#espece").find(":selected").val();
    $.ajax({
        method: 'GET',
        url: '../../libraries/ajax/getRaces.php?espece=' + espece,
        dataType: "json",
        success: function (data) {
            $.each(data, function (i, espece) {
                str = str + '<option value="' + espece.value + '">' + espece.label + '</option>';
            });
            $('#race').html(str);
            $('#race').prop("disabled",false);
            $('#fatherId').prop("disabled",false);
            $('#motherId').prop("disabled",false);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('Le serveur a rencontré l\'erreur suivante : ' + xhr.status + " " + thrownError);
        },
        complete: function (data, status) {
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
        select: function (event, farm) {
            event.preventDefault();
            $('#birthFarm').val(farm.item.nom_elevage);
            $('#farmId').val(farm.item.id);
        },
        focus: function (event, ui) {
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which !== 13 && event.which !== 9 && event.which !== 16) {
        if (document.getElementById('birthFarm').value === '') {
            document.getElementById('farmId').value = '1';
        } else {
            document.getElementById('farmId').value = '';
        }
    }
}

$(document).ready(function() {
    $('#naissance').validate({
        rules: {
            animalID: {
                required: true,
                remote: {
                    url: "../../libraries/ajax/check_id_number.php",
                    method: "POST"
                }
            }
        }
    });
    
    $('.parent').select2({
        ajax: {
            url: "../../libraries/ajax/suggestAnimal.php",
            data: function (params) {
                var gender = (this[0].id === "fatherId" ? 1 : 2);
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
            if (parentGender === 'fatherId') {
                $('#lignee').val(animalGeneticInformation.lignee);
                $('#pourcentage_sang_pere').val(animalGeneticInformation.pourcentage_sang_race);
            } else if (parentGender === 'motherId') {
                $('#famille').val(animalGeneticInformation.famille);
                $('#pourcentage_sang_mere').val(animalGeneticInformation.pourcentage_sang_race);
            }
            
            if ($('#fatherId').find(':selected')[0] !== undefined && $('#motherId').find(':selected')[0] !== undefined) {
                var pourcentageSangPere = parseFloat($('#pourcentage_sang_pere').val());
                var pourcentageSangMere = parseFloat($('#pourcentage_sang_mere').val());
                $('#pourcentage_sang_animal').prop('disabled', true);
                $('#pourcentage_sang_animal').val((pourcentageSangPere + pourcentageSangMere)/ 2);
            }
            
        });
    });
    
    $('.parent').on('select2:clear', function(event) {
        var parentGender = event.target.id;
        if (parentGender === 'fatherId') {
            $('#lignee').val("");
            $('#pourcentage_sang_pere').val(null);
        } else if (parentGender === 'motherId') {
            $('#famille').val("");
            $('#pourcentage_sang_mere').val(null);
        }
        $('#pourcentage_sang_animal').val("");
        $('#pourcentage_sang_animal').prop('disabled', false);
    });
});
