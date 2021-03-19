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
            $('#fatherId').prop("disabled",false);
            $('#motherId').prop("disabled",false);
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
            document.getElementById('farmId').value='1';
        } else {
            document.getElementById('farmId').value='';
        }
    }
}

$(document).ready(function() {
    $('#naissance').validate({
        rules: {
            animalID: {
                required: true,
                remote: {
                    url: "../libraries/ajax/check_id_number.php",
                    method: "POST"
                }
            }
        }
    });
    
    $('.parent').select2({
        ajax: {
            url: function(){
                var race = 6;
                var gender = 1;
                return '../../libraries/ajax/suggestAnimal.php?sex=' + gender + '&race=' + race;
            },
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
            // Transforms the top-level key of the response object from 'items' to 'results'
                var options = {
                    "results": []
                }
                $.each(data, function (i) {
                    options.results[i] = {
                        "id": data[i].id,
                        "text": data[i].label,
                        "ancetre": data[i].ancetre
                    }
                });
                return options;
            }
        },
        minimumInputLength: 2,
        placeholder: "Rechercher un parent",
        templateSelection: function (data) {
            $(data.element).attr('data-ancetre', data.ancetre);
            return data.text;
        },
        allowClear: true,
        language: "fr"
    });
    
    $('.parent').on('select2:select', function(event) {
        var parentGender = event.target.id;
        if (parentGender === 'fatherId') {
            $('#lignee').val($('#fatherId').find(':selected').data('ancetre'));
        } else if (parentGender === 'motherId') {
            $('#famille').val($('#motherId').find(':selected').data('ancetre'));
        }
    });
    
    $('.parent').on('select2:clear', function(event) {
        var parentGender = event.target.id;
        if (parentGender === 'fatherId') {
            $('#lignee').val("");
        } else if (parentGender === 'motherId') {
            $('#famille').val("");
        }
    });
});