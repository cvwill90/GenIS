<?php
/**
 * Created by PhpStorm.
 * User: Christophe
 * Date: 08/06/2016
 * Time: 21:35
 */

if (isset($_GET["term"])){
    $term = $_GET["term"];
}

include '../fonctions.php';

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

$sql = "SELECT DISTINCT e.id_elevage, no_elevage, nom_elevage, l.code_race, c.id_contact, c.nom, c.prenom, c.adresse, c.adresse2, c.tel, c.tel2, c.mail, c.notes, com.id_commune, com.lib_commune, com.cp_commune, com.no_dpt, d.no_region 
        FROM contact AS c
        LEFT JOIN elevage AS e ON e.id_elevage = c.id_elevage
        LEFT JOIN link_race_elevage l ON e.id_elevage = l.id_elevage
        LEFT JOIN commune AS com ON com.id_commune = c.id_commune
        LEFT JOIN departement AS d ON d.no_dpt = com.no_dpt
        WHERE e.nom_elevage LIKE '%{$term}%' OR c.nom LIKE '%{$term}%' OR c.prenom LIKE '%{$term}%'
        ORDER BY c.nom ASC";

        //echo $sql;

$query = $con -> query($sql);

$string = "[";

$i=0;

$prevContact = '';

while ($list = $query->fetch()) {
    if ($prevContact == $list['id_contact']){
        $string = $string . ",\"race". $list['code_race'] ."\"";
    } else {
        $string = $string . "]},{\"label\": \"". str_replace('"', '\"', $list['nom']) . ', ' . str_replace('"', '\"', $list['prenom']) . 
                "\", \"id_contact\": \"" . $list['id_contact'] . 
                "\", \"nom\": \"" . str_replace('"', '\"', $list['nom']) . 
                "\", \"prenom\": \"" . str_replace('"', '\"', $list['prenom']) . 
                "\", \"adresse\": \"" . str_replace('"', '\"', $list['adresse']) . 
                "\", \"adresse2\": \"" . str_replace('"', '\"', $list['adresse2']) . 
                "\", \"tel\": \"" . $list['tel'] . 
                "\", \"tel2\": \"" . $list['tel2'] . 
                "\", \"no_region\": \"" . $list['no_region'] . 
                "\", \"no_dpt\": \"" . $list['no_dpt'] . 
                "\", \"id_commune\": \"" . $list['id_commune'] . 
                "\", \"lib_commune\": \"" . str_replace('"', '\"', $list['lib_commune']) . 
                "\", \"cp_commune\": \"" . $list['cp_commune'] . 
                "\", \"mail\": \"" . $list['mail'] . 
                "\", \"notes\": \"" . str_replace('"', '\"', $list['notes']) . 
                "\", \"id_elevage\": \"" . $list['id_elevage'] . 
                "\", \"nom_elevage\": \"" . str_replace('"', '\"', $list['nom_elevage']) . 
                "\", \"no_elevage\": \"" . $list['no_elevage'] . 
                "\", \"races\": [";
        if ($list['code_race'] != ''){
            $string .= "\"race". $list['code_race'] ."\"";
        }
    }
    $prevContact = $list['id_contact'];
    $i++;
}

$string = $string. "]";

$trimmed = "[". substr(str_replace(",]","]", $string),4) .'}]';

//Get out the newlines in the json string and replace by \n
$json_with_eol = $trimmed;
$i = 0;
$string_parts = [];
while ($eol_pos = strpos($json_with_eol, PHP_EOL)) {
    $string_parts[$i] = substr($json_with_eol, 0, $eol_pos-1);
    $json_with_eol = substr($json_with_eol, $eol_pos+2);
    $i++;
}
$string_parts[$i] = substr($json_with_eol, 0);

echo implode('\n', $string_parts);
