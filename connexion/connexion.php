<?php
/**
 * Created by PhpStorm.
 * User: Christophe_2
 * Date: 22/02/2016
 * Time: 23:30
 */
session_start();

require_once '../libraries/constants.php';
require_once PROJECT_ROOT . '/libraries/fonctions.php';

$pw = $_POST['password'];
$user = $_POST['user'];

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

//$sql = "SELECT con.id_contact, com.id_compte, p.id_privilege FROM contact con LEFT JOIN compte com ON con.id_contact=com.id_contact LEFT JOIN privilege p ON  p.id_privilege=com.id_privilege WHERE identifiant='$user' AND mdp='$pw'";
$sql = "SELECT com.id_compte, p.id_privilege FROM compte com LEFT JOIN privilege p ON  p.id_privilege=com.id_privilege WHERE identifiant='$user' AND mdp='$pw'";

$query = pdo_sql_query($con,$sql);

$result = pdo_query_fetch($query);

if (count($result)==0){
    echo 'Pas de correspondance dans la bdd';
} else {
    //$_SESSION['contact'] = $result[0];
    $_SESSION['compte']= $result[1];
    $_SESSION['privilege'] = $result[2];
    $_SESSION['utilisateur'] = $user;

    header('Location: ../index.php');
    exit();
}

