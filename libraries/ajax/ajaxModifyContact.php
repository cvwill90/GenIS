<?php

include '../fonctions.php';

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

$data = filter_input_array(INPUT_GET);

foreach ($data as $dk => $dv){
    $data[$dk] = str_replace("'", "\'", $dv);
}

if (!isset($data['races'])){
    $data['races'] = [];
}

if (!isset($data['codePostal'])){
    $data['codePostal'] = '';
}

$sqlArray = array();

try {
    $update = modifyData($con, $data);
} catch (Exception $ex) {
    $update_error = $ex->getMessage();
}

if (isset($update_error)) {
    $response['statusMsg'] = 'error';
    $response['response']['errorMsg'] = $update_error;
} else {
    $response['statusMsg'] = 'ok';
    $response['response']['msg'] = 'Les données apportées au contact et/ou à son élevage ont bien été enregistrées.';
}

echo json_encode($response);


/*
 * Update functions
 */

function modifyData($con, $data) {
    try {
        $sqlFarm = getFarmSQL($con, $data);
        $sqlTown = getTownSQL($data);
        $sqlContact = getContactSQL($data);
        $allSQL = $sqlFarm . $sqlTown . $sqlContact;
        
        $con->beginTransaction();
        $con->query($allSQL);
        $con->commit();        
    } catch (Exception $ex) {
        throw new Exception($ex->getMessage());
    }
}

/*
 * Verifier quelles donnees ont ete modifiees par rapport a l'elevage
 * RETURN: 
 * STRING --> sql statements concerning farm modifications
 * SEPARATOR --> ";"
 */
function getFarmSQL($con, $data) {
    $sqlFarm = "";
    
    if (!$data['eleveur']){ //Si le contact n'est pas eleveur
        $sqlFarm .= "UPDATE ". DB_NAME .".contact SET id_elevage=NULL WHERE id_contact={$data['idDbContact']};";
    } else {    //Si le contact est eleveur
        if (isset($data['nouvelElevage'])){    //Si l'utilisateur a coché l'option "nouvel élevage", c'est qu'il faut en créer un nouveau
            $sqlNewFarm = createNewFarm($data);
            $idDbElevage = "(SELECT last_insert_id())";
            $sqlFarm .= $sqlNewFarm;
        } else {    // Autrement un id elevage de base de donnees est renseigné: soit c'est toujours le meme soit c'en est un autre déjà existant dans la base de données.
            $existingFarmUpdate = updateFarm(/*$con, */$data);
            $idDbElevage = $data['idDbElevage'];
            $sqlFarm .= $existingFarmUpdate;
        }
        $sqlRaces = checkRaces($con, $data, $idDbElevage);
        $sqlFarm .= $sqlRaces;
    }
    return $sqlFarm;
}

/*
 * Crée une nouvelle ferme
 * RETURN:
 * STRING --> SQL Statement for the creation of a new farm
 */

function createNewFarm($data) {
    $sqlNewFarm = "INSERT INTO ". DB_NAME .".elevage (id_elevage,nom_elevage,no_elevage) VALUES (NULL, '{$data['nomElevage']}', '{$data['idElevage']}');";   
    return $sqlNewFarm;
}

/*
 * Vérifie les races présentes sur un élevage et apporte les modifications si nécessaire
 * RETURN:
 * STRING --> SQL statements 
 * SEPARATOR --> ";"
 */
function checkRaces($con, $data, $idDbElevage) {
    $sqlRacesElevage = '';
    //Comparer les races existentes pour l'id elevage en question avec les races qui ont été renseignées dans le formulaire

    //Récupérer toutes les races de la table link_race_elevage pour l'élevage en question
    $sqlGetRacesElevage = "SELECT code_race FROM link_race_elevage WHERE id_elevage = {$idDbElevage};";
    $dbRaces = $con->query($sqlGetRacesElevage);

    //Placer le résultat dans un tableau
    $dbRacesArray = array();
    while ($code_race = $dbRaces->fetch()){
        array_push($dbRacesArray, intval($code_race[0]));
    }
    
    //Si une race de la base de données est en trop par rapport aux races du formulaire, c'est qu'elle a été supprimée
    foreach ($dbRacesArray as $race){
        if (!in_array($race, $data['races'])){
            $sqlRacesElevage .= "DELETE FROM ". DB_NAME .".link_race_elevage WHERE id_elevage = {$idDbElevage} AND code_race = {$race};";
        }
    }

    //Si une race du formulaire est en trop par rapport aux races de la base de données, c'est qu'elle a été ajoutée
    foreach ($data['races'] as $race){
        if (!in_array($race, $dbRacesArray)){
            $sqlRacesElevage .= "INSERT INTO ". DB_NAME .".link_race_elevage VALUES ({$idDbElevage}, {$race});";
        }
    }
    
    //if ($sqlRacesElevage = ''){ $sql = ''; } else { $sql = $sqlRacesElevage; }
    
    return $sqlRacesElevage;
}

/*
 * Mets à jour l'élevage du contact et également les données relatives à cet élevage, sauf les données relatives aux races élevées
 * RETURN:
 * STRING --> SQL statements 
 * SEPARATOR --> ";"
 */

function updateFarm(/*$con, */$data) {
    $sqlUpdateFarm = '';
    $sqlUpdateFarm .= "UPDATE ". DB_NAME .".contact SET id_elevage={$data['idDbElevage']} WHERE id_contact={$data['idDbContact']};";
    $sqlUpdateFarm .= "UPDATE ". DB_NAME .".elevage SET nom_elevage='{$data['nomElevage']}', no_elevage='{$data['idElevage']}' WHERE id_elevage={$data['idDbElevage']};";
    /*$sqlRaces = checkRaces($con, $data, $data['idDbElevage']);
    $sqlUpdateFarm .= $sqlRaces;*/
    return $sqlUpdateFarm;
}

/*
 * Vérifier quelles données ont ete modifiées par rapport à la ville
 * RETURN:
 * STRING --> sql statements concerning town modifications)
 * SEPARTOR --> ";"
 */

function getTownSQL($data) {
    $townName = mb_strtoupper($data['ville'], 'UTF-8');
    $sqlTown = "";
    if ($data['idVille'] == 1) {    //Si la ville n'existe pas dans la base de données --> on l'ajoute dans la BDD
        $sqlTown .= "INSERT INTO ". DB_NAME .".commune (id_commune,lib_commune,cp_commune,no_dpt)
                    VALUES (NULL, '{$townName}', '{$data['codePostal']}', {$data['departement']});";
        $sqlTown .= "UPDATE ". DB_NAME .".contact SET id_commune=(SELECT last_insert_id()) WHERE id_contact={$data['idDbContact']};";
    } else {    //Sinon soit il s'agit de la même ville, soit il s'agit d'une autre ville existant dans la BDD
        //$sqlTown .= "UPDATE ". DB_NAME .".commune SET lib_commune='{$townName}', cp_commune='{$data['codePostal']}', no_dpt={$data['departement']} WHERE id_commune={$data['idVille']};";
        $sqlTown .= "UPDATE ". DB_NAME .".contact SET id_commune={$data['idVille']} WHERE id_contact={$data['idDbContact']};";
    }
    return $sqlTown;
}

/*
 * Retourne le code sql pour modifier les données de la table contact (pas les clés étrangères)
 */

function getContactSQL($data) {
    $sqlContact = "UPDATE ". DB_NAME .".contact SET nom='{$data['nom']}', prenom='{$data['prenom']}', adresse='{$data['adresse']}', adresse2='{$data['adresseCompl']}', tel='{$data['tel1']}', tel2='{$data['tel2']}', mail='{$data['mail']}', notes='{$data['notes']}' WHERE id_contact={$data['idDbContact']};";
    return $sqlContact;
}
