<?php

require_once '../fonctions.php';		
//header("Location: http://localhost/Genis/SiteWeb/Calculs/Pedig/postCalculs.php");
		
$race = $_GET['key1'];
$ped_util = $_GET['key2'];
$sortie = $_GET['key3'];


ensure_directory_existence(PEDIG_FILES_FOLDER);
$fp = fopen(PEDIG_FILES_FOLDER . "lancement_vanrad.txt", "w+"); // cr�ation et/ou modification d'un fichier texte, ici le fichier .txt contient les informations � envoyer � vanrad pour qu'il s'execute tout seul
fputs($fp, "C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $ped_util); // 1ere ligne du fichier texte
fputs($fp, "\r\n");
fputs($fp, "C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $sortie);
fputs($fp, "\r\n");
fclose($fp);

$output=shell_exec('C:\wamp64\www\genis.cra\libraries\pedigModules\vanrad.exe < C:\wamp64\www\genis.cra\calculs\pedigFiles\lancement_vanrad.txt'); // lancement de ped_util � partir du fichier .txt cr�� au dessus


$pedFile = fopen(PEDIG_FILES_FOLDER . "ped_". $race .".csv","r");
//$pedFile = fopen("C:/wamp/www/Genis/SiteWeb/Calculs/Pedig/ped_animaux.csv","r");
$tab = array();										//initialisation d'un tableau qui contiendra l'id et le no_identification

$k = 0;

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
        $tab[$k][0] = $array[0];						// on met l'id attribu� par pedig et le num�ro d'identification
        $tab[$k][1] = $array[7];						// 		dans un m�me tableau
        $tab[$k][2] = 0;
        $k++;
}

$k=0; $i=0; $j=0;

/*foreach ($tab as $t) {
 echo $t[0] .' '. $t[1] .' '. $t[2] .'<br>';					// juste pour avoir un apercu
 }*/

fclose($pedFile);


$vanFile = fopen(PEDIG_FILES_FOLDER . "vanrad_". $race .".csv","r");
//$vanFile = fopen("C:/wamp/www/Genis/SiteWeb/Calculs/Pedig/vanrad_animaux.csv","r");

$tabVan = array();
$k=0;

while (($data = fgets($vanFile,115)) !== false) {
        $data = str_replace(" ",";", $data);

        for ($i=10; $i>0; $i--) {
                $str = ";";
                $j = 0;
                while ($j<$i) {
                        $str = $str .";";
                        $j++;
                }
                $data = str_replace($str,";",$data);
        }
        $data = substr($data,1,100);
        $array = explode(";",$data);
        array_push($tabVan,$array);
        /*$tabVan[$k][2] = 0;
         $k++;*/
}

/*foreach ($tabVan as $tV) {
        echo $tV[0] .' '. $tV[1] .'<br>';
}*/

fclose($vanFile);

foreach ($tab as $key => &$t) {
        foreach ($tabVan as $tV) {
                if ($t[0] == $tV[0]) {
                        $t[2] = $tV[1];
                }
        }
}

$destination_folder= PEDIG_DUMP_FOLDER . "/vanrad/";
ensure_directory_existence($destination_folder);
$result_file = fopen($destination_folder . "vanrad_". $race .".csv","w+");

fputs($result_file,"ID Pedig;Numéro d'identification;Coefficient de consanguinité\r\n");

$result = '';
foreach ($tab as $row) {
    $line = '';
    $i = 0;
    foreach ($row as $cell) {
        if ($i == 2) {
            $cell = trim(preg_replace('/\s\s+/', '', $cell));
        }
        $line = $line . $cell .";";
        $i++;
    }
    $result = $result . $line . "\r\n";
}
fputs($result_file, $result);
fclose($result_file);
