<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DatabaseOperations
 *
 * @author cl2811
 */
class DatabaseConnection {
    private $db;
    private $pw;
    private $user;
    private $host;
    private $con;
    
    public function select($sql) {
        $con = $this->con;
        try {
            $con->beginTransaction();
            $result = $con->query($sql);
            $con->commit();
        } catch (Exception $e) {
            $con->rollback();
            $result = $this->handle_sql_error($e->getCode(), $e->getMessage());
        }
        return $result;
    }
    
    public function execute($sql) {
        $con = $this->con;
        try {
            $con->beginTransaction();
            $con->query($sql);
            $con->commit();
        } catch (Exception $e) {
            $con->rollback();
            $error = $this->handle_sql_error($e->getCode(), $e->getMessage());
        }
        
        if (isset($e)) {
            $return = $error;
        } else {
            $return = array('error'=>FALSE);
        }
        return $return;
    }
    
    private function handle_sql_error($error_code, $error_msg) {
        $response = array();
        $response['error'] = True;
        $response['error_msg'] = $error_msg;
        return $response;
    }
    
    private function create_db_connection($host, $user, $db, $pw) {
        $this->con = new PDO('mysql:host='. $host .';dbname='. $db .';charset=utf8', $user, $pw);
        $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function close_db_connection(){
        $this->con = null;
    }

    public function __construct($host, $user, $db, $pw) {
        $this->create_db_connection($host, $user, $db, $pw);
    }
}
