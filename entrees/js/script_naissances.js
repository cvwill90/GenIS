/**
 * Created by Christophe_2 on 01/03/2016.
 */

//var specie = document.getElementById('espece');
//var selected = specie.options[specie.selectedIndex].value;
//alert(specie.value);

var unknownFatherId = 1;
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
        pourcentageSangRace: window.unknownParentBloodPercentage
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
    updateFatherInformationInForm: function(){
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
}

function switchFocusToNextFormElement(targetFormElement) {
    document.getElementById(targetFormElement).focus();
}

/**
 * Fonction remplissant la liste de sélection des races en fonction de ce qui est choisi comme espèce
 */

function fillup_race() {
    var str = '';
    var specie = $("#espece").find(":selected").val();
    $.ajax({
        method: 'GET',
        url: '../../libraries/ajax/getRaces.php?espece=' + specie,
        dataType: "json",
        success: function (data, status) {
            $.each(data, function (i, espece) {
                str = str + '<option value="' + espece.value + '">' + espece.label + '</option>';
            });
            $('#race').html(str);
            $('#race').prop("disabled", false);
            $('#fatherID').prop("disabled", false);
            $('#motherID').prop("disabled", false);
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
        select: function (event, ui) {
            event.preventDefault();
            $('#birthFarm').val(ui.item.nom_elevage);
            $('#farmId').val(ui.item.id);
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

function get_pourcentage_sang() {
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

/**
 * Fonction d'autocomplétion proposant les mâles existants dans la bdd
 * gère également l'autoremplissage du champ "lignee"
 * @param event
 */

function triggerAutocompleteMale(event) {
    var race = $('#race').val();
    $("#fatherID").autocomplete({
        source: "../../libraries/ajax/suggestAnimal.php?sex=1&race=" + race,
        dataType: "json",
        select: function (event, selectedOption) {
            event.preventDefault();
            window.family.father = selectedOption.item;
            window.switchFocusToNextFormElement('motherID');
        },
        focus: function (event, focusedOption) {
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which !== 13 && event.which !== 9 && event.which !== 16) {
        var currentFatherIdInput = document.getElementById('fatherID').value;
        window.family.father = {
            id: 1,
            value: currentFatherIdInput,
            ancetre: window.unknownParentAncestor,
            pourcent_sang: window.unknownParentBloodPercentage
        };
    }
}

/**
 * Fonction d'autocomplétion proposant les femelles existantes dans la bdd
 * gère également l'autoremplissage du champ "famille"
 * @param event
 */

function triggerAutocompleteFemale(event) {
    var race = $('#race').val();
    $('#motherID').autocomplete({
        source: "../../libraries/ajax/suggestAnimal.php?sex=2&race=" + race,
        dataType: "json",
        select: function (event, selectedOption) {
            event.preventDefault();
            window.family.mother = selectedOption.item;
            window.switchFocusToNextFormElement('animalID');
        },
        focus: function (event, focusedOption) {
            event.preventDefault();
        },
        minLength: 2
    });
    if (event.which !== 13 && event.which !== 9 && event.which !== 16) {
        if (document.getElementById('motherID').value === '') {
            window.family.resetMotherInformationInForm();
        } else {
            window.family.eraseMotherInformationInForm();
        }
    }
}

/**
 * Lorsqu'on quitte un champ d'autocomplétion (onblur),
 * @param this_element
 * @param target
 */

function checkIfElementIsEmpty(thisElement, targetElement, defaultValue) {
    if (document.getElementById(thisElement).value === '') {
        document.getElementById(targetElement).value = defaultValue;
    }
}

function checkForm() {
    if (document.getElementById('fatherID').value !== '' && document.getElementById('fatherId').value === '') {
        alert('Le nom du père est invalide.');
        return false;
    } else if (document.getElementById('motherID').value !== '' && document.getElementById('motherId').value === '') {
        alert('Le nom de la mère est invalide.');
        return false;
    } else {
        return true;
    }
}

$().ready(function () {
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
    })
});