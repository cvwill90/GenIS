<?php
$term = $_GET["term"];

include '../fonctions.php';

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

$sql = "SELECT DISTINCT e.id_elevage, no_elevage, nom_elevage, r.lib_race, r.code_race, c.nom FROM elevage AS e
		LEFT JOIN contact AS c ON e.id_elevage = c.id_elevage
		LEFT JOIN link_race_elevage l ON e.id_elevage = l.id_elevage
		LEFT JOIN race r ON r.code_race = l.code_race
                WHERE e.nom_elevage LIKE '%{$term}%' OR c.nom LIKE '%{$term}%' OR e.no_elevage LIKE '%{$term}%'
		ORDER BY c.nom ASC";
        
$query = $con -> query($sql);

$string = "";

$i=0;
$prevFarm='';

while ($list = $query->fetch()) {
    if ($prevFarm == $list['id_elevage']){
        $string = $string . ",\"race". $list['code_race'] ."\"";
    }else{
        if ($list['no_elevage'] != ''){
            $string = $string . "]},{\"label\": \"" . $list['no_elevage'] . ' - ' . $list['nom_elevage'] . "\", \"id\": \"" . $list['id_elevage'] . "\", \"nom_elevage\": \"" . $list['nom_elevage'] . "\", \"no_elevage\": \"" . $list['no_elevage'] . "\", \"races\": [\"race" . $list['code_race'] . "\"";
        } else {
            $string = $string . "]},{\"label\": \"" . $list['nom_elevage'] . "\", \"id\": \"" . $list['id_elevage'] . "\", \"nom_elevage\": \"" . $list['nom_elevage'] . "\", \"no_elevage\": \"" . $list['no_elevage'] . "\", \"races\": [\"race" . $list['code_race'] . "\"";
        }
    }
    $prevFarm = $list['id_elevage'];
    $i++;
}

$string = $string. "]}]";

$trimmed = "[". substr($string,3);
echo $trimmed;