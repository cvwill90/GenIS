/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var animal_information;

var interest_info = {
        'nom_animal': 'Nom',
        'no_identification': 'N° d\'identification',
        'lib_race': 'Race',
        'sexe': 'Sexe',
        'date_naiss': 'Date de naissance',
        'coeff_consang': 'Coefficient de consanguinité',
        'lignee': 'Lignée',
        'nom_pere': 'Nom du prère',
        'no_identification_pere': 'N° d\'identitication père',
        'famille': 'Famille',
        'nom_mere': 'Nom de la mère',
        'no_identification_mere': 'N° d\'identification mère',
        'livre_gene': 'Livre généalogique'
    }
    
    var interest_story = {
        'nom_elevage': 'Nom élevage',
        'date_entree': 'Date d\'entrée',
        'date_sortie': 'Date de sortie',
        'lib_type': 'Remarque'
    }

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
            $('#choose_animal').prop("disabled",false);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('Le serveur a rencontré l\'erreur suivante : '+xhr.status + " "+ thrownError);
        },
        complete : function(data, status){
        }
    });
}

function autocomplete_animal(event) {
    var race = $('#race').val();
    var animal = $('#choose_animal');
    animal.autocomplete({
        source: "../../libraries/ajax/get_animal_info_story.php?type=1&race="+race,
        dataType: "json",
        select: function (event,ui){
            animal_information = ui.item;
            event.preventDefault();
            animal.val(ui.item.value);
            
            var data_keys = Object.keys(ui.item.data);
            for (var i = 0, len = data_keys.length; i < len; i++) {
                var data_value = ui.item.data[data_keys[i]];
                if (data_keys[i] === 'sexe') {
                    data_value = replace_val(String(data_value), 'sexe');
                }
                document.getElementById(data_keys[i]).innerHTML = data_value;
            }
            var story_keys = Object.keys(ui.item.story);
            var story_rows = '';
            for (var i = 0, len = story_keys.length; i < len; i++){
                var story_value = ui.item.story[i];
                
                var nom_elevage = replace_val(story_value['nom_elevage'], 'location');
                var date_entree = replace_val(story_value['date_entree'], 'date');
                var date_sortie = replace_val(story_value['date_sortie'], 'date');
                var type_periode = replace_val(story_value['lib_type'], 'periode');
                var id_elevage = replace_val(story_value['id_elevage'], 'id_location');
                
                var link_nom_elevage = '<a style="color: #666 !important" href="visuElevage.php?id_elevage=' + id_elevage + '&nom_elevage=' + nom_elevage + '">' + nom_elevage + '</a>';
                
                if (story_value['lib_type'] === 'naissance'){
                    document.getElementById('lieu_naiss').innerHTML = nom_elevage;
                }
                
                if (type_periode !== 'NA'){
                    story_rows += '<tr><td>' + link_nom_elevage + '</td><td>' + date_entree + '</td><td>' + date_sortie + '</td><td>'  + type_periode + '</td></tr>';
                }
            }
            document.getElementById('story_table_body').innerHTML = story_rows;
        },
        focus: function (event,ui){
            event.preventDefault();
        },
        minLength: 2
    });
    
    var info = document.getElementsByName('table_value');
    if (event.which!==13 && event.which!==9 && event.which!==16){
        event.preventDefault();
        for (var j=0, len=info.length; j<len; j++){
            document.getElementById(info[j].id).innerHTML = '';
            $('#nom_animal').html('');
        }
        document.getElementById('story_table_body').innerHTML = '';
    }
}

function print_info(){
    html2canvas($('#print_div'), {
            background: '#ffffff',
            onrendered: function(canvas) {         
                var imgData = canvas.toDataURL(
                    'image/png', 1.0);              
                var doc = new jsPDF('l', 'mm');
                doc.addImage(imgData,'png', 1, 1);
                doc.autoPrint();
                var imguri = doc.output('dataurlstring');
                $.ajax({
                    method: "POST",
                    url: "../../libraries/ajax/save_info.php",
                    contentType: 'application/upload',
                    data: imguri,
                    dataType: 'html',
                    success: function (data, textStatus, jqXHR) {
                        window.open("animal_info.pdf","_blank");
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('Une erreur est survenue lors de la création du fichier PDF...');
                    }
                });
            }
        });
}

function export_csv(){
    var animal_info = animal_information['data'];
    var animal_info_keys = Object.keys(interest_info);
    var story_info = animal_information['story'];
    var story_info_keys = Object.keys(interest_story);
    
    var csv_info = new Array();
    
    for (var i=0, len=animal_info_keys.length; i<len; i++){
        if (animal_info_keys[i] === 'sexe'){
            sexe = replace_val(animal_info[animal_info_keys[i]], animal_info_keys[i]);
            csv_info.push(interest_info[animal_info_keys[i]] + ';' + sexe);
        } else {
            csv_info.push(interest_info[animal_info_keys[i]] + ';' + animal_info[animal_info_keys[i]]);
        }
    }
    
    var story_head = '';
    var story_body = '';
    var csv_story = '';
    for (var i=0, len=story_info_keys.length; i<len; i++){
        story_head += interest_story[story_info_keys[i]] + ';';
    }
    story_head = story_head.substring(0, story_head.length-1) + '\r\n';
    
    for (var i=0, len1=story_info.length; i<len1; i++){
        var story_body_row = '';
        for (var j=0, len2=story_info_keys.length; j<len2; j++){
            var story_element = '';
            switch (story_info_keys[j]){
                case 'date_entree':
                case 'date_sortie':
                    story_element = replace_val(story_info[i][story_info_keys[j]], 'date');
                    break;
                case 'nom_elevage':
                    story_element = replace_val(story_info[i][story_info_keys[j]], 'location');
                    break;
                case  'lib_type':
                    story_element = replace_val(story_info[i][story_info_keys[j]], 'periode');
                    break;
            }
            
            story_body_row += story_element + ';';
        }
        story_body_row = story_body_row.substring(0, story_body_row.length-1);
        story_body += story_body_row + '\r\n';
    }
    
    csv_story = story_head + story_body;
    //console.log(csv_info.join('\r\n') + '\r\n\r\n' + csv_story);
    
    download(animal_info['nom_animal'] + ".csv",csv_info.join('\r\n') + '\r\n\r\n' + csv_story);
}

function replace_val(info, type){
    var replaced_val = '';
    if (String(info) === 'null' && type === 'date'){
        replaced_val = '';
    } else if (String(info) === 'null' && type === 'location') {
        replaced_val = 'Elevage Inconnu';
    } else if (type === 'periode') {
        if (String(info) === 'naissance'){
            replaced_val = 'Naissance';
        } else if (String(info) === 'sejour') {
            replaced_val = 'Séjour';
        } else if (String(info) === 'mort') {
            replaced_val = 'Mort';
        } else {
            replaced_val = 'NA';
        }
    } else if (type === 'id_location' && String(info) === 'null') {
        replaced_val = '0';
    } else if (type === 'sexe'){
        if (info === '1') {
            replaced_val = 'Mâle';
        } else if (info === '2') {
            replaced_val = 'Femelle';
        } else if (info === '3') {
            replaced_val = 'Castré';
        } else {
            replaced_val = 'Erreur';
        }

    } else {
        replaced_val = info;
    }
    return replaced_val;
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