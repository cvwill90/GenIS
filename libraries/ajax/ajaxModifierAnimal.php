<?php

require '../fonctions.php';
require '../classes/AnimalHistory.php';

$response = array();

$con = pdo_connection(HOST_DB, DB_NAME, USER_DB, PW_DB);

$data = filter_input_array(INPUT_GET);

if (intval($data['type']) == 1){
    
    if (isset($data['race'])){
        $race = $data["race"];
    }

    if (isset($data['term'])){
        $animal = $data["term"];
    }

    $sql = "SELECT a1.*, a2.nom_animal as nom_pere, a3.nom_animal as nom_mere, lg.id_livre as livre, GROUP_CONCAT(p.date_entree SEPARATOR '*#%') as date_naiss2, GROUP_CONCAT(p.date_sortie SEPARATOR '*#%') as date_sortie, GROUP_CONCAT(e.id_elevage SEPARATOR '*#%') as id_elevage, GROUP_CONCAT(e.nom_elevage SEPARATOR '*#%') as nom_elevage "
            . "FROM ". DB_NAME .".animal a1 "
            . "LEFT JOIN animal a2 ON a2.id_animal=a1.id_pere "
            . "LEFT JOIN animal a3 ON a3.id_animal=a1.id_mere "
            . "LEFT JOIN periode p ON p.id_animal=a1.id_animal "
            . "LEFT JOIN elevage e ON e.id_elevage=p.id_elevage "
            . "LEFT JOIN livre_genealogique lg ON lg.id_livre = a1.id_livre "
            . "WHERE a1.code_race={$race} "
                . "AND (a1.nom_animal LIKE '%{$animal}%' OR a1.no_identification LIKE '%{$animal}%') "
                . "AND (p.id_type=3 OR p.id_type=1) "
            . "GROUP BY id_animal, nom_animal, sexe, no_identification, date_naiss, reproducteur, fecondation, coeff_consang, conservatoire, valide_animal, code_race, id_pere, id_mere, id_photo, nom_pere, nom_mere "
            . "ORDER BY nom_animal ASC LIMIT 0, 10";
                
    $query = $con->query($sql);

    $string = "[";

    while ($list = $query->fetch()) {
        $nom_elevage = explode("*#%", $list['nom_elevage']);
        $id_elevage = explode("*#%", $list['id_elevage']);
        $date_sortie = explode("*#%", $list['date_sortie']);
        $string = $string . "{\"label\": \"" . $list['no_identification'] . ' - ' . $list['nom_animal'] . "\", "
                . "\"value\": \"" . $list['nom_animal'] . "\", "
                . "\"id\": " . $list['id_animal'] . ", "
                . "\"sexe\": " . $list['sexe'] . ","
                . "\"no\": \"" . $list['no_identification'] . "\","
                . "\"date_naiss\": \"" . $list['date_naiss2'] . "\","
                . "\"date_mort\": \"" . $date_sortie[0] . "\","
                . "\"cons\": " . $list['conservatoire'] . ","
                . "\"id_p\": " . $list['id_pere'] . ","
                . "\"nom_p\": \"" . $list['nom_pere'] . "\","
                . "\"id_m\": " . $list['id_mere'] . ","
                . "\"nom_m\": \"" . $list['nom_mere'] . "\","
                . "\"livre\": \"" . $list['livre'] . "\","
                . "\"nom_elev\": \"" . $nom_elevage[0] . "\","
                . "\"id_elev\": \"" . $id_elevage[0] . "\"},";
    }

    $string = $string . "]";

    $trimmed = str_replace(",]", "]", $string);
    echo $trimmed;
} elseif (intval($data['type']) == 2) {
    
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
    
    $sql_update_animal = "UPDATE ". DB_NAME .".animal SET nom_animal='{$data['animalName']}', sexe={$data['animalSex']}, no_identification='{$data['animalID']}', id_livre={$data['livre_gene']}, date_naiss='{$data['birthDate']}', conservatoire={$data['conserv']}, id_pere={$data['fatherId']}, id_mere={$data['motherId']} WHERE id_animal={$data['IDanimalChoisi']}";
    $animal_history = new AnimalHistory($data['IDanimalChoisi']);
    $sql_birth_update_result = $animal_history->change_animal_birth_info($data['birthDate'], $farmId);
    $sql_death_update_result = $animal_history->change_animal_death_date($death_date);
    
    $sql = array($sql_update_animal, $sql_birth_update_result, $sql_death_update_result);
    
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
}
