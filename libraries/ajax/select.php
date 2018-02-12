<?php

include '../../libraries/fonctions.php';

//obligatoire
$race = $_GET["race"];

//optionnel
$minBirth = $_GET["minBirthDate"];
$maxBirth = $_GET["maxBirthDate"];
$repro = $_GET["repro"];
$state = $_GET["etat"];
//$sexe = $_GET["animalSex"];
//$farm = $_GET["hidElevage"];
//$start = $_GET["startPeriod"];
//$end = $_GET["endPeriod"];

// Information n�cessaire pour ped_util.exe


/*
 * Requ�te reprenant l'intégralité des animaux présents dans la base de donn�es
 *
 * => On va s'en servir pour piocher dedans selon les crit�res impos�s par l'utilisateur
 */

$sqlPedigree = "SELECT DISTINCT a.id_animal AS id, 
                            a.no_identification AS no_identification,
                            p.no_identification AS no_id_pere, 
                            m.no_identification AS no_id_mere,
                            a.date_naiss AS date_naissance, 
                            a.sexe AS sex									
                        FROM animal AS a
                        JOIN animal AS p ON a.id_pere = p.id_animal
                        JOIN animal AS m ON a.id_mere = m.id_animal
                        WHERE a.code_race = '". $race ."'";

/*
 * On traite le cas du choix du sexe
 * Si aucun sexe n'est choisi ($sexe =3), la requete reste la meme
 */
/*
if ($sexe == 1) {
    $sqlSexe = "SELECT * FROM  ({$sqlPedigree}) AS b WHERE sex={$sexe}";
} elseif ($sexe == 2){
    $sqlSexe = "SELECT * FROM  ({$sqlPedigree}) AS b WHERE sex={$sexe}";
} elseif ($sexe == 3){
    $sqlSexe = "SELECT * FROM  ({$sqlPedigree}) AS b WHERE sex={$sexe}";
} else {
    $sqlSexe = "SELECT * FROM (". $sqlPedigree .") AS b";
}
*/
/*
 * On traite le cas de la date de naissance
 */


if ($maxBirth =='' AND $minBirth != '') {
        $sqlBirth = "SELECT * FROM (". $sqlPedigree .") AS ped WHERE date_naissance >= '". $minBirth ."'";
} elseif ($minBirth =='' AND $maxBirth != '') {
        $sqlBirth = "SELECT * FROM (". $sqlPedigree .") AS ped WHERE date_naissance <= '". $maxBirth ."'";
} elseif ($maxBirth !='' AND $minBirth != '') {
        $sqlBirth = "SELECT * FROM (". $sqlPedigree .") AS ped WHERE date_naissance BETWEEN '". $minBirth ."' AND '". $maxBirth ."'";
} else {
        $sqlBirth = "SELECT * FROM (". $sqlPedigree .") AS ped";
}

/*
* On traite selon que l'animal est reproducteur ou non
*/

if ($repro == 1) {
    $sqlRepro = "SELECT * FROM (". $sqlBirth .") AS naiss WHERE sex=1 OR sex=2";
} else {
    $sqlRepro = "SELECT * FROM (". $sqlBirth .") AS naiss WHERE sex=3";
}

/*
 * On traite le cas selon lequel l'animal est mort ou vivant
 */

if ($state == 1) {
        $sqlEtat = "SELECT * FROM (". $sqlRepro. ") AS repro 
                                LEFT JOIN (SELECT DISTINCT id_animal FROM periode WHERE id_type = 1) AS mort 
                                ON repro.id = mort.id_animal 
                                WHERE mort.id_animal IS NULL";
} elseif ($state ==2) {
        $sqlEtat = "SELECT * FROM (". $sqlRepro. ") AS repro 
                                JOIN (SELECT DISTINCT id_animal FROM periode WHERE id_type = 1) AS mort 
                                ON repro.id = mort.id_animal";
} else {
        $sqlEtat = $sqlRepro;
}

/*
 * On traite le cas o� on s'int�resse aux animaux d'un �levage en particulier et pendant quelle p�riode
 */
/*
if ($farm) {
        if ($start !='' AND $end != '') {
                $sqlFarm = "SELECT * FROM (". $sqlEtat .") AS f
                                JOIN periode AS p ON f.id=p.id_animal
                                WHERE id_type = '2' AND id_elevage = '". $farm ."'
                                                                        AND (date_entree BETWEEN '". $start ."' AND '". $end ."' 
                                                                                OR date_sortie BETWEEN '". $start ."' AND '". $end ."')";
        } elseif ($start =='' AND $end != '') {
                $sqlFarm = "SELECT * FROM (". $sqlEtat .") AS f
                                JOIN periode AS p ON f.id=p.id_animal
                                WHERE id_type = '2' AND id_elevage = '". $farm ."'
                                                                        AND date_entree <= '". $end ."'";
        } elseif ($start !='' AND $end == '') {
                $sqlFarm = "SELECT * FROM (". $sqlEtat .") AS f
                                JOIN periode AS p ON f.id=p.id_animal
                                WHERE id_type = '2' AND id_elevage = '". $farm ."'
                                                                        AND date_sortie >= '". $start ."'";
        } else {
                $sqlFarm = "SELECT * FROM (". $sqlEtat .") AS f";
        }
} else {
        $sqlFarm = "SELECT * FROM (". $sqlEtat .") AS f";
}*/

/*
 * A cause de $sqlFarm, il se peut qu'un animal apparaisse deux fois dans le r�sultat (2 s�jours dans le meme �levages
 * Une derniere requete est n�cessaire pour ne tirer  que les colonnes souhait�es et des enregistrements uniques
 */
	
$sqlReference = "SELECT DISTINCT final.no_identification, final.no_id_pere, final.no_id_mere, final.date_naissance, final.sex FROM (". $sqlEtat .") AS final";

/*
 * requete pour donner un nom au fichier .txt cr�� plus bas
 */	
	
$sqlRace = "SELECT r.abbrev FROM race AS r WHERE r.code_race = '". $race ."'";


/*
 * Starting connection to database
 */

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

//transaction pour les deux requetes

try {

    $con -> beginTransaction();

    $query_pedigree = $con->query($sqlPedigree);
    $query_reference = $con -> query($sqlReference);

    $queryRace = $con -> query($sqlRace);
    $resultRace = $queryRace -> fetch();
    $race_abbrev = $resultRace['abbrev'];
    $ped_name = "req_". $resultRace['abbrev'] .".txt";
    $ref_name = "ref_". $resultRace['abbrev'] .".txt";


    $transCommit = $con -> commit();

} catch (Exception $e) {
    $con -> rollback();
}

if (!isset($e)){

    /*
    * On enregistre le résultat de la requete dans un fichier txt selon l'organisation exig�e par ped_util.exe
    */

    //Ouverture/Création des fichiers de la race concernée

    $fp_ped = fopen("C:/wamp64/www/genis.cra/calculs/pedigFiles/". $ped_name, "w+");
    $fp_ref = fopen("C:/wamp64/www/genis.cra/calculs/pedigFiles/". $ref_name, "w+");

    //On parcourt le recordset pour écrire chaque ligne dans les fichier txt

    $nb_anim_ref = 0;
    while ($result_ref = $query_reference -> fetch()) {
        fputs($fp_ref, "". $result_ref['no_identification'] ."");
        fputs($fp_ref, "\r\n");
        $nb_anim_ref++;
    }

    while ($result_ped = $query_pedigree -> fetch()) {
        fputs($fp_ped, "". $result_ped['no_identification'] ."\t". $result_ped['no_id_pere'] ."\t". $result_ped['no_id_mere'] ."\t". substr($result_ped['date_naissance'],0,4) ."\t". $result_ped['sex'] ."\t1\t0");
        fputs($fp_ped, "\r\n");
    }

    //fermeture des fichiers

    fclose($fp_ped);
    fclose($fp_ref);

    //on fait test pour voir si la requete s'est bien exécutée
    //si oui, alors on renvoit en json les noms des fichiers créés (ref et req)


    if (!$nb_anim_ref){
        echo '{"status":"error","message":"Le fichier de référence de ped_util.exe ne contient aucun animal. Aucun animal correspondant aux critères de sélection n\'a été trouvé dans la base de données"}';
    } else {
        echo '{"status": "ok", "ped":"'. $ped_name .'","ref":"'. $ref_name .'","race":"'. $race_abbrev .'","message":"Les fichiers ont bien été créés"}';
    }
    
} else {
    echo '{"status":"error","message":"Une erreur est survenue lors de la requête: '. $e .'"}';
}