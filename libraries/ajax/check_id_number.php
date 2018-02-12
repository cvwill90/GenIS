<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../fonctions.php';
autoload_classes();

if (isset($_POST["animalID"])) {
    $id = $_POST["animalID"];
    $response = check_id_number_existence($id);
} else {
    $response = json_encode(array('error'=>TRUE, 'error_msg'=>'Aucune valeur n\'a été fournie pour le contrôle du numéro d\'identification.'));
}

echo $response;


function check_id_number_existence($id) {
    $sql = "SELECT nom_animal as nom
            FROM animal a
            WHERE no_identification='{$id}'";
            
    $con = new DatabaseConnection(HOST_DB, USER_DB, DB_NAME, PW_DB);
    
    $result = $con->select($sql);
    
    $con->close_db_connection();
    
    $i = 0;
    while ($animal = $result->fetch()) {
        $i++;
        $animal_name = $animal['nom'];
    }
    
    if ($i > 0) {
        $response = json_encode("Ce numéro d'identification est déjà attribué à ". $animal_name .".");
    } else {
        $response = json_encode(true);
    }
    
    return $response;
}