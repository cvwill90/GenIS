<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('PROJECT_ROOT', dirname(dirname(__FILE__)));
define('PEDIG_FILES_FOLDER', PROJECT_ROOT . '\\calculs\\pedigFiles\\');
define('PEDIG_ERROR_LOG_FILE', PROJECT_ROOT . '\\calculs\\error_log\\error_log.txt');
define('PEDIG_MODULES_FOLDER', PROJECT_ROOT . '\\libraries\\pedigModules\\');
$ini = parse_ini_file(PROJECT_ROOT .'\\config\\config.ini', TRUE);

define('USER_DB', 'root');
define('HOST_DB', '127.0.0.1');
define('PW_DB', '');
define('DB_NAME', $ini['database']['db_name']);

define('HEAD_START', PROJECT_ROOT . '/libraries/html_head1.php');
define('BODY_START', PROJECT_ROOT . '/libraries/html_bodystart1.php');
define('BODY_END', PROJECT_ROOT . '/libraries/html_bodyend1.php');

define('IMPORT_RACES', '[0,5,6,2,19,20,21,9,10,8]');
define('IMPORT_RACES_LABEL', '["- Choisissez une race -","Vache Bordelaise","Vache Marine","Poney Landais","Vache Béarnaise","Vache Maraîchine","Baudet du Poitou","Mouton Landais","Sasi Ardia", "Âne des Pyrénées"]');

define('SESSION_MAX_LIFETIME', 1800);

define('DEFAULT_LABELS', array(
    'livre_gene' => 'Non renseigné'
));

define('EXPORT_TYPES', array(
    'intranet' => 'intranet_export',
    'internal' => 'export_internal'
));

define('RACES_SOUMISES_A_GESTION_GENETIQUES', array(2, 7, 8, 21, 3, 4, 5, 6, 19, 20));

/*
 * Paramètres du conservatoire du fichier de configuration
 */
// Dossier contenant le programme "mysqldump.exe"
define('MYSQLDUMP_PATH', $ini['paths']['mysqldump_path']);

// Dossier de destination des sauvegardes de la base de données
define('DUMP_FOLDER', $ini['paths']['dump_folder']);

//Dossier destination des exportations INTRANET
define('EXPORT_FOLDER', $ini['paths']['export_folder']);

// Dossier de destination des résultats PEDIG
define('PEDIG_DUMP_FOLDER', $ini['paths']['pedig_dump_folder']);

// Dossier de destination des messages d'erreur
define('ERROR_LOG_FOLDER', $ini['paths']['error_log_folder']);


/*
 * Exceptions
 */

define("INPUT_EXCEPTIONS", array(
        0 => "Array cannot contain an empty value.",
        1 => "Has to be an INTEGER.",
        2 => "Must be greater than 0."
    )
);


define("GLOBAL_EXCEPTIONS", array(
        0 => "Les données transmises sont erronées."
    )
);