/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getAnimalGeneticInformation(animalId) {
    return $.ajax({
        method: 'GET',
        url: 'http://genisquery.cra/animalInformation/' + animalId,
        data: {
            include_genetic_information: 1
        },
        dataType: "json",
        error: function (xhr, ajaxOptions, thrownError) {
            alert('Le serveur a rencontré l\'erreur suivante : ' + xhr.status + " " + thrownError);
        }
    });
}

function getAnimalAnimalAndParentsInformation(animalId) {
    return $.ajax({
        method: 'GET',
        url: 'http://genisquery.cra/animalInformation/' + animalId,
        data: {
            include_genetic_information: 1,
            include_parents_information: 1
        },
        dataType: "json",
        error: function (xhr, ajaxOptions, thrownError) {
            alert('Le serveur a rencontré l\'erreur suivante : ' + xhr.status + " " + thrownError);
        }
    });
}
