<?php

require '../fonctions.php';

header('Content-type: text/html; charset=UTF-8');

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

$reg = $_GET["region"];

$sql = "SELECT no_dpt,lib_dpt FROM departement d
		WHERE no_region = $reg
		ORDER BY lib_dpt asc";

$query = pdo_sql_query($con,$sql);

$string = "[";

while ($result = $query->fetch()) {
    $value = $result["no_dpt"];
    $label = $result["lib_dpt"];
    $string = $string. '{"value": "'. $value .'","label": "'. $label . '"},';
}

$string = $string. "]";

$trimmed = str_replace(",]","]", $string);

echo $trimmed;