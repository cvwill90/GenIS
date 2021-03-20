/**
 * Created by Christophe_2 on 01/03/2016.
 */

//var specie = document.getElementById('espece');
//var selected = specie.options[specie.selectedIndex].value;
//alert(specie.value);

/*var unknownFatherId = 1;
var unknownMotherId = 2;
var unknownParentName = "";
var unknownParentAncestor = "";
var unknownParentBloodPercentage = "";
var childDefaultLivreGenealogique = null;
var childDefaultBloodPercentage = null;

var family = {
    fatherInternal: {
        id: window.unknownFatherId,
        name: window.unknownParentName,
        lignee: window.unknownParentAncestor,
        pourcentageSangRace: window.unknownParentBloodPercentage,
        livreGenealogique:window.
    },
    motherInternal: {
        id: window.unknownMotherId,
        name: window.unknownParentName,
        famille: window.unknownParentAncestor,
        pourcentageSangRace: window.unknownParentBloodPercentage
    },
    childGeneticInformationInternal: {
        livreGenealogique: window.childDefaultLivreGenealogique,
        pourcentageSangRace: window.childDefaultBloodPercentage,
    },
    getChildGeneticInformation: function(){
        var childGeneticInformation = {
            livreGenealogique: window.childDefaultLivreGenealogique,
            pourcentageSangRace: window.childDefaultBloodPercentage,
        };
        if (this.fatherInternal.id !== 1 && this.motherInternal.id !== 2) {
            console.log("sending calculation request")
            $.ajax({
                method: 'GET',
                url: '../../libraries/ajax/calculate_genetic_information.php',
                dataType: "json",
                success: function(val, ui){
                    childGeneticInformation.livreGenealogique = "Hello ";
                    childGeneticInformation.pourcentageSangRace = 354;
                },
                error: function(val, ui){}
            });
            console.log("Child genetic information updated -- livre gene: " + childGeneticInformation.livreGenealogique + " ; pourcentage sang: " + childGeneticInformation.pourcentageSangRace);
        } else {
            console.log("no calculation needed");
        }
        return childGeneticInformation;
    },
    set father(father) {
        this.fatherInternal.id = father.id;
        this.fatherInternal.name = father.value;
        this.fatherInternal.lignee = father.ancetre;
        this.fatherInternal.pourcentageSangRace = father.pourcent_sang;
        this.updateFatherInformationInForm();
    },
    set mother(mother) {
        this.motherInternal.id = mother.id;
        this.motherInternal.name = mother.value;
        this.motherInternal.famille = mother.ancetre;
        this.motherInternal.pourcentageSangRace = mother.pourcent_sang;
        this.updateMotherInformationInForm();
    },
    /*updateFatherInformationInForm: function(){
        if (this.fatherInternal.id !== 1) {
            $("#fatherID").val(this.fatherInternal.name);
            $('#fatherId').val(this.fatherInternal.id);
        } else {
            $('#fatherId').val("");
        }
        $('#lignee').val(this.fatherInternal.lignee);
        $('#pourcentage_sang_pere').val(this.fatherInternal.pourcentageSangRace);
        this.updateChildGeneticInformationInForm(this.getChildGeneticInformation());
    },
    updateMotherInformationInForm: function(){
        $("#motherID").val(this.motherInternal.name);
        $('#motherId').val(this.motherInternal.id);
        $('#famille').val(this.motherInternal.famille);
        $('#pourcentage_sang_mere').val(this.motherInternal.pourcentageSangRace);
        this.updateChildGeneticInformationInForm(this.getChildGeneticInformation());
    },
    eraseFatherInformationInForm: function(){
        document.getElementById('fatherId').value = "";
        document.getElementById('lignee').value = window.unknownParentAncestor;
        document.getElementById('pourcentage_sang_pere').value = window.unknownParentBloodPercentage;
        document.getElementById('pourcentage_sang_animal').value = "";
        this.updateChildGeneticInformationInForm(this.getChildGeneticInformation());
    },
    resetFatherInformationInForm: function(){
        document.getElementById('fatherId').value = window.unknownFatherId;
        this.updateChildGeneticInformationInForm(this.getChildGeneticInformation());
    },
    eraseMotherInformationInForm: function(){
        document.getElementById('motherId').value = "";
        document.getElementById('famille').value = window.unknownParentAncestor;
        document.getElementById('pourcentage_sang_mere').value = window.unknownParentBloodPercentage;
        document.getElementById('pourcentage_sang_animal').value = "";
        this.updateChildGeneticInformationInForm(this.getChildGeneticInformation());
    },
    resetMotherInformationInForm: function(){
        document.getElementById('motherId').value = window.unknownMotherId;
        this.updateChildGeneticInformationInForm(this.getChildGeneticInformation());
    },
    updateChildGeneticInformationInForm: function(childGeneticInformation){
        console.log("updating child genetical information");
        //$("#livre_gene").val(childGeneticInformation.livreGenealogique);
        $("#pourcentage_sang_animal").val(childGeneticInformation.pourcentageSangRace);
    },
    get getFather(){
        return this.fatherInternal;
    },
    get getMother(){
        return this.motherInternal;
    }
}*/

/*function switchFocusToNextFormElement(targetFormElement) {
    document.getElementById(targetFormElement).focus();
}*/

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

/**
 * Fonction calculant à la volée le pourcentage de race du nouvel animal en fonction
 * du pourcentage de race des deux parents
 * @param {type} event
 * @returns {undefined}
 */

function getNewAnimalGeneticInformation() {
    var info = {fatherId: 1, motherId: 2};
    return $.get('../../libraries/ajax/calculate_genetic_information.php', info);
    /*var father_id = document.getElementById('fatherId').value;
    var mother_id = document.getElementById('motherId').value;
    if (father_id !== null && father_id != 1 && father_id != "" && mother_id !== null && mother_id != 2 && mother_id != "") {
        var pourcentage_sang_pere = parseFloat(document.getElementById('pourcentage_sang_pere').value);
        var pourcentage_sang_mere = parseFloat(document.getElementById('pourcentage_sang_mere').value);
        var pourcentage_sang_animal = (pourcentage_sang_pere + pourcentage_sang_mere) / 2;

        $("#pourcentage_sang_animal").val(pourcentage_sang_animal);
    }*/
    
    // Request pourcentage de sang over ajax to backend
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
        /*var childGeneticInformation = window.getNewAnimalGeneticInformation().done(function (data) {return data});
        });*/
        window.getNewAnimalGeneticInformation().done(function (data) {
            var parsedGeneticInformation = JSON.parse(data);
            $('#pourcentage_sang_animal').val(parsedGeneticInformation.pourcentage_sang_race);
        });
        //$('#pourcentage_sang_animal').val(childGeneticInformation.pourcentage_sang_race);
    });
    
    $('.parent').on('select2:clear', function(event) {
        var parentGender = event.target.id;
        if (parentGender === 'fatherId') {
            $('#lignee').val("");
        } else if (parentGender === 'motherId') {
            $('#famille').val("");
        }
        $('#pourcentage_sang_animal').val("");
    });
});