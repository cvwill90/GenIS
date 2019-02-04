<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('USER_DB', 'root');
define('HOST_DB', '127.0.0.1');
define('PW_DB', '');
define('DB_NAME', 'genis_test');

define('PROJECT_ROOT', dirname(dirname(__FILE__)));
define('HEAD_START', PROJECT_ROOT . '/libraries/html_head1.php');
define('BODY_START', PROJECT_ROOT . '/libraries/html_bodystart1.php');
define('BODY_END', PROJECT_ROOT . '/libraries/html_bodyend1.php');

define('IMPORT_RACES', '[0,5,6,2,19,20,21,9,10]');
define('IMPORT_RACES_LABEL', '["- Choisissez une race -","Vache Bordelaise","Vache Marine","Poney Landais","Vache Béarnaise","Vache Maraîchine","Baudet du Poitou","Mouton Landais","Sasi Ardia"]');

define('SESSION_MAX_LIFETIME', 1800);

define('DEFAULT_LABELS', array(
    'livre_gene' => 'Non renseigné'
));

define('EXPORT_TYPES', array(
    'intranet' => 'intranet_export',
    'intern' => 'intern_export'
));

/*
 * Paramètres du conservatoire du fichier de configuration
 */

$ini = parse_ini_file(PROJECT_ROOT .'/config/conf.ini', TRUE);

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