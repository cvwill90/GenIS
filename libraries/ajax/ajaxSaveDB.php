<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../constants.php';

//dump Database
$targetfolder = DUMP_FOLDER . "\\" . date('dmy');

if (!is_dir($targetfolder)){
    mkdir($targetfolder, 0777, true);
}

$filename = date('jmy_His') . '_sauvegarde.sql';

$cmd = MYSQLDUMP_PATH ." --user=root --host=localhost ". DB_NAME ." > \"". $targetfolder . "\\". $filename ."\"";

exec($cmd, $output, $return_var);

$response = array("status" => $return_var);

echo json_encode($response);