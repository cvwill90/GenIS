<?php
/*
 * Meme fichier pour l'autocompl�tion m�le eet femelle
 */

$race = $_GET["race"];
$animal = $_GET["term"];
$ajaxType = $_GET["ajaxType"];

if (isset($_GET['sex'])) {
    $sex = $_GET['sex'];
}

include '../fonctions.php';

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

if ($ajaxType==1) {			//Soit on signale une mort...
    $sql = "SELECT a.no_identification, a.nom_animal, a.id_animal, p.id_elevage, e.nom_elevage
            FROM animal a
            JOIN periode p ON p.id_animal = a.id_animal
            LEFT JOIN elevage e ON e.id_elevage = p.id_elevage
            WHERE code_race = $race
                    AND (no_identification LIKE \"%" . $animal . "%\" OR nom_animal LIKE \"%" . $animal . "%\")
                    AND p.id_type = 2 AND p.date_sortie is NULL
            ORDER BY nom_animal ASC LIMIT 0, 10";

    $query = $con->query($sql);

    $string = "[";

    $i = 0;

    while ($list = $query->fetch()) {
        if (isset($_GET['max'])) {
            $max = $_GET['max'];
            if ($i < $max) {
                $string = $string . "{\"label\": \"" . $list['no_identification'] . ' - ' . $list['nom_animal'] . "\", \"value\": \"" . $list['nom_animal'] . "\", \"id\": \"" . $list['id_animal'] . "\", \"id_elevage\": \"" . $list['id_elevage'] . "\", \"nom_elevage\": \"" . $list['nom_elevage'] . "\"},";
                $i++;
            } else {
                break;
            }
        } else {
            $string = $string . "{\"label\": \"" . $list['no_identification'] . ' - ' . $list['nom_animal'] . "\", \"value\": \"" . $list['nom_animal'] . "\", \"id\": \"" . $list['id_animal'] . "\", \"id_elevage\": \"" . $list['id_elevage'] . "\", \"nom_elevage\": \"" . $list['nom_elevage'] . "\"},";
        }
    }

    $string = $string . "]";

    $trimmed = str_replace(",]", "]", $string);
    echo $trimmed;


} elseif ($ajaxType==2) {			// ... soit on signale un mouvement.

	if ($sex != 0) {
		$sql = "SELECT a.no_identification, a.nom_animal, a.id_animal, p.id_elevage, e.nom_elevage
				FROM animal a
				JOIN periode p ON p.id_animal = a.id_animal
				JOIN elevage e ON e.id_elevage = p.id_elevage
				WHERE sexe = $sex
					AND code_race = $race
					AND (no_identification LIKE \"%" . $animal . "%\" OR nom_animal LIKE \"%" . $animal . "%\")
					AND p.id_type = 2 AND p.date_sortie is NULL
				ORDER BY nom_animal ASC LIMIT 0, 10";
	} else {
		$sql = "SELECT DISTINCT a.no_identification, a.nom_animal, a.id_animal, p.id_elevage, e.nom_elevage
				FROM animal a
				JOIN periode p ON p.id_animal = a.id_animal
				JOIN elevage e ON e.id_elevage = p.id_elevage
				WHERE code_race = $race
					AND (no_identification LIKE \"%" . $animal . "%\" OR nom_animal LIKE \"%" . $animal . "%\")
					AND p.id_type = 2 AND p.date_sortie is NULL
				ORDER BY nom_animal ASC LIMIT 0, 10";
	}
        
	$query = $con->query($sql);

	$string = "[";

	$i = 0;

	while ($list = $query->fetch()) {
		if (isset($_GET['max'])) {
			$max = $_GET['max'];
			if ($i < $max) {
				$string = $string . "{\"label\": \"" . $list['no_identification'] . ' - ' . $list['nom_animal'] . "\", \"value\": \"" . $list['nom_animal'] . "\", \"id\": \"" . $list['id_animal'] . "\", \"id_elevage\": \"" . $list['id_elevage'] . "\", \"nom_elevage\": \"" . $list['nom_elevage'] . "\"},";
				$i++;
			} else {
				break;
			}
		} else {
			$string = $string . "{\"label\": \"" . $list['no_identification'] . ' - ' . $list['nom_animal'] . "\", \"value\": \"" . $list['nom_animal'] . "\", \"id\": \"" . $list['id_animal'] . "\", \"id_elevage\": \"" . $list['id_elevage'] . "\", \"nom_elevage\": \"" . $list['nom_elevage'] . "\"},";
		}
	}

	$string = $string . "]";

	$trimmed = str_replace(",]", "]", $string);
	echo $trimmed;

} else {

	$sql = "SELECT DISTINCT a.no_identification, a.nom_animal, a.id_animal, p.id_elevage, e.nom_elevage
				FROM animal a
				JOIN periode p ON p.id_animal = a.id_animal
				JOIN elevage e ON e.id_elevage = p.id_elevage
				WHERE code_race = $race
					AND (no_identification LIKE \"%" . $animal . "%\" OR nom_animal LIKE \"%" . $animal . "%\")
					AND p.id_type = 4 AND p.date_sortie is NULL
				ORDER BY nom_animal ASC LIMIT 0, 10";

$query = $con->query($sql);

$string = "[";

$i = 0;

while ($list = $query->fetch()) {
	if (isset($_GET['max'])) {
		$max = $_GET['max'];
		if ($i < $max) {
			$string = $string . "{\"label\": \"" . $list['no_identification'] . ' - ' . $list['nom_animal'] . "\", \"value\": \"" . $list['nom_animal'] . "\", \"id\": \"" . $list['id_animal'] . "\", \"id_elevage\": \"" . $list['id_elevage'] . "\", \"nom_elevage\": \"" . $list['nom_elevage'] . "\"},";
			$i++;
		} else {
			break;
		}
	} else {
		$string = $string . "{\"label\": \"" . $list['no_identification'] . ' - ' . $list['nom_animal'] . "\", \"value\": \"" . $list['nom_animal'] . "\", \"id\": \"" . $list['id_animal'] . "\", \"id_elevage\": \"" . $list['id_elevage'] . "\", \"nom_elevage\": \"" . $list['nom_elevage'] . "\"},";
	}
}

$string = $string . "]";

$trimmed = str_replace(",]", "]", $string);
echo $trimmed;

}