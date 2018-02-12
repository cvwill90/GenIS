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
    
    public function delete_animal() {
        $sql = $this->get_animal_deletion_sql();
        $db_con = new DatabaseConnection(HOST_DB, USER_DB, DB_NAME, PW_DB);
        $result = $db_con->execute($sql);
        $db_con->close_db_connection();
        return $result;
    }
    
    public function get_animal_deletion_sql() {
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

    public function __construct($id_animal) {
        $this->id = $id_animal;
    }
}
