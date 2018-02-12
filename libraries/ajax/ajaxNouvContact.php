<?php
/**
 * Created by PhpStorm.
 * User: Christophe
 * Date: 05/06/2016
 * Time: 14:29
 */

//header("Location:http://localhost/joomla/index.php?option=com_content&view=article&id=32");

include '../fonctions.php';

//Facultatif: Attribution d'une variable à chaque paramètre envoyé par le formulaire

if(isset($_GET["prenom"])){
    $firstName=$_GET["prenom"];
}

if(isset($_GET["nom"])){
    $lastName=$_GET["nom"];
}

if(isset($_GET["adresse"])){
    $addressPersoA = $_GET["adresse"];
}

if(isset($_GET["adresseCompl"])){
    $addressPersoB = $_GET["adresseCompl"];
}

if(isset($_GET["codePostal"])){
    $cp = $_GET["codePostal"];
}

if(isset($_GET["ville"])){
    $townName = mb_strtoupper($_GET["ville"],'UTF-8');
}

if(isset($_GET["mail"])){
    $mail = $_GET["mail"];
}

if(isset($_GET["tel1"])){
    $tel1 = $_GET["tel1"];
}

if(isset($_GET["tel2"])){
    $tel2 = $_GET["tel2"];
}

if(isset($_GET["idVille"])){
    $id_commune = $_GET["idVille"];
}

if (isset($_GET["departement"])){
    $dep = $_GET["departement"];
}

if (isset($_GET['notes'])){
    $notes = $_GET['notes'];
}

if(isset($_GET["eleveur"])){
    $elev = $_GET["eleveur"];
    if($elev == 1){
        if (isset($_GET["nomElevage"]))
            $nomElevage = $_GET["nomElevage"];
        if (isset($_GET["idElevage"]))
            $idElevage = $_GET["idElevage"];
        if (isset($_GET["idDbElevage"])){
            $idDbElevage = $_GET["idDbElevage"];
        }
    }
}

if (isset($_GET["races"])){
    $races = $_GET["races"];
}else{
    $races = [];
}

// Connexion à la BDD

	$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

    try {
        $con -> beginTransaction();

        if ($id_commune == 1){
            $sqlCommune = "INSERT INTO bdd_genis.commune (id_commune,lib_commune,cp_commune,no_dpt)
                            VALUES (NULL,'{$townName}','{$cp}','{$dep}')";

            $queryCommune = $con->query($sqlCommune);

            $id_commune = $con->lastInsertId();
        }
        
        if($elev == 0){ //Si le contact n'a pas d'élevage
            $sqlContact ="INSERT INTO bdd_genis.contact (id_contact, nom, prenom, adresse, adresse2, tel, tel2, mail, id_commune, notes)
			              VALUES (NULL,'$lastName','$firstName','$addressPersoA','$addressPersoB','$tel1','$tel2','$mail','$id_commune','$notes')";

            $query = $con -> query($sqlContact);
        } else {    //Si le contact a un élevage
            if ($idDbElevage == ''){    //Si l'élevage est inexistant --> on en crée un nouveau
                $sqlElevage = "INSERT INTO bdd_genis.elevage (id_elevage,nom_elevage,no_elevage)
                            VALUES (NULL,'$nomElevage','$idElevage')";

                $queryElevage = $con->query($sqlElevage);
                
                $idDbElevage = $con->lastInsertId();
            }

            //Comparer les races existentes pour l'id elevage en question avec les races qui ont été renseignées dans le formulaire
            
            //Récupérer toutes les races de la table link_race_elevage pour l'élevage en question
            $sqlGetRacesElevage = "SELECT code_race FROM link_race_elevage WHERE id_elevage = {$idDbElevage}";
            
            $dbRaces = $con->query($sqlGetRacesElevage);
            
            //Placer le résultat dans un tableau
            $dbRacesArray = array();
            while ($code_race = $dbRaces->fetch()){
                array_push($dbRacesArray, intval($code_race[0]));
            }
            
            //Si une race de la base de données est en trop par rapport aux races du formulaire, c'est qu'elle a été supprimée
            foreach ($dbRacesArray as $race){
                if (!in_array($race, $races)){
                    $sqldeleteRaceFromFarm = "DELETE FROM ". DB_NAME .".link_race_elevage WHERE id_elevage = {$idDbElevage} AND code_race = {$race}";
                    $con->query($sqldeleteRaceFromFarm);
                }
            }
            
            //Si une race du formulaire est en trop par rapport aux races de la base de données, c'est qu'elle a été ajoutée
            foreach ($races as $race){
                if (!in_array($race, $dbRacesArray)){
                    $sqlAddRaceToFarm = "INSERT INTO ". DB_NAME .".link_race_elevage VALUES ({$idDbElevage}, {$race})";
                    $con->query($sqlAddRaceToFarm);
                }
            }
            
            //On ajoute enfin le contact
            $sqlContact = "INSERT INTO bdd_genis.contact 
                        (id_contact, nom, prenom, adresse, adresse2, tel, tel2, mail, id_commune, notes, id_elevage)
                        VALUES (NULL,'$lastName','$firstName','$addressPersoA','$addressPersoB','$tel1','$tel2','$mail','$id_commune','$notes','$idDbElevage')";

            $queryContact = $con->query($sqlContact);
        }

        //Pas d'erreur jusque là donc on peut faire le commit()
        $transactioncommit= $con -> commit();

    } catch (Exception $e) {
        $con -> rollback();
        $error_message = $e->getMessage();
    }

if (isset($error_message)){
    $err = $con -> errorCode();
    $response['statusMsg'] = "error";
    $response['response']['errorMsg'] = $error_message;
} else {
    $response['statusMsg'] = "ok";
    $response['response']['msg'] = "Le nouveau contact a bien été enregistré dans la base de données.";
}

echo json_encode($response);

