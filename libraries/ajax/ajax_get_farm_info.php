<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

header('Content-type: text/html; charset=UTF-8');
require_once '../classes/Elevage.php';
        
$request_type = $_GET["type"];
$id_farm = $_GET["id_farm"];

if ($request_type == 'races') {
    $result = get_bread_races($id_farm);
} elseif ($request_type == 'animals') {
    $race = $_GET["race"];
    $repro = $_GET["repro"];
    $sex = $_GET["sex"];
    $period_start = $_GET["start"];
    $period_end = $_GET["end"];

    $result = get_animals_on_farm($id_farm, $race, $sex, $repro, $period_start, $period_end);
} else {
    $result = json_encode(array('error'=>true, 'errormsg'=>'Invalid Request parameters.'));
}

echo $result;

function get_bread_races($id_farm) {
    $farm = new Elevage($id_farm);
    return $farm->get_bread_races();
}

function get_animals_on_farm($id_farm, $race, $sex, $repro, $period_start, $period_end) {
    $farm = new Elevage($id_farm);
    return $farm->get_farm_animals($race, $sex, $repro, $period_start, $period_end);
}