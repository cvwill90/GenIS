<html>
    <head>
        <meta http-equiv="Content-Type" content="text/csv; charset=UTF-8">
    </head>
    <body>
        <?php

        /* 
         * To change this license header, choose License Headers in Project Properties.
         * To change this template file, choose Tools | Templates
         * and open the template in the editor.
         */

        include '../fonctions.php';
        
        $type = $_GET['type'];
        
        if ($type == EXPORT_TYPES['intranet']) {
            $table_headers = "id_contact;nom;prenom;cheptel;id_animal;nom_animal;sexe;no_identification;date_naiss;id_mere;nom_mere;identif_mere;id_pere;nom_pere;identif_pere;code_race;nom_race";
            $table_headers_array = explode(';', $table_headers);

            $sql_export = "SELECT id_contact, nom, prenom, e.nom_elevage as cheptel, a.id_animal, a.nom_animal, a.sexe, a.no_identification, a.date_naiss, a.id_mere, mere.nom_animal AS nom_mere, mere.no_identification AS identif_mere, a.id_pere, pere.nom_animal AS nom_pere, pere.no_identification AS identif_pere, a.code_race, r.lib_race AS nom_race, esp.lib_espece 
                    FROM animal a 
                    INNER JOIN animal mere ON a.id_mere=mere.id_animal 
                    INNER JOIN animal pere ON a.id_pere=pere.id_animal 
                    INNER JOIN race r ON a.code_race=r.code_race 
                    INNER JOIN espece esp ON esp.id_espece=r.id_espece 
                    INNER JOIN periode p ON p.id_animal=a.id_animal 
                    LEFT JOIN elevage e ON p.id_elevage=e.id_elevage 
                    LEFT JOIN contact c ON c.id_elevage=e.id_elevage 
                    WHERE p.id_type=2 AND p.date_sortie IS NULL
                    AND a.id_animal NOT IN (SELECT id_animal FROM periode WHERE id_type=1) 
                    ORDER BY esp.lib_espece, nom_race, a.nom_animal";
        } elseif ($type == EXPORT_TYPES['intern']) {
            
        }
        
        //export location
        $target_folder = EXPORT_FOLDER;
        $filename =  "intranet.csv";

        if (!is_dir($target_folder)){
            mkdir($target_folder, 0777, true);
        }
        
        $con = pdo_connection(HOST_DB, DB_NAME, USER_DB, PW_DB);
        
        $result = $con->query($sql_export);

        $fp = fopen($target_folder .'\\'. $filename, 'w');
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        fwrite($fp, $table_headers . "\n");

        $csv = $table_headers;
        while ($rs = $result->fetch()){
            $csv_line = array();
            for ($i=0; $i<count($table_headers_array); $i++){
                $value = $rs[$table_headers_array[$i]];
                if ($value == ''){
                    $value = 'N/A';
                }
                array_push($csv_line, $value);
            }
            fputcsv($fp, $csv_line, ';', '"');
        }

        fclose($fp);
        
        ?>


    </body>
</html>