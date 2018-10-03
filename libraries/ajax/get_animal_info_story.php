<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../fonctions.php';

header('Content-type: text/html; charset=UTF-8');

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

$data = filter_input_array(INPUT_GET);

if (isset($data['term'])){
    $animal = $data["term"];
}

if (isset($data['race'])){
    $race = $data["race"];
}

$sql = "SELECT a.id_animal, a.nom_animal, a.sexe, lg.lib_livre AS livre_gene, r.lib_race, a.coeff_consang, a.no_identification, a.date_naiss, p.no_identification AS no_identification_pere, p.nom_animal as nom_pere, m.no_identification as no_identification_mere, m.nom_animal as nom_mere, e.id_elevage, e.nom_elevage, tp.lib_type, periode.id_periode, periode.date_entree, periode.date_sortie "
        . "FROM animal AS a "
        . "INNER JOIN animal AS p ON p.id_animal = a.id_pere "
        . "INNER JOIN animal AS m ON m.id_animal = a.id_mere "
        . "INNER JOIN periode ON periode.id_animal = a.id_animal "
        . "LEFT JOIN elevage AS e ON periode.id_elevage = e.id_elevage "
        . "INNER JOIN race AS r ON r.code_race = a.code_race "
        . "INNER JOIN type_periode AS tp ON tp.id_type = periode.id_type "
        . "LEFT JOIN livre_genealogique AS lg ON lg.id_livre = a.id_livre "
        . "WHERE (a.nom_animal LIKE '%". $animal ."%' OR a.no_identification LIKE '%". $animal ."%')";

if ($race != 'null'){
    $sql .= " AND a.code_race=" . $race;
}

$sql .=   " ORDER BY a.nom_animal ASC, id_animal ASC, periode.id_periode DESC";
//echo $sql;
$result = $con->query($sql);

$i = -1;
$j = 0;
$rs = array();
$previous_animal = '';

$info = array('id_animal','nom_animal','sexe','lib_race','coeff_consang','no_identification','date_naiss','no_identification_pere','nom_pere','no_identification_mere','nom_mere', 'livre_gene');
$story = array('id_elevage', 'nom_elevage','lib_type','date_entree','date_sortie');

while ($record = $result->fetch()) {
    if ($previous_animal != $record['id_animal']){
        $j = 0;
        $i++;
        foreach ($record as $rk => $rv){
            if (in_array(strval($rk), $info)){
                $rs[$i]['data'][$rk] = replace_sql_null_values($rk, $rv);
            }
        }
        $lignee = getNomAncetre($con, $rs[$i]['data']['id_animal'], 1);
        $famille = getNomAncetre($con, $rs[$i]['data']['id_animal'], 2);
        $rs[$i]['data']['lignee'] = $lignee;
        $rs[$i]['data']['famille'] = $famille;
        $rs[$i]['label'] = $rs[$i]['data']['no_identification'] . ' - ' . $rs[$i]['data']['nom_animal'];
        $rs[$i]['value'] = $rs[$i]['data']['nom_animal'];
    }
    
    foreach ($record as $rk => $rv){
        if (in_array(strval($rk), $story)) {
            $rs[$i]['story'][$j][$rk] = $rv;
        }
    }
    $previous_animal = $record['id_animal'];
    $j++;
}

//print_r($rs);
echo json_encode($rs);