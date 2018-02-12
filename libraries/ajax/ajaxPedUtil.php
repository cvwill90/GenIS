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

create_pedig_dict_files($f_sortie, $race);

if ($error_pos){
    $error_message = mb_convert_encoding(substr($output,$error_pos+36),'ISO-8859-1');
    echo '{"status":"problem","error_message":"'. $error_message .'"}';
} elseif (strlen($output) < 2000) {
    echo '{"status":"problem","error_message":"Aucun résultat retourné par ped_util.exe"}';
} else {
    echo '{"status":"ok"}';
}

function create_pedig_dict_files($sortie, $race){
    $pedFile = fopen(PROJECT_ROOT . "calculs\\pedigFiles\\". $sortie,"r");
    //$pedFile = fopen("C:\wamp\www\Genis\SiteWeb\Calculs\Pedig\ped_animaux.csv","r");

    $no_ident_table = array();

    while (($data = fgets($pedFile, 115)) !== false) {
        $data =str_replace(" ",";",$data);				//Remplacement de tous les caract�res " " par des ";"

        for ($i=12; $i>1; $i--) {						//On commence � $i=12 car le nombre d'espaces cons�cutifs peut aller jusqu'� 10 dans ped_...csv
            $str = ";";									//    Je mets 12 pour etre sur
            $j=0;
            while ($j<$i) {
                    $str = $str.";";
                    $j++;									// Comme un un "\t" est fait de plusieurs espaces, on obtient des s�ries
            }											// 		s�ries de ";" inutiles => on r�duit leur nombre
            $data = str_replace($str,";",$data);
        }
        $data = substr($data,1,100);					// Il faut enleveer le ";" au d�but de la cha�ne
        $array = explode(";",$data);					// on segmente la chaine de caract�res
        $pedig_id = strval($array[0]);
        $no_ident_table[$pedig_id] = array();						// on met l'id attribu� par pedig et le num�ro d'identification
        $no_ident_table[$pedig_id][0] = $array[7];                                             // dans un m�me tableau
        $nom_animal = get_name_animal($array[7], $race);
        $no_ident_table[$pedig_id][1] = $nom_animal;
    }
    
    $no_ident_table['0'] = ['0000000000', 'Inconnu'];
    
    $fd = fopen('C:\wamp64\www\genis.cra\libraries\pedigModules\dict_ped_util.json', 'w+');
    fwrite($fd, json_encode($no_ident_table));
    fclose($fd);
}

function get_name_animal($no_id, $race){
    $con = pdo_connection(HOST_DB, DB_NAME, USER_DB, PW_DB);
    
    $result = $con->query("SELECT nom_animal FROM animal WHERE no_identification={$no_id} AND code_race={$race}");
    
    $nom_animal = $result->fetch()[0];
    
    return $nom_animal;
}