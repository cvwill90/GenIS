<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Animal
 *
 * @author cl2811
 */

require_once '../constants.php';
require_once 'DatabaseConnection.php';

class Animal {
    private $id;
    private $db_con;
    
    public function delete_animal() {
        $sql = $this->get_animal_deletion_sql();
        $result = $this->db_con->execute($sql);
        $this->db_con->close_db_connection();
        return $result;
    }
    
    public function get_animal_information($fields): array {
        $sql = $this->get_animal_information_sql($fields);
        $result = $this->db_con->select($sql);
        $this->db_con->close_db_connection();
        $animal_information = $result->fetch();
        return $animal_information;
    }
    
    public function get_animal_deletion_sql(): string {
        $sql = "DELETE FROM periode 
                WHERE id_animal=". $this->id .";";
        $sql .= "UPDATE animal 
                 SET id_pere = 1 
                 WHERE id_pere = ". $this->id .";";
        $sql .= "UPDATE animal 
                 SET id_mere = 2 
                 WHERE id_mere = ". $this->id .";";
        $sql .= "DELETE FROM animal 
                 WHERE id_animal=". $this->id;
        return $sql;
    }
    
    private function get_animal_information_sql($fields): string {
        $requested_fields = implode(",", $fields);
        $sql =  "SELECT $requested_fields "
                . "FROM animal "
                . "WHERE id_animal = $this->id";
        return $sql;
    }
    
    public function close_db_connection() {
        $this->db_con->close_db_connection();
    }

    public function __construct($id_animal) {
        $this->id = $id_animal;
        $this->db_con = new DatabaseConnection(HOST_DB, USER_DB, DB_NAME, PW_DB);
    }
}
