<?php
/*
 * Meme fichier pour l'autocompl�tion m�le eet femelle
 */

require_once '../fonctions.php';

if (isset($_GET["sex"])) {
    $sex = intval($_GET["sex"]);
} else {
    $sex = '1,2,3';
}

if (isset($_GET["race"])) {
    $race = intval($_GET["race"]);
} else {
    $race = 0;
}

if (isset($_GET["term"])) {
    $animal = $_GET["term"];
} else {
    $animal = '';
}

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);


$sqlselect = "SELECT no_identification, nom_animal, id_animal FROM animal WHERE sexe IN ({$sex}) AND (no_identification LIKE \"%". $animal ."%\" OR nom_animal LIKE \"%". $animal ."%\") ";
if ($race) {
    $sqlselect .= "AND code_race = {$race} ";
}
$sqlselect .= "ORDER BY nom_animal ASC LIMIT 0, 10";

$query = $con -> query($sqlselect);


$string = "[";

$i=0;

while ($list = $query->fetch()) {
    $ancetre = getNomAncetre($con, intval($list[2]), $sex);
    if (isset($_GET['max'])){
            $max = $_GET['max'];
            if ($i < $max) {
                    $string = $string . "{\"label\": \"" . $list['no_identification'] . ' - ' . $list['nom_animal'] . "\", \"value\": \"" . $list['nom_animal'] . "\", \"id\": \"" . $list['id_animal'] . "\", \"ancetre\": \"" . $ancetre . "\"},";
                    $i++;
            } else {
                    break;
            }
    } else {
            $string = $string . "{\"label\": \"" . $list['no_identification'] . ' - ' . $list['nom_animal'] . "\", \"value\": \"" . $list['nom_animal'] . "\", \"id\": \"" . $list['id_animal'] . "\", \"ancetre\": \"" . $ancetre . "\"},";
    }
}

$string = $string. "]";

$trimmed = str_replace(",]","]", $string);
echo $trimmed;
