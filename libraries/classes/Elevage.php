<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Elevage
 *
 * @author cl2811
 */

require_once 'methods.php';
require_once '../constants.php';
require_once 'DatabaseConnection.php';

class Elevage {
    private $id;
    
    /*public function get_farm_id(){
        return $this->id;
    }*/
    
    public function get_farm_animals($race, $sex, $repro, $period_start, $period_end) {
        $sex_range = $this->determine_sex_range_of_animals_to_select($sex, $repro);
        $sql = $this->build_sql_list_animals_on_farm($race, $sex_range, $period_start, $period_end);
        $con = new DatabaseConnection(HOST_DB, USER_DB, DB_NAME, PW_DB);
        $animals = $con->select($sql);
        $con->close_db_connection();
        $list_animals = $this->sort_animals($animals);
        
        return $list_animals;
    }
    
    private function sort_animals($recordset_animals) {
        $array_animals = [];
        $i=0;
        while ($row = $recordset_animals->fetch()) {
            $array_animals[$i]['id'] = $row['id_animal'];
            $array_animals[$i]['name'] = $row['nom_animal'];
            $array_animals[$i]['id_nb'] = $row['no_identification'];
            $array_animals[$i]['race'] = $row['lib_race'];
            $array_animals[$i]['sex'] = $row['sexe'];
            $array_animals[$i]['birth'] = $row['date_naiss'];
            $array_animals[$i]['nom_pere'] = $row['nom_pere'];
            $array_animals[$i]['no_pere'] = $row['no_pere'];
            $array_animals[$i]['nom_mere'] = $row['nom_mere'];
            $array_animals[$i]['no_mere'] = $row['no_mere'];
            $i++;
        }
        return json_encode($array_animals, JSON_UNESCAPED_UNICODE);
    }
    
    private function determine_sex_range_of_animals_to_select($sex, $repro) {
        //$repro = 0 --> on veut tous les animaux, reproducteurs ou non
        if ($sex == 0 && $repro == 1) {
            $sex_range = '(1,2)';
        } elseif ($sex == 0 && $repro == 0) {
            $sex_range = '(1,2,3)';
        } elseif ($sex == 1 && $repro == 1) {
            $sex_range = '(1)';
        } elseif ($sex == 1 && $repro == 0) {
            $sex_range = '(1,3)';
        } elseif ($sex == 2) {
            $sex_range = '(2)';
        }
        return $sex_range;
    }
        
    public function get_bread_races() {
        $sql = $this->build_sql_list_bread_races($this->id);
        $con = new DatabaseConnection(HOST_DB, USER_DB, DB_NAME, PW_DB);
        $races = $con->select($sql);
        $con->close_db_connection();
        
        $bread_races = $this->sort_species_and_races($races);
        
        return $bread_races;
    }
    private function sort_species_and_races($recordset_races) {
        $array_races = [];
        $i = 0;
        while ($row = $recordset_races->fetch()) {
            $array_races[$i]['id_espece'] = $row['id_espece'];
            $array_races[$i]['lib_espece'] = $row['lib_espece'];
            $array_races[$i]['races'] = [];
            $id_races_array = explode(',', $row['code_race']);
            $name_races_array = explode(',', $row['lib_race']);
            for ($j=0; $j<count($id_races_array); $j++) {
                $array_races[$i]['races'][$j]['id_race'] = $id_races_array[$j];
                $array_races[$i]['races'][$j]['lib_race'] = $name_races_array[$j];
            }
            $i++;
        }
        return json_encode($array_races, JSON_UNESCAPED_UNICODE);
    }
    
    private function build_sql_list_bread_races($id_farm) {
        $sql = "SELECT  group_concat(cast(r.code_race as char)) code_race, group_concat(cast(r.lib_race as char)) lib_race, e.id_espece, e.lib_espece
                FROM    link_race_elevage lre
                JOIN    race r ON r.code_race=lre.code_race
                JOIN    espece e ON e.id_espece=r.id_espece
                WHERE   id_elevage = {$id_farm}
                GROUP BY id_espece
                ORDER BY id_espece ASC, code_race ASC;
                ";
        return $sql;
    }
    
    private function build_sql_list_animals_on_farm($race, $sex_range, $start, $end){
        $sql = "SELECT  DISTINCT a.id_animal, a.nom_animal, a.no_identification, r.lib_race, a.sexe, a.date_naiss, pere.nom_animal as nom_pere, pere.no_identification as no_pere, mere.nom_animal as nom_mere, mere.no_identification as no_mere
                FROM    animal a
                JOIN    race r ON r.code_race=a.code_race
                JOIN    animal pere ON pere.id_animal=a.id_pere
                JOIN    animal mere ON mere.id_animal=a.id_mere
                JOIN    periode p ON p.id_animal=a.id_animal
                WHERE   p.id_type = 2
                    AND p.id_elevage = {$this->id}
                    AND (p.date_entree BETWEEN '{$start}' AND '{$end}' OR p.date_sortie BETWEEN '{$start}' AND '{$end}' OR (p.date_entree <= '{$start}' AND p.date_sortie >= '{$end}') OR (p.date_entree <= '{$start}' AND p.date_sortie IS NULL))
                    AND a.sexe IN {$sex_range}";
        if ($race != 0) {
            $sql .= " AND a.code_race = {$race}";
        }
        
        return $sql;
    }

    public function __construct($id_farm) {
        $this->id = $id_farm;
    }
}
