<?php
require_once '../fonctions.php';

/*
 * les fichiers, meuw.php, vanrad.php, par3.php, etc. suivent tous le m�me sch�ma:
 * D'abord on r�cup�re les param�tres sp�cifi�s par l'utilisateur et on les �crit dans un fichier de lancement .txt
 * Ensuite on ex�cute le programme demand� g�ce � shellexec et sur base du fichier txt sus-mentionn�
 * 
 * Le probl�me des programmes de pedig �tant que les fichiers de sortie ne contiennent que les id cr��s pour chaque animal par pedig,
 * 		il s'agit donc de cr�er un autre fichier (.txt ou .csv) donnant les r�sultats de pedig, mais associ�s cette fois aux num�ro d'identifications des animaux
 * EDIT : LE FICHIER CR�� PAR MEUW.EXE EST EN FAIT �CRAS� POUR NE PAS AVOIR TROP DE FICHIERS!!
 * 
 * On peut imaginer � l'avenir relier les r�sultats au nom des animaux, ce qui faciliterait la lecture de r�sultats
 */


/*
 * R�cup�ration des param�tres
 */		

        $race = $_GET['key1'];
        $ped_util = $_GET['key2'];
        $sortie = $_GET['key3'];

/*
 * Ecriture des param�tres dans le fichier ...lancement.txt
 */
        ensure_directory_existence(PEDIG_FILES_FOLDER);
        $fp = fopen(PEDIG_FILES_FOLDER . "lancement_meuw.txt", "w+"); // création et/ou modification d'un fichier texte, ici le fichier .txt contient les informations � envoyer � meuw pour qu'il s'execute tout seul
        fputs($fp, PEDIG_FILES_FOLDER . $ped_util); // 1ere ligne du fichier texte
        fputs($fp, "\r\n");
        fputs($fp, PEDIG_FILES_FOLDER . $sortie);
        fputs($fp, "\r\n");
        fclose($fp);

/*
 * Ex�cution du programme (ici: meuw)
 */

        $output = shell_exec(PEDIG_MODULES_FOLDER . 'meuw.exe < ' . PEDIG_FILES_FOLDER . 'lancement_meuw.txt'); // lancement de ped_util � partir du fichier .txt cr�� au dessus

/*
 * Un fichier a �t� cr�� avec les r�sultats
 * 
 * On va en �crire un autre avec les m�mes r�sultats reli�s � l'identifiant des animaux
 */


/*
 * Ouverture du fichier ped_...txt (qui contient l'id pedig ET le num�ro d'identification de chaque animal)
 */
        $pedFile = fopen(PEDIG_FILES_FOLDER . "ped_". $race .".csv","r");
        //$pedFile = fopen("C:\wamp\www\Genis\SiteWeb\Calculs\Pedig\ped_animaux.csv","r");


        $tab = array();										//Cr�ation et initialisation d'un tableau qui contiendra l'id et le no_identification

        $k = 0;

        while (($data = fgets($pedFile, 115)) !== false) {	// On r�cup�re chaque ligne du fichier une par une, et elle est ensuite trait�e comme une chaine de caract�res
                $data = str_replace(" ",";",$data);				// Remplacement de tous les caract�res " " (espaces) par des ";"
                for ($i=12; $i>1; $i--) {						// Cette boucle sert � cr�er des chaines de ";;;;;" de diff�rentes longuers, de chercher chacune d'elle dans la ligne en cours de lecture, et de la remplacer par un point-virgule unique
                        $str = ";";									// On commence � $i=12 car le nombre d'espaces cons�cutifs peut aller jusqu'� 10 dans ped_...csv
                        $j=0;										// Je mets 12 pour etre s�r
                        while ($j<$i) {
                                $str = $str.";";
                                $j++;									// Explication suppl�mentaire: Comme un "\t" est fait de plusieurs espaces, on obtient des s�ries
                        }											// s�ries de ";" inutiles => donc on r�duit leur nombre
                        $data = str_replace($str,";",$data);
                }
                $data = substr($data,1,100);					// Il faut enleveer le ";" au d�but de la cha�ne
                $array = explode(";",$data);					// on segmente la chaine de caract�res
                $tab[$k][0] = $array[0];						// on met l'id attribu� par pedig et le num�ro d'identification
                $tab[$k][1] = $array[7];
                $tab[$k][2] = 0;								// dans un m�me tableau
                $k++;
        }

        $k=0; $i=0; $j=0;									// on r�initialise les compteurs par pr�caution

        /*foreach ($tab as $t) {
         echo $t[0] .' '. $t[1] .' '. $t[2] .'<br>';					// juste pour avoir un apercu
         }*/

        fclose($pedFile);									// Fermeture du fichier ped_...csv

/*
 * On proc�de � une d�marche similaire pour le fichier (ici: meuw_...txt) cr�� par le programme
 */

        $meuwFile = fopen(PEDIG_FILES_FOLDER . "meuw_". $race .".csv","r");

        $tabMeuw = array();
        $k=0;

        while (($data = fgets($meuwFile,115)) !== false) {
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
                $tabMeuw[$k][0] = $array[0];
                $tabMeuw[$k][1] = $array[1];
                $tabMeuw[$k][2] = 0;
                $k++;
        }

        /*foreach ($tabMeuw as $tM) {
                echo $tM[0] .' '. $tM[1] .' '.$tM[2] .'<br>';
        }*/

        fclose($meuwFile);

/*
 * C'est ici qu'on �dite le tableau appropri� pour relier le num�ro d'identification � l'id fourni par pedig
 * 
 * Dans le cas de meuw, j'�dite le tableau cr�� � partir des r�sultats de ped_util.exew,
 * 			mais pour d'autres programmes, tel que prob_orig, par exemple, il est plus judicieux de modifier
 * 			le tableau cr�� � partir des r�sultats du sous-programme (prob_orig.exe)
 * 
 * Ici, donc, j'ajoute le coefficient de consanguinit� au tableau tab
 */


foreach ($tab as $key => &$t) {
        foreach ($tabMeuw as $tM) {
                if ($t[0] == $tM[0]) {
                        $t[2] = $tM[1];
                }
        }
}

/*
 * Ecriture du tableau tab dans un fichier
 */

$destination_folder= PEDIG_DUMP_FOLDER . "/meuw/";
ensure_directory_existence($destination_folder);
$result_file = fopen($destination_folder . "meuw_". $race .".csv","w+");

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