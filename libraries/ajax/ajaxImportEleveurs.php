<?php

require '../fonctions.php';

$response = array();

$con = pdo_connection(HOST_DB, DB_NAME, USER_DB, PW_DB);

if (isset($_POST['data']) && isset($_POST['race'])){
    $data = $_POST['data'];
    $race = $_POST['race'];
    $response = importEleveurs($con, $data, $race);

} else {
    $response = array("statusMsg" => "error", "errorMsg" => "Error in import request!");
}
 
echo json_encode($response);



function importEleveurs($con, $data, $race) {
    $structData = structData($data);
    try {
        
        $check = checkExistences($con, $structData, $race);
        
        $insert = insertData($con, $check, $race);
        
    } catch (Exception $ex) {
        return setResponse($ex);
    }
    if ($insert){
        return setResponse('L\'importation s\'est déroulée avec succès !');
    }
}

function structData($data) {
    $dataArray = explode(';', $data);
    $structData = array();
    $i = 0;
    $j = 0;
    foreach ($dataArray as $da) {
        if ($j != 0 && $j%11 == 0){
            $i++;
            $j=0;
        }
        $structData[$i][$j]= str_replace("'", "\'", $da);
        $j++;
    }
    $corrStructData = checkEmptyNames($structData);
    return $corrStructData;
}

function checkEmptyNames($data){
    $corrData = $data;
    foreach ($corrData as $eleveurK => $eleveurV){
        //echo "<br>Nom: ". strlen($eleveurV[0]) .", Prénom: ". strlen($eleveurV[1]) .", Nom élevage: ". strlen($eleveurV[2]);
        if (strlen($eleveurV[0]) <= 2 && strlen($eleveurV[1]) <= 2 && strlen($eleveurV[2]) > 2) {
            //echo ' --> remplace nom éleveur!';
            $corrData[$eleveurK][0] = $corrData[$eleveurK][2];
        } elseif (strlen($eleveurV[0]) > 2 && strlen($eleveurV[2] <= 2)) {
            //echo ' --> remplace nom élevage!';
            $corrData[$eleveurK][2] = $corrData[$eleveurK][0];
        }
    }
    return $corrData;
}

function checkExistences($con, $structData, $race){
    $eleveurID = array();
    $villeID = array();
    $elevageID = array();
    $linkRaceElevage = array();
    foreach ($structData as $dataVal) {
        try {
            $result_eleveur = eleveurExiste($con, $dataVal[0], $dataVal[1], $dataVal[3]);
            array_push($eleveurID, $result_eleveur[0]);
            if (!is_null($result_eleveur[0])){
                //echo "eleveur trouvé";
                //print_r($result_eleveur);
                array_push($villeID, $result_eleveur[2]);
                array_push($elevageID, $result_eleveur[1]);
                $result_link_race_elevage = linkRaceElevageExiste($con, $result_eleveur[1], $race, 'elevage');
            } else {
                //echo 'pas d\'éleveur';
                $result_ville = villeExiste($con, $dataVal[4]);
                array_push($villeID, $result_ville);
                $result_elevage = elevageExiste($con, $dataVal[2], $dataVal[0]);
                array_push($elevageID, $result_elevage);
                if (is_null($result_elevage)){
                    //echo "elevage n'existe pas";
                    $result_link_race_elevage = 0;
                } else {
                    //echo "elevage existe";
                    $result_link_race_elevage = linkRaceElevageExiste($con, $result_elevage, $race, 'elevage');
                }
            }
            array_push($linkRaceElevage, $result_link_race_elevage);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
    return array("data" => $structData, "eleveurs" => $eleveurID, "liens" => $linkRaceElevage, "villes" => $villeID, "elevages" => $elevageID);
}

function eleveurExiste($con, $nom, $prenom, $cp) {
    $sql_trouve_eleveur = "SELECT con.id_contact, con.id_elevage, con.id_commune FROM contact con LEFT JOIN commune com ON con.id_commune=com.id_commune WHERE con.nom = '{$nom}' AND con.prenom = '{$prenom}' AND com.cp_commune = '{$cp}';";
    //echo $sql_trouve_eleveur;
    try {
        $con->beginTransaction();
        $recordset = $con->query($sql_trouve_eleveur);
        $con->commit();
        $result = $recordset->fetch();
        $eleveurID = $result[0];
        $elevageID = $result[1];
        $villeID = $result[2];
    } catch (Exception $ex) {
        $con->rollback();
        throw new Exception('Error during farmer existence check: '. $ex->getMessage());
    }
    
    if (is_null($eleveurID)){
        return array($eleveurID, $elevageID, $villeID); //return [null, null]
    } else {
        return array(intval($eleveurID), intval($elevageID), intval($villeID));
    }
}

function linkRaceElevageExiste($con, $eleveur, $race, $lookFor='eleveur'){
    if ($lookFor == 'eleveur'){
        $sql_trouve_linkRaceElevage = "SELECT l.code_race FROM contact co LEFT JOIN elevage e ON e.id_elevage=co.id_elevage LEFT JOIN link_race_elevage l ON l.id_elevage=e.id_elevage WHERE l.code_race = {$race} AND co.id_contact = {$eleveur};";
    } else {
        $sql_trouve_linkRaceElevage = "SELECT l.code_race FROM link_race_elevage l WHERE l.code_race = {$race} AND l.id_elevage = {$eleveur};";
    }
    //echo $sql_trouve_linkRaceElevage;
    try {
        $con->beginTransaction();
        $recordset = $con->query($sql_trouve_linkRaceElevage);
        $con->commit();
        $code_race = $recordset->fetch()[0];
    } catch (Exception $ex) {
        $con->rollback();
        throw new Exception('Error during linkRaceElevage existence check: '. $ex->getMessage());
    }
    
    if (is_null($code_race)){
        return 0; 
    } else {
        return 1;
    }
}

function villeExiste($con, $ville){
    $sql_trouve_ville = "SELECT id_commune FROM commune WHERE lib_commune = '{$ville}';";

    try {
        $con->beginTransaction();
        $recordset = $con->query($sql_trouve_ville);
        $con->commit();
        $villeID = $recordset->fetch()[0];
    } catch (Exception $ex) {
        $con->rollback();
        throw new Exception('Error during town existence check: '. $ex->getMessage());
    }
    if (is_null($villeID)){
        return $villeID; 
    } else {
        return intval($villeID);
    }
}

function elevageExiste($con, $nomElevage, $nomEleveur){
    if (str_replace(' ', '', $nomElevage)) {
        $nom = $nomElevage;
    } else {
        $nom = $nomEleveur;
    }
    $sql_trouve_elevage = "SELECT e.id_elevage FROM elevage e LEFT JOIN contact co ON co.id_elevage=e.id_elevage WHERE e.nom_elevage = '{$nom}' AND co.nom = '{$nomEleveur}';";
    
    try {
        $con->beginTransaction();
        $recordset = $con->query($sql_trouve_elevage);
        $con->commit();
        $elevageID = $recordset->fetch()[0];
    } catch (Exception $ex) {
        $con->rollback();
        throw new Exception('Error during farm existence check: '. $ex->getMessage());
    }
    if (is_null($elevageID)){
        return $elevageID; 
    } else {
        return intval($elevageID);
    }
}

function insertData($con, $check, $race){
    try {
        loopData($con, $check, $race);
    } catch (Exception $ex) {
        throw new Exception($ex->getMessage());
    }
    return TRUE;
}

function loopData($con, $check, $race){
    $data = $check['data'];
    $eleveurs = $check['eleveurs'];
    $liens = $check['liens'];
    //print_r($liens);
    $villes = $check['villes'];
    $elevages = $check['elevages'];
    $i = 0;
    foreach ($data as $d){
        try {
            $villeID = getTownID($con, $villes[$i], $d[3], $d[4]);
            $elevageID = getElevageID($con, $elevages[$i], $d[2]);
            if (is_null($eleveurs[$i])){
                insertNewEleveur($con, $d, $villeID, $elevageID);
                //$result_insertEleveur = insertNewEleveur($con, $d, $villeID, $elevageID);
            }
            if (!$liens[$i]){
                insertNewLinkRaceElevage($con, $race, $elevageID);
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        $i++;
    }
}

function getTownID($con, $villeID, $villeCP, $villeNom){
    if (!is_null($villeID)){
        return $villeID;
    } else {
        $db = DB_NAME;
        $code_departement = intval(substr($villeCP, 0, 2));
        $sqlInsertNewTown = "INSERT INTO {$db}.commune VALUES (NULL, '{$villeNom}', '{$villeCP}', {$code_departement});";
        try {
            $con->beginTransaction();
            $con->query($sqlInsertNewTown);
            $villeID = $con->lastInsertId();
            $con->commit();
        } catch (Exception $ex) {
            throw new Exception('Error while inserting new town: '. $ex->getMessage());
        }
        return $villeID;
    }
}

function getElevageID($con, $elevageID, $elevageNom){
    if (!is_null($elevageID)){
        return $elevageID;
    } else {
        $db = DB_NAME;
        $sqlInsertNewElevage = "INSERT INTO {$db}.elevage VALUES (NULL, '{$elevageNom}', '');";
        try {
            $con->beginTransaction();
            $con->query($sqlInsertNewElevage);
            $elevageID = $con->lastInsertId();
            $con->commit();
        } catch (Exception $ex) {
            throw new Exception('Error while inserting new farm: '. $ex->getMessage());
        }
        return $elevageID;
    }
}

function insertNewEleveur($con, $data, $villeID, $elevageID){
    $db = DB_NAME;
    $eleveurNom = $data[0];
    $eleveurPrenom = $data[1];
    $eleveurAdresse1 = $data[5];
    $eleveurAdresse2 = $data[6];
    $eleveurTelFixe = $data[7];
    $eleveurTelPor = $data[8];
    $eleveurMail = $data[9];
    $eleveurNotes = $data[10];
    $sql_insertNewEleveur = "INSERT INTO {$db}.contact (id_contact, nom, prenom, adresse, adresse2, tel, tel2, mail, id_commune, notes, id_elevage) VALUES (NULL, '{$eleveurNom}', '{$eleveurPrenom}', '{$eleveurAdresse1}', '{$eleveurAdresse2}', '{$eleveurTelFixe}', '{$eleveurTelPor}', '{$eleveurMail}', {$villeID}, '{$eleveurNotes}', {$elevageID});";
    
    try {
        $con->beginTransaction();
        $con->query($sql_insertNewEleveur);
        $con->commit();
    } catch (Exception $ex) {
        throw new Exception('Error while inserting new eleveur: '. $ex->getMessage());
    }
    return TRUE;
}

function insertNewLinkRaceElevage($con, $race, $eleveur){
    $db = DB_NAME;
    try {
        $sqlInsertNewLinkRaceElevage = "INSERT INTO {$db}.link_race_elevage VALUES ({$eleveur},{$race});";
        $con->beginTransaction();
        $con->query($sqlInsertNewLinkRaceElevage);
        $con->commit();
    } catch (Exception $ex) {
        throw new Exception('Error while inserting new link_race_elevage entry: '. $ex->getMessage());
    }
    return TRUE;
}

function setResponse($result){
    if (is_a($result, 'Exception')){
        $response['statusMsg'] = 'error';
        $response['errorMsg'] = $result->getMessage();
    } else {
        $response['statusMsg'] = 'ok';
        $response['msg'] = $result;
    }
    return $response;
}