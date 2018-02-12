/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var animal_id;

$(document).ready(function () {
    animal = $('#animal');
    selected_animal = $('#selected_animal');
    animal.autocomplete({
        source: "../../libraries/ajax/suggestAnimal.php",
        dataType: "json",
        select: function (event,ui){
            animal_information = ui.item;
            event.preventDefault();
            animal.val(ui.item.value);
            animal_id = parseInt(ui.item.id);
            selected_animal.html(ui.item.label);
            animal.blur();
        },
        focus: function (event){
            event.preventDefault();
        },
        minLength: 2
    });
    
    animal.keydown(function () {
        if (![13,9].includes(event.which)) {
            animal_id = undefined;
            selected_animal.html('');
        };
    });
    
    $('#del_animal').click(function () {
        if (animal_id != undefined) {
            del_confirm = confirm('Cette action supprimera définitivement l\'animal "' + selected_animal.html() + '", ainsi que toutes les données qui le concernent, de la base de données. S\'il est parent d\'autres animaux, ceux-ci auront désormais un parent inconnu.\n\nEtes-vous sûr de vouloir continuer ?');
            if (del_confirm) {
                delete_animal(animal_id);
            }
        } else {
            alert('Aucun animal n\'a été sélectionné.');
        }
    });
    
})

function delete_animal(id_animal) {
    $.ajax({
        method: "POST",
        data: "id_animal=" + id_animal,
        url: "../../libraries/ajax/delete_animal.php",
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                alert(data.error_msg);
            } else {
                alert('L\'animal ainsi que toutes ses données ont bien été supprimés de la base de données.');
                location.reload();
            }
        },
        error: function () {
            alert('Une erreur est survenue lors de la suppression de l\'animal');
        }
    });
}