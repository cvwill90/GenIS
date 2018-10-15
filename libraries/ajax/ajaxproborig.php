<?php

include '../fonctions.php';

/*
 * Récupération des paramètres
 */

$race = $_GET['key1'];
$ped_util = $_GET['key2'];
$sortie_contrib = $_GET['key3'];
$sortie_list = $_GET['key8'];
$nb_anc = $_GET['key4'];
$sex = $_GET['key5'];
$year1 = $_GET['key6'];
$year2 = $_GET['key7'];


//Ecriture de lancement_prob_orig.txt

$fp = fopen("C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\lancement_prob_orig.txt", "w+"); // création et/ou modification d'un fichier texte, ici le fichier .txt contient les informations � envoyer � prob_orig pour qu'il s'execute tout seul

fputs($fp, "'C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $ped_util ."'\r\n"); // 1ere ligne du fichier texte
fputs($fp, "'". PEDIG_DUMP_FOLDER ."\\prob_orig\\". $sortie_contrib ."'\r\n");
fputs($fp, $nb_anc ."\r\n");
fputs($fp, $sex ."\r\n");
fputs($fp, $year1 ."\r\n");
fputs($fp, $year2 ."\r\n");

fclose($fp);

/*
 * Vérification de l'existence du dossier de destination
 */
$destination = PEDIG_DUMP_FOLDER . "\\prob_orig\\";
 
if (!is_dir($destination)){
    mkdir($destination, 0777, true);
}

/*
 * Exécution de prob_orig
 */
$test = array();
$output = exec('C:\\wamp64\\www\\genis.cra\\libraries\\pedigModules\\prob_orig.exe < C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\lancement_prob_orig.txt', $test); // lancement de ped_util � partir du fichier .txt cr�� au dessus

/*
 * Dans le cas de Prob_orig.exe, on travaille à partir du fichier retourné
 * dans le fichier list_ancestors
*/

$file_ancestors = 'list_ancestors';
$f_list_anc = fopen($file_ancestors, 'r');

$list_anc = fread($f_list_anc, filesize($file_ancestors));

$list_anc2 =str_replace(" ",";",$list_anc);

for ($i=10; $i>1; $i--) {
    $str = ";";
    $j=0;
    while ($j<$i) {
        $str = $str.";";
        $list_anc2 = str_replace($str,";",$list_anc2);
        $j++;
    }
}

$list_anc_array = explode(";",$list_anc2);		//on segmente la chaine en array

$nb_lignes = count($list_anc_array);
$nb_arrays = ($nb_lignes-8)/12;

$list_anc_tab = array();

$k=1;

for ($i=0; $i<$nb_arrays; $i++) {
    for ($j=0; $j<2; $j++) {
        $list_anc_tab[$i][$j] = $list_anc_array[$k];
        $k++;
    }
    $list_anc_tab[$i][2] = "0000000000";            //Création d'un champ vide devant contenir le no d'identification de l'animal
    $list_anc_tab[$i][3] = "";                      //Création d'un champ vide devant contenir le nom de l'animal
    for ($j=4; $j<14; $j++) {
        $list_anc_tab[$i][$j] = $list_anc_array[$k];
        $k++;
    }
    $list_anc_tab[$i][14] = "";
    $list_anc_tab[$i][15] = "";
    $list_anc_tab[$i][16] = "";
    $list_anc_tab[$i][17] = "";
}

$fd = fopen(PROJECT_ROOT ."/libraries/pedigModules/dict_ped_util.json", "r");

$animal_dict = json_decode(fread($fd, filesize(PROJECT_ROOT ."\\libraries\\pedigModules\\dict_ped_util.json")));

    /*foreach ($tab2 as $t) {
     echo $t[0] .' '. $t[1] .'<br>';					// juste pour avoir un apercu
     }*/

fclose($fd);

foreach ($list_anc_tab as $key => &$row) {
    $pedig_id_animal = strval($row[0]);
    $db_info_animal = $animal_dict[$pedig_id_animal];
    $row[2] = $db_info_animal[0];
    $row[3] = $db_info_animal[1];
    $pedig_id_pere = strval($row[6]);
    $db_info_pere = $animal_dict[$pedig_id_pere];
    $row[14] = $db_info_pere[0];
    $row[15] = $db_info_pere[1];
    $pedig_id_mere = strval($row[7]);
    $db_info_mere = $animal_dict[$pedig_id_mere];
    $row[16] = $db_info_mere[0];
    $row[17] = $db_info_mere[1];
}

unset($row);

/*
 * Ecriture du contenu de la console dans un fichier
 */

$result = fopen($destination . $sortie_list,"w+");
fputs($result, mb_convert_encoding(";;;;;Contributions;;Pere;;Mere;;;Nb effectif d'ancêtres\r\n", 'UTF-16LE', 'UTF-8'));
fputs($result, mb_convert_encoding("Nom;Numéro d'identification;Sexe;Année de Naissance;Totale;Marginale;Cumulée;No d'identification;Nom;No d'identification;Nom;Nb de descendants;Mini;Maxi\r\n", 'UTF-16LE', 'UTF-8'));

for ($i = 0; $i < count($list_anc_tab); $i++) {
    $row = $list_anc_tab[$i];
    $line = array($row[3], $row[2], $row[4], $row[5], $row[9], $row[10], $row[11], $row[14], $row[15], $row[16], $row[17], $row[8], $row[12], $row[13]);
    fputs($result, mb_convert_encoding(implode(";", $line), 'UTF-16LE', 'UTF-8'));
}

fclose($result);

replace_contribution_id_numbers($destination, $sortie_contrib, $animal_dict);

echo '{"status": "ok"}';


function replace_contribution_id_numbers($destination, $filename, $dict_correspondance){
    $filepath = $destination . $filename;
    $fr_ressource = fopen($filepath, 'r');
    $contributions = [];
    while ($line = fgets($fr_ressource)){
        $contribution_line = explode(';', preg_replace('/\s+/', ';', remove_spaces($line)));
        $animal_info = $dict_correspondance[$contribution_line[0]];
        $no_sire = $animal_info[0];
        $name = $animal_info[1];
        $final_contribution_line = [$name, $no_sire, $contribution_line[1], $contribution_line[2]];
        array_push($contributions, implode(';', $final_contribution_line));
    }
    fclose($fr_ressource);
    
    $fw_ressource = fopen($filepath, 'w+');
    for ($i=0; $i<count($contributions); $i++) {
        fwrite($fw_ressource, $contributions[$i] . "\r\n");
    }
    fclose($fw_ressource);
}