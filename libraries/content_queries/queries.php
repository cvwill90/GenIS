<?php
/**
 * Created by PhpStorm.
 * User: Christophe_2
 * Date: 01/03/2016
 * Time: 21:15
 */

/**
 * Queries
 * Fonctions qui font des requêtes -> pour ne pas allourdir les pages php et html
 */

require '../fonctions.php';

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

function get_races_especes($con){
    $sql = "SELECT  lib_race, lib_espece FROM race r LEFT JOIN espece e on e.id_espece = r.id_espece";
    $query = pdo_sql_query($con,$sql);
    $result = pdo_query_fetch($query);
    print_r($result);
}

get_races_especes($con);