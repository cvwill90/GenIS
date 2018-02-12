<?php
include '../fonctions.php';

$error_log_folder = 'parente';
$error_log_file = 'parente_log.txt';
prepare_error_log($error_log_folder, $error_log_file);
ini_set('track_errors',1);
ini_set('display_errors','stderr');
ini_set('error_log', ERROR_LOG_FOLDER .'\\'. $error_log_folder .'\\'. $error_log_file);

session_start();

$race = $_GET['key1'];
$ref = $_GET['key2'];
$entree = $_GET['key3'];


/*
 * ECRITURE DU FICHIER DE LANCEMENT DE PARENTE.EXE: lancement_parente.txt
 */

$fp = fopen("C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\lancement_parente.txt", "w+");
fputs($fp, "C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $entree ."\r\n");
fputs($fp, "C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $ref ."\r\n");
fclose($fp);

/*
 * Vérification de l'existence du dossier de destination;
 */
$destination_folder = PEDIG_DUMP_FOLDER . "\\parente\\";
$destination_file = $destination_folder ."parente_". $race .".csv";

if (!is_dir($destination_folder)){
    mkdir($destination_folder, 0777, true);
}

/*
 * EXECUTION DE PARENTE.EXE
 */
$parente_result = array();
$output=exec('C:\wamp64\www\genis.cra\libraries\pedigModules\parente.exe < C:\wamp64\www\genis.cra\calculs\pedigFiles\lancement_parente.txt', $parente_result);

/*
 * ECRITURE DE LA MATRICE DE SORTIE DANS UN FICHIER .CSV
 */
    
$resultParente = fopen($destination_file, "w+");
fputs($resultParente, mb_convert_encoding("Matrice de parenté\r\n", 'UTF-16LE', 'UTF-8'));
fclose($resultParente);

extract_data($parente_result, $destination_file);

if (!error_get_last()) {
    echo '{"status": "ok"}';
} else {
    echo '{"status": "wrong", "errorMessage": "Erreur lors de l\'exploitation des résultats de parente.exe."}';
}

/*
 * FUNCTIONS
 */

function extract_data($array_parente, $destination_file){
    $matrix_pos = array_search('Complete matrix', $array_parente);
    $table_pos = array_search('Within group 1', $array_parente);
    
    if ($matrix_pos) {
        $results = array_slice($array_parente, $matrix_pos+1);
        $matrix = process_parente_matrix($results);
    } elseif ($table_pos) {
        $results = array_slice($array_parente, $table_pos+1);
        $matrix = process_parente_table($results);
    } else {
        throw new Exception('Parente.exe n\'a pas généré de sortie exploitable');
    }
    write_matrix_to_file($matrix, $destination_file);
}

function process_parente_matrix($results) {
    try {
        $tableau_parente = replace_pedig_numbers_matrix($results);
        $matrix = build_matrix($tableau_parente);
        return $matrix;
    } catch (Exception $ex) {
        throw new Exception($ex->getMessage());
    }
    
}

function process_parente_table($results){
    try {
        $tableau_parente = replace_pedig_numbers_table($results);
        $matrix = build_matrix($tableau_parente);
        return $matrix;
    } catch (Exception $ex) {
        throw new Exception($ex->getMessage());
    }
}

function replace_pedig_numbers_table($results){
    $tableau_parente = array();
    $list_animals = array();
    $id_pedig = '';
    for ($i=0; $i<count($results); $i++) {
        if ($results[$i]){
            $parente_anim = array_slice(explode(';', preg_replace('/\s+/', ';', $results[$i])), 1);
            if ($id_pedig != $parente_anim[0]) {
                $line_self_parente = array($parente_anim[0], $parente_anim[0], '0.500');
                $replaced_id_line = replace_id_line($line_self_parente);
                array_push($tableau_parente, $replaced_id_line);
                array_push($list_animals, array($replaced_id_line[0], $replaced_id_line[1]));
            }
            $line = replace_id_line($parente_anim);
            $tableau_parente[count($tableau_parente)] = $line;
            $id_pedig = $parente_anim[0];
        } else {
            break;
        }
    }
    $last_parente_line = $tableau_parente[count($tableau_parente)-1];
    array_push($tableau_parente, array($last_parente_line[2], $last_parente_line[3], $last_parente_line[2], $last_parente_line[3], '0.500'));
    array_push($list_animals, array($last_parente_line[2], $last_parente_line[3]));
    return array(array_reverse($list_animals), array_reverse($tableau_parente));
}

function replace_pedig_numbers_matrix($results){
    $list_animals_pedig = array_slice(explode(';', preg_replace('/\s+/', ';', $results[0])), 1);
    $parente_tableau = array();
    for ($i=1; $i<count($results); $i++) {
        if ($results[$i]) {
            $parente_anim = array_slice(explode(';', preg_replace('/\s+/', ';', $results[$i])), 1);
            $id_animal1 = $parente_anim[0];
            $j = 1;
            for ($j=1; $j<count($parente_anim); $j++) {
                $id_animal2 = $list_animals_pedig[$j-1];
                $parente = $parente_anim[$j];
                array_push($parente_tableau, array($id_animal1, $id_animal2, $parente));
            }
        } else {
            break;
        }
    }
    $parente_tableau_replaced = array();
    $list_animals = array();
    $id_pedig = '';
    for ($k=0; $k<count($parente_tableau); $k++){
        $line = replace_id_line($parente_tableau[$k]);
        $parente_tableau_replaced[$k] = $line;
        if ($id_pedig != $parente_tableau[$k][0]){
            array_push($list_animals, array($line[0], $line[1]));
        }
        $id_pedig = $parente_tableau[$k][0];
    }
    return array($list_animals, $parente_tableau_replaced);
}

function replace_id_line($line_array){
    $animal_dict = get_animal_dict();
    $animal1 = $animal_dict->$line_array[0];
    $animal2 = $animal_dict->$line_array[1];
    $no_id1 = $animal1[0];
    $nom1 = $animal1[1];
    $no_id2 = $animal2[0];
    $nom2 = $animal2[1];
    $parente = $line_array[2];
    $line_replaced_id = array($no_id1, $nom1, $no_id2, $nom2, $parente);
    return $line_replaced_id;
}

function build_matrix($tableau) {
    $matrix_header = build_matrix_header($tableau[0]);
    $matrix_body = build_matrix_body($tableau[1]);
    return array($matrix_header, $matrix_body);
}

function build_matrix_header($list_animals){
    $header_line_1 = 'Nom;';
    $header_line_2 = ';No SIRE';
    for ($i=0; $i<count($list_animals); $i++) {
        $header_line_1 = implode(';', array($header_line_1, $list_animals[$i][1]));
        $header_line_2 = implode(';', array($header_line_2, $list_animals[$i][0]));
    }
    return array($header_line_1, $header_line_2);
}

function build_matrix_body($table){
    $animal_precedent = '';
    $matrix = array();
    $line = '';
    for ($i=0; $i<count($table); $i++) {
        if ($animal_precedent != $table[$i][0]){
            array_push($matrix, $line .';0.500');
            $line = $table[$i][1] .';'. $table[$i][0];
            $animal_precedent = $table[$i][0];
        } else {
            $line .= ';'. $table[$i-1][4];
        }
    }
    array_push($matrix, $line .';0.500');
    return array_slice($matrix, 1);
}

function write_matrix_to_file($matrix, $destination_file) {
    $matrix_header_1 = $matrix[0][0];
    $matrix_header_2 = $matrix[0][1];
    $matrix_body = $matrix[1];

    $resultParente = fopen($destination_file, "w");
    fputs($resultParente, mb_convert_encoding($matrix_header_1, 'UTF-16LE', 'UTF-8') ."\r\n");
    fputs($resultParente, mb_convert_encoding($matrix_header_2, 'UTF-16LE', 'UTF-8') ."\r\n");

    for ($i=0; $i<count($matrix_body); $i++){
        fputs($resultParente, mb_convert_encoding($matrix_body[$i], 'UTF-16LE', 'UTF-8') ."\r\n");
    }
    fclose($resultParente);
}

function add_parente_to_self($table){
    $modified_table = array();
    $last_pedig_id = '';
    for ($i=0; $i<count($table); $i++) {
        echo $table[$i];
        if ($last_pedig_id != $table[$i][0]) {
            array_push($modified_table, array($table[$i][0], $table[$i][0], '0.500'));
        }
        array_push($modified_table, $table[$i]);
    }
    return $modified_table;
}

function get_animal_dict(){
    $fd = fopen(PROJECT_ROOT ."libraries\\pedigModules\\dict_ped_util.json", "r");
    $animal_dict = json_decode(fread($fd, filesize(PROJECT_ROOT ."libraries\\pedigModules\\dict_ped_util.json")));
    fclose($fd);
    return $animal_dict;
}