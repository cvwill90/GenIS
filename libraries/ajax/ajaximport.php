<?php
/**
 * Created by PhpStorm.
 * User: Christophe
 * Date: 13/10/2016
 * Time: 23:34
 */

require '../fonctions.php';

ini_set('max_execution_time', 1200);

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);


$response = array();

if (isset($_POST['key1']) && isset($_POST['key2']) && isset($_POST['key3'])){

    $response['statusMsg'] = 'ok';

    $status = $_POST['key1'];
    $data = $_POST['key2'];
    $race = $_POST['key3'];
    
    if ($status == 'testAnimals') {
        $verification = verifyAnimal($con,$data,$race);
        $response['statusMsg'] = $verification[0];
        $response['response']['msg'] = $verification[1]['msg'];
        $response['response']['errorMsg'] = $verification[1]['errorMsg'];
    } else {
        $import = importAnimals($con, $data, $race);
        $response['statusMsg'] = $import[0];
        $response['response']['msg'] = $import[1]['msg'];
        $response['response']['errorMsg'] = $import[1]['errorMsg'];
    }
    
} else {
    $response['statusMsg'] = 'error';
    $response['response']['errorMsg'] = 'Error in request!';
}

echo json_encode($response);


function verifyAnimal($con, $data, $race){
    $response = array();
    try {
        $dataArray = explode(';',$data);
        $structData = array();

        $i=0;
        $j=0;

        foreach ($dataArray as $dv){
            if($j%7==0 && $j!=0){
                $i++;
                $j=0;
            }
            $structData[$i][$j]=$dv;
            $j++;
        }

        $animals = array();
        $count1 = 0;
        $count2 = 0;

        foreach ($structData as $sd){

            $sql="Select a.id_animal from animal a WHERE a.no_identification='{$sd[0]}' AND a.code_race = {$race}";
            $recordset = $con->query($sql);
            
            if ($recordset->fetch()[0]){
                $animals['existing'][$count1]=array($sd[0],$sd[1]);
                $count1++;
            } else {
                $animals['missing'][$count2]=array($sd[0],$sd[1],$sd[2],$sd[3],$sd[4],$sd[5],$sd[6]); //Array containing father $sd[3] and mother $sd[4]
                $count2++;
            }
        }
        
        $response[0] = 'ok';
        $response[1]['msg'] = $animals;
        $response[1]['errorMsg'] = '';        
    } catch (Exception $ex) {
        $response[0] = 'error';
        $response[1]['msg'] = '';
        $response[1]['errorMsg'] = $ex->getMessage();
    }
    return $response;
}

function importAnimals($con,$data,$race){
    
    try {
        //Set the auto_increment to last row id of animal table
        reset_auto_increment($con, DB_NAME, 'animal', 'id_animal');

        // create temporary tables
        
        $temp_table1 = 'temp_animal1';
        $temp_table2 = 'temp_animal2';
        
        //Drop the tables if they exist
        dropTables($con, $temp_table1, $temp_table2);
        // Create the tables
        $lastId = create_temp_tables($con, $temp_table1, $temp_table2);
        
        // Fill out temporary tables
        $response1 = fillOutTempTables($con,$data,$race,$temp_table1);
        $response2 = fillOutTempTables($con,$data,$race,$temp_table2);
        
        //foreach record in temporary tables, replace mother or father animal sire number by database_id in the temporary table 1
        $response3 = replaceAnimalIds($con, $temp_table1, $temp_table2, $race);
        
        //insert animals in database
        $response4 = insertIntoDatabase($con, $temp_table1, $lastId);
        
        //DROP TABLES
        dropTables($con, $temp_table1, $temp_table2);
        
        //return successful import
        
        if ($response1[0] == 'error'){
            $response[0] = $response1[0];
            $response[1]['msg'] = $response1[1]['msg'];
            $response[1]['errorMsg'] = 'Error while filling temporary table 1: '. $response1[1]['errorMsg'];
        } elseif ($response2[0] == 'error'){
            $response[0] = $response2[0];
            $response[1]['msg'] = $response2[1]['msg'];
            $response[1]['errorMsg'] = 'Error while filling temporary table 2: '. $response2[1]['errorMsg'];
        } elseif ($response3[0] == 'error'){
            $response[0] = $response3[0];
            $response[1]['msg'] = $response3[1]['msg'];
            $response[1]['errorMsg'] = 'Error while replacing animal IDs: '. $response3[1]['errorMsg'];
        } elseif ($response4[0] == 'error'){
            $response[0] = $response4[0];
            $response[1]['msg'] = $response4[1]['msg'];
            $response[1]['errorMsg'] = 'Error while inserting animals into database: '. $response4[1]['errorMsg'];
        } else {
            $response[0] = 'ok';
            $response[1]['msg'] = 'Les animaux ont bien été insérés dans la base de données !';
            $response[1]['errorMsg'] = '';
        }
        
    } catch (Exception $ex) {
        $response[0] = 'error';
        $response[1]['msg'] = '';
        $response[1]['errorMsg'] = $ex->getMessage();
    }
    return $response;
}

function create_temp_tables($con, $temp_table1, $temp_table2) {
    
    $sql_temp_table1 = "CREATE TABLE {$temp_table1} (
                                `id_animal` int(11) AUTO_INCREMENT PRIMARY KEY,
                                `nom_animal` varchar(50),
                                `sexe` tinyint(1),
                                `no_identification` varchar(13) NOT NULL DEFAULT '0000000000',
                                `date_naiss` date,
                                `reproducteur` tinyint(1),
                                `fecondation` tinyint(1),
                                `coeff_consang` decimal(5,5),
                                `conservatoire` tinyint(1),
                                `valide_animal` tinyint(1),
                                `code_race` varchar(5),
                                `id_pere` varchar(13) DEFAULT '0000000000',
                                `id_mere` varchar(13) DEFAULT '0000000000',
                                `vivant` tinyint(1)
                              ) ENGINE=InnoDB DEFAULT CHARSET=utf8
                              AS (SELECT * FROM animal);";

        $sql_temp_table2 = "CREATE TABLE {$temp_table2} (
                                `id_animal` int(11) AUTO_INCREMENT PRIMARY KEY,
                                `nom_animal` varchar(50),
                                `sexe` tinyint(1),
                                `no_identification` varchar(13) NOT NULL DEFAULT '0000000000',
                                `date_naiss` date,
                                `reproducteur` tinyint(1),
                                `fecondation` tinyint(1),
                                `coeff_consang` decimal(5,5),
                                `conservatoire` tinyint(1),
                                `valide_animal` tinyint(1),
                                `code_race` varchar(5),
                                `id_pere` varchar(13) DEFAULT '0000000000',
                                `id_mere` varchar(13) DEFAULT '0000000000',
                                `vivant` tinyint(1)
                              ) ENGINE=InnoDB DEFAULT CHARSET=utf8
                              AS (SELECT * FROM animal);";
        
    try {
        $con -> beginTransaction();
        $con->query($sql_temp_table1);
        $con->query($sql_temp_table2);
        $lastId = $con->lastInsertId();
        $con->commit();
        return $lastId;
    } catch (Exception $ex) {
        $con->rollBack();
    }
}

function fillOutTempTables($con,$data,$race,$table){
    $decodedData = json_decode($data);

    $response = '';

    try {
        $con -> beginTransaction();

        foreach ($decodedData as $d){
            // Insertion de l'animal né dans la table animal
            $date = $d[5] .'-01-01';
            if (intval($d[6])){
                $etat = 1;
            } else {
                $etat = 0;
            }
            
            $sql = "INSERT INTO {$table} (id_animal, nom_animal, sexe, no_identification, date_naiss, reproducteur, fecondation, coeff_consang, conservatoire, valide_animal, code_race, id_pere, id_mere, vivant)
                VALUES (NULL,'". str_replace("'", "\'", $d[1]) ."',$d[2],'". $d[0] ."','". $date ."',0,0,0,0,0,$race,'$d[3]','$d[4]', $etat)";
            $con -> query($sql);
        }
        //Si pas d'erreur, on peut faire le commit()
        $con -> commit();

    } catch (Exception $e) {
        $con -> rollback();
        $error = $e->getMessage();
    }
    
    if (isset($error)){
        $response[0] = 'error';
        $response[1]['errorMsg'] = $error;
        $response[1]['msg'] = '';
    } else {
        $response[0] = 'ok';
        $response[1]['errorMsg'] = '';
        $response[1]['msg'] = '';
    }

    return $response;
}

function replaceAnimalIds($con, $temp_table1, $temp_table2, $race){
    $sql_read_temp = "SELECT * FROM {$temp_table1}";
    $recordset = $con->query($sql_read_temp);
    
    while ($row = $recordset->fetch()){
        $sql_get_id_dad = "UPDATE {$temp_table1} SET id_pere = (IF(((SELECT id_animal from {$temp_table2} WHERE no_identification='{$row['id_pere']}' AND (sexe=1 OR sexe=3) AND (code_race = {$race} OR code_race IS NULL))IS NOT NULL),(SELECT id_animal from {$temp_table2} WHERE no_identification='{$row['id_pere']}' AND (sexe=1 or sexe=3) AND (code_race = {$race} OR code_race IS NULL)),1)) WHERE id_animal={$row['id_animal']}";
        $sql_get_id_mom = "UPDATE {$temp_table1} SET id_mere = (IF(((SELECT id_animal from {$temp_table2} WHERE no_identification='{$row['id_mere']}' AND sexe=2 AND (code_race = {$race} OR code_race IS NULL)) IS NOT NULL),(SELECT id_animal from {$temp_table2} WHERE no_identification='{$row['id_mere']}' AND sexe=2 AND (code_race = {$race} OR code_race IS NULL)),2)) WHERE id_animal={$row['id_animal']}";
        try {
            $con->beginTransaction();

            $con->query($sql_get_id_dad);
            $con->query($sql_get_id_mom);

            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            $error = 'Error while replacing animal IDs: '. $e->getMessage();
            break;
        }
    }
    if (isset($error)){
        $response[0] = 'error';
        $response[1]['errorMsg'] = $error;
        $response[1]['msg'] = '';
    } else {
        $response[0] = 'ok';
        $response[1]['errorMsg'] = '';
        $response[1]['msg'] = '';
    }
    return $response;
}

function insertIntoDatabase($con, $temp_table1, $lastId){
    $recordset_update = pdo_sql_query($con, "SELECT * FROM {$temp_table1}");            
    
    $valuesAnimal = '';
    $valuesBirth = '';
    $valuesStay = '';
    $valuesDeath = '';
    
    while ($row = $recordset_update->fetch()){
        $currentYear = date('Y-01-01');
        if ($row['id_animal'] > $lastId){    //Ignore rows which are already in the database table "animal"
            $valuesAnimal .= "({$row['id_animal']}, '". str_replace("'", "\'", $row['nom_animal']) ."', {$row['sexe']}, '{$row['no_identification']}', '{$row['date_naiss']}', {$row['reproducteur']}, {$row['fecondation']}, '{$row['coeff_consang']}', {$row['conservatoire']}, {$row['valide_animal']}, {$row['code_race']}, {$row['id_pere']}, {$row['id_mere']}),";
            $valuesBirth .= "(NULL, '{$row['date_naiss']}', NULL, 0, {$row['id_animal']}, NULL, 3),";
            if (!intval($row['vivant'])){
                $valuesStay .= "(NULL, '{$row['date_naiss']}', '{$currentYear}', 0, {$row['id_animal']}, NULL, 2),";
                $valuesDeath .= "(NULL, NULL, '{$currentYear}', 0, {$row['id_animal']}, NULL, 1),";
            } else {
                $valuesStay .= "(NULL, '{$row['date_naiss']}', NULL, 0, {$row['id_animal']}, NULL, 2),";
            }
        }
    }
    $sqlDropKeyConstraintMom = "ALTER TABLE animal DROP FOREIGN KEY fk_mere";
    $sqlDropKeyConstraintDad = "ALTER TABLE animal DROP FOREIGN KEY fk_pere";
    
    $sqlAddConstraintMom = "ALTER TABLE `animal` ADD CONSTRAINT `fk_mere` FOREIGN KEY (`id_mere`) REFERENCES `animal`(`id_animal`) ON DELETE NO ACTION ON UPDATE NO ACTION";
    $sqlAddConstraintDad = "ALTER TABLE `animal` ADD CONSTRAINT `fk_pere` FOREIGN KEY (`id_mere`) REFERENCES `animal`(`id_animal`) ON DELETE NO ACTION ON UPDATE NO ACTION";
    
    if ($valuesDeath != '') {
        $sqlAddDeathPeriods = "INSERT INTO periode VALUES ". substr($valuesDeath, 0, strlen($valuesDeath)-1);
    }
    
    if ($valuesAnimal != '') {
        $sql_update_animal_table = "INSERT INTO animal (`id_animal`, `nom_animal`, `sexe`, `no_identification`, `date_naiss`, `reproducteur`, `fecondation`, `coeff_consang`, `conservatoire`, `valide_animal`, `code_race`, `id_pere`, `id_mere`) VALUES ". substr($valuesAnimal, 0, strlen($valuesAnimal)-1);
        $sqlAddBirthPeriods = "INSERT INTO periode VALUES ". substr($valuesBirth, 0, strlen($valuesBirth)-1);
        $sqlAddStayPeriods = "INSERT INTO periode VALUES ". substr($valuesStay, 0, strlen($valuesStay)-1);

        try {
            $con->beginTransaction();
            $con->query($sqlDropKeyConstraintMom);
            $con->query($sqlDropKeyConstraintDad);
            $con->query($sql_update_animal_table);
            $con->query($sqlAddConstraintMom);
            $con->query($sqlAddConstraintDad);
            $con->query($sqlAddBirthPeriods);
            $con->query($sqlAddStayPeriods);
            if (isset($sqlAddDeathPeriods)){
                $con->query($sqlAddDeathPeriods);
            }
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            $error = 'Error while inserting into database:'. $e->getMessage();
        }
    }
    
    if (isset($error)){
        $response[0] = 'error';
        $response[1]['errorMsg'] = $error;
        $response[1]['msg'] = '';
    } else {
        $response[0] = 'ok';
        $response[1]['errorMsg'] = '';
        $response[1]['msg'] = '';
    }
    
    return $response;
}

function dropTables($con,$temp_table1, $temp_table2){
    $sql_drop_tables = "DROP TABLE IF EXISTS {$temp_table1}, {$temp_table2}";

    try {
        $con->beginTransaction();
        $con->query($sql_drop_tables);
        $con->commit();
    } catch (Exception $ex) {
        $con->rollBack();
    }
}