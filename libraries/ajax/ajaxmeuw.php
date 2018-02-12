<?php

/*
 * les fichiers, meuw.php, vanrad.php, par3.php, etc. suivent tous le même schéma:
 * D'abord on récupère les paramètres spécifiés par l'utilisateur et on les écrit dans un fichier de lancement .txt
 * Ensuite on exécute le programme demandé gâce à shellexec et sur base du fichier txt sus-mentionné
 * 
 * Le problème des programmes de pedig étant que les fichiers de sortie ne contiennent que les id créés pour chaque animal par pedig,
 * 		il s'agit donc de créer un autre fichier (.txt ou .csv) donnant les résultats de pedig, mais associés cette fois aux numéro d'identifications des animaux
 * EDIT : LE FICHIER CRÉÉ PAR MEUW.EXE EST EN FAIT ÉCRASÉ POUR NE PAS AVOIR TROP DE FICHIERS!!
 * 
 * On peut imaginer à l'avenir relier les résultats au nom des animaux, ce qui faciliterait la lecture de résultats
 */


	/*
	 * Récupération des paramètres
	 */		

		$race = $_GET['key1'];
		$ped_util = $_GET['key2'];
		$sortie = $_GET['key3'];
		
	/*
	 * Ecriture des paramètres dans le fichier ...lancement.txt
	 */
		
		$fp = fopen("C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\lancement_meuw.txt", "w+"); // création et/ou modification d'un fichier texte, ici le fichier .txt contient les informations à envoyer à meuw pour qu'il s'execute tout seul
		fputs($fp, "C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $ped_util); // 1ere ligne du fichier texte
		fputs($fp, "\r\n");
		fputs($fp, "C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\". $sortie);
		fputs($fp, "\r\n");
		fclose($fp);
		
	/*
	 * Exécution du programme (ici: meuw)
	 */
		
		$output=shell_exec('C:\wamp64\www\genis.cra\libraries\pedigModules\meuw.exe < C:\wamp64\www\genis.cra\calculs\pedigFiles\lancement_meuw.txt'); // lancement de ped_util à partir du fichier .txt créé au dessus
		//echo $output;
		
	/*
	 * Un fichier a été créé avec les résultats
	 * 
	 * On va en écrire un autre avec les mêmes résultats reliés à l'identifiant des animaux
	 */
		
		
	/*
	 * Ouverture du fichier ped_...txt (qui contient l'id pedig ET le numéro d'identification de chaque animal)
	 */
		$pedFile = fopen("C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\ped_". $race .".csv","r");
		//$pedFile = fopen("C:\wamp\www\Genis\SiteWeb\Calculs\Pedig\ped_animaux.csv","r");
		
		
		$tab = array();										//Création et initialisation d'un tableau qui contiendra l'id et le no_identification
		
		$k = 0;
		
		while (($data = fgets($pedFile, 115)) !== false) {	// On récupère chaque ligne du fichier une par une, et elle est ensuite traitée comme une chaine de caractères
			$data = str_replace(" ",";",$data);				// Remplacement de tous les caractères " " (espaces) par des ";"
			for ($i=12; $i>1; $i--) {						// Cette boucle sert à créer des chaines de ";;;;;" de différentes longuers, de chercher chacune d'elle dans la ligne en cours de lecture, et de la remplacer par un point-virgule unique
				$str = ";";									// On commence à $i=12 car le nombre d'espaces consécutifs peut aller jusqu'à 10 dans ped_...csv
				$j=0;										// Je mets 12 pour etre sûr
				while ($j<$i) {
					$str = $str.";";
					$j++;									// Explication supplémentaire: Comme un "\t" est fait de plusieurs espaces, on obtient des séries
				}											// séries de ";" inutiles => donc on réduit leur nombre
				$data = str_replace($str,";",$data);
			}
			$data = substr($data,1,100);					// Il faut enleveer le ";" au début de la chaîne
			$array = explode(";",$data);					// on segmente la chaine de caractères
			$tab[$k][0] = $array[0];						// on met l'id attribué par pedig et le numéro d'identification
			$tab[$k][1] = $array[7];
			$tab[$k][2] = 0;								// dans un même tableau
			$k++;
		}
		
		$k=0; $i=0; $j=0;									// on réinitialise les compteurs par précaution
		
		/*foreach ($tab as $t) {
		 echo $t[0] .' '. $t[1] .' '. $t[2] .'<br>';					// juste pour avoir un apercu
		 }*/
		
		fclose($pedFile);									// Fermeture du fichier ped_...csv
		
	/*
	 * On procède à une démarche similaire pour le fichier (ici: meuw_...txt) créé par le programme
	 */
		
		//$meuwFile = fopen("C:\wamp\www\Genis\SiteWeb\Calculs\Pedig\meuw_animaux.csv","r");
		$meuwFile = fopen("C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\meuw_". $race .".csv","r");
		
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
	 * C'est ici qu'on édite le tableau approprié pour relier le numéro d'identification à l'id fourni par pedig
	 * 
	 * Dans le cas de meuw, j'édite le tableau créé à partir des résultats de ped_util.exew,
	 * 			mais pour d'autres programmes, tel que prob_orig, par exemple, il est plus judicieux de modifier
	 * 			le tableau créé à partir des résultats du sous-programme (prob_orig.exe)
	 * 
	 * Ici, donc, j'ajoute le coefficient de consanguinité au tableau tab
	 */
		
		
		foreach ($tab as $key => &$t) {
			foreach ($tabMeuw as $tM) {
				if ($t[0] == $tM[0]) {
					$t[2] = $tM[1];
				}
			}
		}
		
		/*foreach ($tab as $t) {
		echo $t[0] .' '. $t[1] .' '. $t[2] .'<br>';
		}*/
		
	/*
	 * Ecriture du tableau tab dans un fichier
	 */
		
			$result = fopen("C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\meuw_". $race .".csv","w+");
			//$result = fopen("C:\wamp\www\Genis\SiteWeb\Calculs\Pedig\meuw_animaux.csv","w+");
			
			fputs($result,"Id_Pedig;Numéro d'identification;Coeff de consanguinité\r\n");
			
			foreach ($tab as $row) {
				foreach ($row as $cell) {
					fputs($result,$cell);
					fputs($result,";");
				}
				fputs($result,"\r\n");
			}
			fclose($result);