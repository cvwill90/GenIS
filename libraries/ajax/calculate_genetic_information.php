<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../fonctions.php';
require '../classes/Animal.php';

$response = array();

$data = filter_input_array(INPUT_GET);

if (isset($data['fatherId']) && isset($data['motherId']) && count($data) == 2) {
    $animal_genetic_information = get_animal_genetic_information($data);
    echo json_encode($animal_genetic_information);
} else {
    echo '{"error": true, "error_msg": "Aucun id_animal n\'a été fourni"}';
}

function get_animal_genetic_information(array $parents) {
    $child_blood_percentage = get_animal_blood_percentage($parents);
    return array("blood_percentage" => $child_blood_percentage);
}

function get_animal_blood_percentage(array $parents) {
    $father = new Animal($parents['fatherId']);
    $mother = new Animal($parents['motherId']);
    $father_blood_percentage = $father->get_animal_information(['pourcentage_sang_race'])[0];
    $mother_blood_percentage = $mother->get_animal_information(['pourcentage_sang_race'])[0];
    $animal_blood_percentage = calculate_blood_percentage($father_blood_percentage, $mother_blood_percentage);
    return $animal_blood_percentage;
}

function calculate_blood_percentage($father_blood_percentage, $mother_blood_percentage): float {
    if (is_null($father_blood_percentage) || is_null($mother_blood_percentage)){
        return 0.0;
    } else {
        return ($father_blood_percentage + $mother_blood_percentage) / 2;
    }
}

function get_animal_livre_genealogique() {
    
}