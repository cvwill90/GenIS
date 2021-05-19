<?php

require '../fonctions.php';
require '../classes/AnimalHistory.php';

$response = array();

$con = pdo_connection(HOST_DB, DB_NAME, USER_DB, PW_DB);

$data = filter_input_array(INPUT_POST);

$pere = (isset($data['father'])) ? $data['father'] : 1;
$mere = (isset($data['mother'])) ? $data['mother'] : 2;

if ($data['farmId'] == 0) {
    $farmId = 'NULL';
} else {
    $farmId = $data['farmId'];
}

if (isset($data['animal_dead'])) {
    $death_date = $data['deathDate'];
} else {
    $death_date = "alive";
}

$sql_update_animal = "UPDATE " . DB_NAME . ".animal "
        . "SET nom_animal='{$data['animalName']}', "
            . "sexe={$data['animalSex']}, "
            . "no_identification='{$data['animalID']}', "
            //. "id_livre={$data['livre_gene']}, "
            . "date_naiss='{$data['birthDate']}', "
            . "conservatoire={$data['conserv']}, "
            . "id_pere=$pere, "
            . "id_mere=$mere "
        . "WHERE id_animal={$data['IDanimalChoisi']}";
$animal_history = new AnimalHistory($data['IDanimalChoisi']);
$sql_birth_update_result = $animal_history->change_animal_birth_info($data['birthDate'], $farmId);
$sql_death_update_result = $animal_history->change_animal_death_date($death_date);

if ($pere == 1 && $mere == 2) {
    $sql_ancetre_update_result = $animal_history->change_ancetre_information(true, 'fondateur', $data['pourcentage_sang_animal']);
} elseif ($pere == 1 || $mere == 2) {
    $sql_ancetre_update_result = $animal_history->change_ancetre_information(true, 'titre_initial', $data['pourcentage_sang_animal']);
} else {
    $sql_ancetre_update_result = $animal_history->change_ancetre_information(false, null, null);
}

$sql = array($sql_update_animal, $sql_birth_update_result, $sql_death_update_result, $sql_ancetre_update_result);

try {
    $con->beginTransaction();
    foreach ($sql as $s) {
        if ($s) {
            $con->query($s);
        }
    }
    $con->commit();
} catch (Exception $ex) {
    $con->rollBack();
    $error = $ex->getMessage();
}

$response = array();

if (isset($error)) {
    $response["status"] = "error";
    $response["statusMsg"] = $error;
} else {
    $response["status"] = "ok";
    $response["statusMsg"] = "Les modifications de l'animal ont bien été apportées à la base de données.";
}

echo json_encode($response);
