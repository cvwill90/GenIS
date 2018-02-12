<?php
/**
 * Created by PhpStorm.
 * User: Christophe
 * Date: 23/05/2016
 * Time: 23:56
 */

$ville = $_GET["term"];

include '../fonctions.php';

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

$sql = "SELECT id_commune,lib_commune,cp_commune FROM commune WHERE lib_commune LIKE \"%". $ville ."%\" ORDER BY lib_commune ASC LIMIT 0, 10";

$query = $con -> query($sql);

$string = "[";

$i=0;

while ($list=$query->fetch()) {
    $string = $string . "{\"value\":\"". $list['id_commune'] ."\",\"label\": \"" . $list['lib_commune'] . "\",\"zip\": \"" . $list['cp_commune'] . "\"},";
}

$string = $string. "]";

$trimmed = str_replace(",]","]", $string);
echo $trimmed;