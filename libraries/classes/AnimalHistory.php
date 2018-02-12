<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AnimalHistory
 *
 * @author cl2811
 */
require_once '../fonctions.php';
require_once 'DatabaseConnection.php';

class AnimalHistory {
    private $id_animal;
    private $db_con;
    
    private function connect_to_database() {
        $con = new DatabaseConnection(HOST_DB, USER_DB, DB_NAME, PW_DB);
        $this->db_con = $con;
    }
    
    public function change_animal_birth_info($date, $farm_id) {
        $birth_update_result = $this->update_animal_birth_info($date, $farm_id);
        return $birth_update_result;
    }
    
    private function update_animal_birth_info($date, $farm_id) {
        $sql_get_current_birth_date = "SELECT date_entree FROM periode WHERE id_animal = {$this->id_animal} AND id_type = 3";
        $first_stay_id = $this ->db_con->select("SELECT id_periode FROM periode WHERE id_animal = {$this->id_animal} AND id_type = 2 AND date_entree = ({$sql_get_current_birth_date})")->fetch();
        
        $sql_modify_birth_date = "UPDATE periode SET date_entree = \"{$date}\", id_elevage = {$farm_id} WHERE id_animal = {$this->id_animal} AND id_type = 3";
        $sql_modify_first_stay = "UPDATE periode SET date_entree = \"{$date}\", id_elevage = {$farm_id} WHERE id_periode = {$first_stay_id['id_periode']}";
        
        $sql_modify_birth = implode(';', array($sql_modify_birth_date, $sql_modify_first_stay));
        
        return $sql_modify_birth;
    }
    
    public function change_animal_death_date($date) {
        if ($date == 'alive') {
            $death_update_result = $this->undo_animal_death();
        } else {
            $death_update_result = $this->update_animal_death_date($date);
        }
        return $death_update_result;
    }
    
    private function undo_animal_death() {
        $sql_get_current_death_date = "SELECT date_sortie FROM periode WHERE id_animal = {$this->id_animal} AND id_type = 1";
        $last_stay_id = $this ->db_con->select("SELECT id_periode FROM periode WHERE id_animal = {$this->id_animal} AND id_type = 2 AND date_sortie = ({$sql_get_current_death_date})")->fetch();
        
        if ($last_stay_id) {
            $sql_delete_death_entry = "DELETE FROM periode WHERE id_animal = {$this->id_animal} AND id_type = 1";
            $sql_modify_last_stay = "UPDATE periode SET date_sortie = NULL WHERE id_periode = {$last_stay_id['id_periode']}";
            $sql_undo_death = implode(";", [$sql_delete_death_entry, $sql_modify_last_stay]);
        } else {
            $sql_undo_death = "";
        }
        return $sql_undo_death;
    }
    
    private function update_animal_death_date($date) {
        $sql_get_current_death_date = "SELECT date_sortie FROM periode WHERE id_animal = {$this->id_animal} AND id_type = 1";
        $last_stay_id = $this ->db_con->select("SELECT id_periode FROM periode WHERE id_animal = {$this->id_animal} AND id_type = 2 AND date_sortie = ({$sql_get_current_death_date})")->fetch();
        
        $sql_modify_death_date = "UPDATE periode SET date_sortie = \"{$date}\" WHERE id_animal = {$this->id_animal} AND id_type = 1";
        $sql_modify_last_stay = "UPDATE periode SET date_sortie = \"{$date}\" WHERE id_periode = {$last_stay_id['id_periode']}";
        
        $sql_undo_death = implode(";", [$sql_modify_death_date, $sql_modify_last_stay]);
        
        return $sql_undo_death;
    }
    
    public function get_animal_history() {
        $sql_get_animal_history = "SELECT * FROM periode where id_animal = {$this->id_animal}";
        $animal_history = $this->db_con->select($sql_get_animal_history);
        return $animal_history;
    }
    
    public function get_id($param) {
        return $this->id_animal;
    }
    
    public function __construct($id) {
        $this->id_animal = $id;
        $this->connect_to_database();
    }
}