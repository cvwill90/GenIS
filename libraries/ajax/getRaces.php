<?php

require '../fonctions.php';

header('Content-type: text/html; charset=UTF-8');

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

$espece = $_GET["espece"];

$sql = "SELECT * FROM race";

$sql = "SELECT code_race, lib_race FROM race r
		WHERE id_espece = $espece
		ORDER BY lib_race asc";

$query = pdo_sql_query($con,$sql);

$string = "[";

while ($result = $query -> fetch()) {
    $value = $result["code_race"];
    $label = $result["lib_race"];
    $string = $string. '{"value": "'. $value .'","label": "'. $label . '"},';
}

$string = $string. "]";

$trimmed = str_replace(",]","]", $string);

echo $trimmed;