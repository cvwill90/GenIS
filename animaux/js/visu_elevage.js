/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var farm_id;
var animal_information;

var animal_tags = {
        'name': 'Nom',
        'id_nb': 'N° d\'identification',
        'race': 'Race',
        'sex': 'Sexe',
        'birth': 'Date de naissance',
        'nom_pere': 'Nom du prère',
        'no_pere': 'N° d\'identitication père',
        'nom_mere': 'Nom de la mère',
        'no_mere': 'N° d\'identification mère'
    }

function autocomplete_farm() {
    var nomElevage = $('#farm');
    nomElevage.autocomplete({
        source: "../../libraries/ajax/suggestFarm.php?",
        dataType: "json",
        select: function(event,ui){
            event.preventDefault();
            nomElevage.val(ui.item.nom_elevage);
            farm_id = ui.item.id;
            fillup_races();
        },
        minLength: 2
    });
    nomElevage.on('input',function(){
        farm_id = undefined;
        $('#race').html('');
        $('#race').prop("disabled",true);
    });
}

async function fillup_races() {
    var str = '';
    await $.ajax({
        method: 'GET',
        url: '../../libraries/ajax/ajax_get_farm_info.php?type=races&id_farm='+farm_id,
        dataType: "json",
        success : function(data, status){
            var k = 0;
            $.each(data, function (i, espece) {
                $.each(espece.races, function (j, race){
                    str = str + '<option value="'+ race.id_race +'">'+ espece.lib_espece + ' - ' + race.lib_race +'</option>';
                    k++;
                });
            });
            if (k > 1) {
                options = '<option value="0">Toutes</option>' + str;
            } else {
                options = str;
            }
            $('#race').html(options);
            $('#race').prop("disabled",false);
        },
        error: function (xhr, thrownError) {
            alert('Le serveur a rencontré l\'erreur suivante : '+xhr.status + " "+ thrownError);
        }
    });
}

function auto_fill_maxdate() {
    initial_maxDate_value = $('#maxDate').val();
    if (initial_maxDate_value === '') {
        $('#maxDate').val($('#minDate').val());
    }
}

function get_animals() {
    $.ajax({
        method: "POST",
        url: "../../libraries/ajax/get_animals_on_farm.php",
        data: $('#param_form').serialize() + "&farm_id=" + farm_id,
        dataType: 'json',
        success: function (data) {
            if (!data.length) {
                alert('Aucun animal ne satisfait les critères de recherche.');
            } else {
                html_table = arrange_animals_to_table(data);
                document.getElementsByTagName('tbody')[1].innerHTML = html_table;
                animal_information = data;
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Une erreur est survenue lors de la requête.');
        }
    })
}

$().ready(function() {
    $('#param_form').validate({
        rules: {
            farm: {
                required: true,
                valid_farm: function () {
                    if (typeof farm_id === 'undefined') {
                        return 'undef';
                    } else {
                        return 'def';
                    }
                }
            },
            race: "required",
            minDate: "required",
            maxDate: {
                required: true,
                valid_maxdate: function () {
                    min_date = $('#minDate')[0].value;
                    return min_date;
                },
            },
        }
    })
});

function arrange_animals_to_table(animals) {
    animal_tags_keys = Object.keys(animal_tags);
    html_table = '';
    $.each(animals, function (j) {
        line = '';
        for (i=0; i<animal_tags_keys.length; i++) {
            animal = animals[j];
            animal_data = animal[animal_tags_keys[i]];
            if (animal_tags_keys[i] === 'sex') {
                if (parseInt(animal_data) === 3) {
                    animal_data = 'MC';
                } else if (parseInt(animal_data) === 2) {
                    animal_data = 'F';
                } else {
                    animal_data = 'M'
                }
            }
            td = i == 0 ? '<td class="sorting_1">' : '<td>';
            line += td + animal_data + '</td>';
        }
        parity = j%2 == 0 ? 'odd' : 'even';
        html_table += '<tr role="row" class="' + parity + '">' + line + '</tr>';
    })
    return html_table;
}

function export_csv() {
    if (animal_information === undefined) {
        alert('Aucune recherche n\'a été effectuée.');
    } else if (animal_information.length === 0) {
        alert('La liste d\'animaux est vide.');
    } else {
        animal_tags_keys = Object.keys(animal_tags);
        csv_info = new Array();
        var num_tags = animal_tags_keys.length
        for (var k=0; k<animal_information.length; k++) {
            line = '';
            for (var i=0; i<num_tags; i++){
                line += animal_information[k][animal_tags_keys[i]] + ';';
            }
            csv_info.push(line);
        }
        csv_head = '';
        for (var j=0; j<num_tags; j++) {
            csv_head += animal_tags[animal_tags_keys[j]] + ';';
        }
        download("historique_elevage.csv",csv_head + '\r\n' + csv_info.join('\r\n'));
    }
}

function download(filename, text) {
  var element = document.createElement('a');
  element.setAttribute('href', 'data:text/csv;charset=utf-8,%EF%BB%BF' + encodeURIComponent(text));
  element.setAttribute('download', filename);

  element.style.display = 'none';
  document.body.appendChild(element);

  element.click();

  document.body.removeChild(element);
}