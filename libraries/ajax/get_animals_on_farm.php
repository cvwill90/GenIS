<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../classes/Elevage.php';

$id_farm = $_POST['farm_id'];
$race = $_POST['race'];
$sex = $_POST['sex'];

if (isset($_POST['repro'])) {
    $repro = $_POST['repro'];
} else {
    $repro = 0;
}

$period_start = $_POST['minDate'];
$period_end = $_POST['maxDate'];

$farm = new Elevage($id_farm);

$animals = $farm->get_farm_animals($race, $sex, $repro, $period_start, $period_end);

echo $animals;