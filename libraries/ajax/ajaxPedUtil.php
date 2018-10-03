<?php

include '../fonctions.php';

$req = $_GET["reqFile"]; // résultat de la requête sur les animaux à sélectionner
$ref = $_GET["refFile"]; // fichier de référence comportant seulement la colonne avec les identifiants des animaux
$f_sortie = $_GET["outputFile"]; // fichier de sortie du programme ped_util
$nb_gen = $_GET["maxGen"]; // paramètre nom/BRe de générations à prendre en compte récupéré dans le formulaire
$nb_par = $_GET["param"]; // nom/BRe de paramètres à prendre en compte (récupéré dans le formulaire précédent)
$elim_pedigree = $_GET["pedigree"]; // paramètre éliination de pedigree inutile (récupéré dans le formulaire précédent)

$race = $_GET['race'];


$fp = fopen("C:/wamp64/www/genis.cra/calculs/pedigFiles/lancement_ped_util.txt", "w+"); // création et/ou modification d'un fichier texte, ici le fichier .txt contient les informations à envoyer à ped_util pour qu'il s'execute tout seul
fputs($fp, "C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $req); // 1ere ligne du fichier texte
fputs($fp,"\r\n");// on va à la ligne
fputs($fp, "C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $ref);// 2nd ligne du fichier texte
fputs($fp,"\r\n");
fputs($fp, "C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $f_sortie);
fputs($fp,"\r\n");
fputs($fp, $nb_gen);
fputs($fp, "\r\n");
fputs($fp, $nb_par);
fputs($fp, "\r\n");
fputs($fp, $elim_pedigree); // dernière ligne du fichier texte
fputs($fp, "\r\n");
fclose($fp); //on ferme le fichier et on l'enregistre

$output = `C:\wamp64\www\genis.cra\libraries\pedigModules\ped_util.exe < C:\wamp64\www\genis.cra\calculs\pedigFiles\lancement_ped_util.txt`; // lancement de ped_util à partir du fichier .txt créé au dessus

//Teste si une erreur est survenue

$error_needle = "Message d erreur";
$error_pos = strpos($output,$error_needle);

store_animal_dictionary($f_sortie, $race);

if ($error_pos){
    $error_message = mb_convert_encoding(substr($output,$error_pos+36),'ISO-8859-1');
    echo '{"status":"problem","error_message":"'. $error_message .'"}';
} elseif (strlen($output) < 2000) {
    echo '{"status":"problem","error_message":"Aucun résultat retourné par ped_util.exe"}';
} else {
    echo '{"status":"ok"}';
}

function store_animal_dictionary($sortie, $race){
    $pedFile = fopen(PROJECT_ROOT . "\\calculs\\pedigFiles\\". $sortie,"r");

    $no_ident_table = array(0 => ['0000000000', 'Parent Inconnu']);

    while (($data = fgets($pedFile, 115)) !== false) {	
        $test = remove_spaces($data);
        $clean_data = preg_replace('{(\s)\1+}', '$1', $test);
        $array = explode(' ', $clean_data);
        $pedig_id = $array[0];
        $no_ident_table[$pedig_id] = array();
        $no_ident_table[$pedig_id][0] = $array[7];
        $nom_animal = get_name_animal($array[7], $race);
        $no_ident_table[$pedig_id][1] = $nom_animal;
    }
    
    $fd = fopen(PROJECT_ROOT .'\libraries\pedigModules\dict_ped_util.json', 'w+');
    fwrite($fd, json_encode($no_ident_table));
    fclose($fd);
}

function get_name_animal($no_id, $race){
    $con = pdo_connection(HOST_DB, DB_NAME, USER_DB, PW_DB);
    
    $result = $con->query("SELECT nom_animal FROM animal WHERE no_identification={$no_id} AND code_race={$race}");
    
    $nom_animal = $result->fetch()[0];
    
    return $nom_animal;
}