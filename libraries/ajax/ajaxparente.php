<?php
include '../fonctions.php';

$error_log_folder = 'parente';
$error_log_file = 'parente_log.txt';
prepare_error_log($error_log_folder, $error_log_file);
ini_set('track_errors',1);
ini_set('display_errors','stderr');
ini_set('error_log', ERROR_LOG_FOLDER .'\\'. $error_log_folder .'\\'. $error_log_file);

session_start();

$input_validation_result = validate_input($_GET, ['NOT_NULL'], 'GET_INPUT');

try {
    input_validation_contains_errors($input_validation_result);
} catch (Exception $ex) {
    print_r($ex->getMessage());
    exit();
}

$race = $_GET['key1'];
$reference_file = $_GET['key2'];
$entry_file = $_GET['key3'];

$launch_file = "lancement_parente.txt";

create_parente_launch_file($launch_file, $entry_file, $reference_file);

// Verify existance of destination folder
$destination_folder = PEDIG_DUMP_FOLDER . "\\parente\\";

if (!is_dir($destination_folder)) mkdir($destination_folder, 0777, true);

// Execute parente.exe
$parente_result = array();
$output=exec('C:\wamp64\www\genis.cra\libraries\pedigModules\parente.exe < C:\wamp64\www\genis.cra\calculs\pedigFiles\lancement_parente.txt', $parente_result);

// How many groups of individuals are there (Hardcoded as 1 group at the moment)
$nb_groups = 1;

/*
 *  Extract data from parente output command lines
 */
$parente_output_csv_array = array(); // This array will contain all lines of the csv file

// Read parente file and build parente reference array
$ref_parente_array = array();
$parente = fopen("C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $reference_file, 'r');
while ($line = fgets($parente)) {
    $clean_line = preg_replace(['/\t/'], ' ', remove_spaces($line));
    array_push($ref_parente_array, explode(' ', $clean_line)[0]);
}
$GLOBALS['ref_animals_parente'] = $ref_parente_array;

// Add title of csv file
$title = "Coefficients de parenté";

// Extract context information from the output command lines 
$extracted_context_info = extract_context_info($parente_result);

// Write context information into output csv file
$parenteCSVFile = fopen($destination_folder ."parente_". $race .".csv", "w+");
$nb_semicols = '';
for ($i=0; $i<=$GLOBALS['nb_individuals_studied']; $i++) {
    $nb_semicols .= ';';
}

put_lines_in_output_file($parenteCSVFile, ['Coefficients de parenté'.$nb_semicols]);
go_to_line_in_output_file($parenteCSVFile, 2);
put_lines_in_output_file($parenteCSVFile, $extracted_context_info);
fclose($parenteCSVFile);

// Extract average and distribution statistics from the output command lines and write into output csv file
for ($i=1; $i <= $nb_groups; $i++) {
    $group_start_position = find_parente_group_position_in_parente_output($parente_result, $i);
    
    $extracted_mean_statistics = extract_mean_statistics_from_parente_output($parente_result, $i, $group_start_position);
    $extracted_coefficient_distribution = extract_coefficients_distribution($parente_result, $i, $group_start_position);
    $extracted_inbreeding_information = extract_inbreeding_information($parente_result, $i, $group_start_position);
    $extracted_individual_relationships = extract_individual_relationships($parente_result, $i, $group_start_position);
    
    $parenteCSVFile = fopen($destination_folder ."parente_". $race .".csv", "a");
    
    go_to_line_in_output_file($parenteCSVFile, 2);
    put_lines_in_output_file($parenteCSVFile, ['GROUPE ' . strval($nb_groups)]);
    
    go_to_line_in_output_file($parenteCSVFile, 2);
    put_lines_in_output_file($parenteCSVFile, ['Statistiques de parenté']);
    go_to_line_in_output_file($parenteCSVFile, 1);
    put_lines_in_output_file($parenteCSVFile, $extracted_mean_statistics);
    
    go_to_line_in_output_file($parenteCSVFile, 2);
    put_lines_in_output_file($parenteCSVFile, ['Distribution des coefficients']);
    go_to_line_in_output_file($parenteCSVFile, 1);
    put_lines_in_output_file($parenteCSVFile, $extracted_coefficient_distribution);
    
    go_to_line_in_output_file($parenteCSVFile, 2);
    put_lines_in_output_file($parenteCSVFile, ['Consanguinité']);
    go_to_line_in_output_file($parenteCSVFile, 1);
    put_lines_in_output_file($parenteCSVFile, $extracted_inbreeding_information);
    
    go_to_line_in_output_file($parenteCSVFile, 2);
    put_lines_in_output_file($parenteCSVFile, ['Matrice de parenté']);
    go_to_line_in_output_file($parenteCSVFile, 1);
    put_lines_in_output_file($parenteCSVFile, $extracted_individual_relationships);

    fclose($parenteCSVFile);
}

if (!error_get_last()) {
    echo '{"status": "ok"}';
} else {
    echo '{"status": "wrong", "errorMessage": "Erreur lors de l\'exploitation des résultats de parente.exe."}';
}


/*
 * FUNCTIONS
 */

// Create parente launch file
function create_parente_launch_file($launch_file, $entry_file, $reference_file) {
    $fp = fopen("C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $launch_file, "w+");
    fputs($fp, "C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $entry_file ."\r\n");
    fputs($fp, "C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $reference_file ."\r\n");
    fclose($fp);
}

function extract_context_info($parente_exported_commandtool_lines) {
    $context_lines = array();
    $i = 0;
    $exit_loop = false;
    while (!$exit_loop) {
        $line = $parente_exported_commandtool_lines[$i];
        $exploded_line = explode(':', $line);
        $str = remove_spaces($exploded_line[0], ' ');
        switch ($str) {
            case 'Number of individuals':
                array_push($context_lines, 'Nombre d\'individus' .';'. remove_spaces($exploded_line[1]));
                break;
            case 'Number of individual studied':
                $nb_individuals_studied = remove_spaces($exploded_line[1]);
                array_push($context_lines, 'Nombre d\'individus étudiés' .';'. $nb_individuals_studied);
                $GLOBALS['nb_individuals_studied'] = intval($nb_individuals_studied);
                break;
            case 'Maximum matrix size':
                array_push($context_lines, 'Taille maximum de la matrice' .';'. remove_spaces($exploded_line[1]));
                $exit_loop = true;
                break;
        }
        $i++;
    }
    return $context_lines;
}


function extract_mean_statistics_from_parente_output($parente_exported_commandtool_lines, $group_number, $group_start_position) {
    $mean_statistics_lines = array();
    $continue_extracting_mean_statistics = true;
    $j = $group_start_position+1;
    while ($continue_extracting_mean_statistics) {
        $line = $parente_exported_commandtool_lines[$j];
        $exploded_line = explode(':', $line);
        $str = remove_spaces($exploded_line[0], ' ');
        $label = '';
        switch ($str):
            case 'Individual studied': $label = "Nombre d'individus dans le groupe $group_number"; break;
            case 'Number of coefficients': $label = "Nombre de coefficients"; break;
            case 'Mean coefficient': $label = "Coefficient moyen"; break;
            case 'Standard deviation of coefficients':
                $label = "Ecart-type des coefficients : ";
                $continue_extracting_mean_statistics = false;
                break;
        endswitch;
        if ($label != '') array_push($mean_statistics_lines, $label . ";" . remove_spaces($exploded_line[1], ' '));
        if (!$continue_extracting_mean_statistics) break;
        $j++;
    }
    return $mean_statistics_lines;
}

function extract_coefficients_distribution($parente_exported_commandtool_lines, $group_nb, $group_start_position) {
    $coefficient_distribution_lines = array();
    $coefficient_distribution_block_position = find_specific_block_in_individuals_group($group_start_position, $parente_exported_commandtool_lines, 'Distribution of coefficients');
    $j = $coefficient_distribution_block_position + 1;
    $continue_extracting_distribution_coefficients = true;
    while ($continue_extracting_distribution_coefficients) {
        $line = $parente_exported_commandtool_lines[$j];
        if ($line == '') {
            $continue_extracting_distribution_coefficients = false;
        } else {
            $exploded_line = explode(':', $line);
            $str = preg_replace(['/\s/'], '', remove_spaces($exploded_line[0])) .';'. remove_spaces($exploded_line[1], ' ');
            array_push($coefficient_distribution_lines, $str);
            $j++;
        }
    }
    return $coefficient_distribution_lines;
}

/**
 * This function reads parente file inside of the specified group and extracts
 * global inbreeding result
 * @param type $parente_exported_commandtool_lines : The generated content command window by parente.exe
 * @param type $group_nb : Defines from what individuals group the algorithm should extract result data
 */
function extract_inbreeding_information($parente_exported_commandtool_lines, $group_nb, $group_start_position) {
    $inbreeding_result_lines = array();
    $inbreeding_block_position = find_specific_block_in_individuals_group($group_start_position, $parente_exported_commandtool_lines, 'Inbreeding');
    $j = $inbreeding_block_position + 2;
    while (true) {
        $line = $parente_exported_commandtool_lines[$j];
        if ($line == '') {
            break;
        } else {
            $inbreeding_line = format_inbreeding_line($line);
            array_push($inbreeding_result_lines, $inbreeding_line);
            $j++;
        }
    }
    return $inbreeding_result_lines;
}

function format_inbreeding_line($inbreeding_line) {
    $cleaned_inbreeding_line = preg_replace('{(\s)\1+}', '$1', remove_spaces($inbreeding_line));
    $exploded_line = explode(' ', $cleaned_inbreeding_line);
    $animals_dict = get_animal_dict();
    $translated_line = array(
        $exploded_line[0],
        strval($animals_dict[intval($exploded_line[1])][1]),
        $exploded_line[2]
            );
    $formatted_inbreeding_line = implode(';', $translated_line);
    return $formatted_inbreeding_line;
}

function extract_individual_relationships($parente_exported_commandtool_lines, $group_nb) {
    $individual_relationship_lines = array();
    $j = find_position_of_group_inbreeding_individual_coefficients($parente_exported_commandtool_lines, $group_nb) + 1;
    
    while (true) {
        if ($parente_exported_commandtool_lines[$j] == ''){
            break;
        } else {
            $clean_line = preg_replace('{(\s)\1+}', '$1', remove_spaces($parente_exported_commandtool_lines[$j]));
            $exploded_line = explode(' ', $clean_line);
            array_push($individual_relationship_lines, $exploded_line);
        }
        $j++;
    }
    $parente_relationships_mapping = build_relationship_mapping($individual_relationship_lines);
    $parente_matrix = generate_relationship_matrix($parente_relationships_mapping);
    $formatted_matrix = format_matrix_to_csv_output($parente_matrix);
    return $formatted_matrix;
}

function find_parente_group_position_in_parente_output ($parente_exported_commandtool_lines, $group_number) {
    $group_position_in_array = array_search("Group $group_number", $parente_exported_commandtool_lines);
    return $group_position_in_array;
}

function find_specific_block_in_individuals_group($group_start_position, $parente_exported_commandtool_lines, $block_title) {
    $j = $group_start_position;
    while (true) {
        $block_current = remove_spaces($parente_exported_commandtool_lines[$j]);
        if ($block_current == $block_title) break;
        $j++; 
    }
    return $j;
}

function find_position_of_group_inbreeding_individual_coefficients($parente_exported_commandtool_lines, $group_number) {
    $group_individual_coefficients_positon = array_search("Within group ". strval($group_number), $parente_exported_commandtool_lines);
    return $group_individual_coefficients_positon;
}

function build_relationship_mapping ($individual_relationship_lines) {
    $mapping = array();
    for ($i = 0; $i < count($individual_relationship_lines); $i++) {
        $irl = $individual_relationship_lines;
        $mapping[$irl[$i][0]][$irl[$i][1]] = $irl[$i][2];
    }
    return $mapping;
}

function generate_relationship_matrix($individual_relationships_mapping) {
    $irm = $individual_relationships_mapping;
    $matrix = array();
    for ($i = 0; $i < count($GLOBALS['ref_parente_array']); $i++) {
        $animal_id_row = intval($GLOBALS['ref_parente_array'][$i]);
        for ($j = 0; $j < count($GLOBALS['ref_parente_array']); $j++) {
            $animal_id_col = intval($GLOBALS['ref_parente_array'][$j]);
            if ($animal_id_row == $animal_id_col) {
                $matrix[$i][$j] = "0.250";
            } elseif (isset($irm[$animal_id_row][$animal_id_col])) {
                $matrix[$i][$j] = $irm[$animal_id_row][$animal_id_col];
            } else {
                $matrix[$i][$j] = "";
            }
        }
    }
    $transposed_matrix = transpose($matrix);
    $final_matrix = sum_equi_dimensional_array($matrix, $transposed_matrix);
    return $final_matrix;
}

function format_matrix_to_csv_output($parente_matrix) {
    $header_animal_names = ';Nom';
    $header_animal_id_numbers = 'Nom;N° SIRE';
    $formatted_matrix = array();
    $animal_dict = get_animal_dict();
    for ($i = 0; $i < count($parente_matrix); $i++) {
        $animal_id = intval($GLOBALS['ref_parente_array'][$i]);
        $header_animal_names .= ';'. $animal_dict[$animal_id][1];
        $header_animal_id_numbers .= ';'. $animal_dict[$animal_id][0];
        $matrix_row = $animal_dict[$animal_id][1];
        $matrix_row .= ';' . $animal_dict[$animal_id][0];
        $matrix_row .= ';' .  implode(';', $parente_matrix[$i]);
        array_push($formatted_matrix, $matrix_row);
    }
    $final_matrix = array_merge([$header_animal_names, $header_animal_id_numbers], $formatted_matrix);
    return $final_matrix;
}



function put_lines_in_output_file($output_resource, $array) {
    for ($i = 0; $i < count($array); $i++) {
        $line = $array[$i];
        fputs($output_resource, mb_convert_encoding($line . ";\r\n", 'UTF-16LE', 'UTF-8'));
    }
}

function go_to_line_in_output_file($output_resource, $nb_lines) {
    for ($i = 0; $i < $nb_lines; $i++) {
        fputs($output_resource, mb_convert_encoding(";\r\n", 'UTF-16LE', 'UTF-8'));
    }
}

function get_animal_dict(){
    $path = PROJECT_ROOT ."/libraries/pedigModules/dict_ped_util.json";
    $fd = fopen($path, "r");
    $animal_dict = json_decode(fread($fd, filesize($path)));
    fclose($fd);
    return $animal_dict;
}