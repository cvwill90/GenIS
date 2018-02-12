<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Animal.php';

if (isset($_POST['id_animal'])) {
    $id = $_POST['id_animal'];
    $animal = new Animal($id);
    $deletion = $animal->delete_animal();
    echo json_encode($deletion);
} else {
    echo '{"error": true, "error_msg": "Aucun id_animal n\'a été fourni"}';
}