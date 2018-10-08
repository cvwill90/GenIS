
<?php
/**
 * Created by PhpStorm.
 * User: Christophe
 * Date: 25/01/2016
 * Time: 16:16
 */

require_once 'constants.php';

function autoload_classes(){
    spl_autoload_register(function ($class_name){
        include PROJECT_ROOT . '/libraries/classes/' . $class_name . '.php';
    });
}

/**
 * Get the URL of the provided resource (php or html file)
 * @param type $folder: E.g. '/path/to/resource.php
 * @return string
 */
function get_web_location($folder) {
    if (isset($_SERVER['HTTPS'])) {
        $protocol = $_SERVER['HTTPS'] == '' ? 'http://' : 'https://';
    } else {
        $protocol = 'http://';
    }
    $location = $protocol . $_SERVER['HTTP_HOST'] . $folder;
    return $location;
}

function invalidate_session_if_expired() {
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_MAX_LIFETIME)) {
        // last request was more than 30 minutes ago
        session_unset();     // unset $_SESSION variable for the run-time
        session_destroy();   // destroy session data in storage
    }
    $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
}

function get_all_especes($con){
    $sql_especes = "SELECT id_espece, lib_espece FROM espece ORDER BY lib_espece";
    
    $query = pdo_sql_query($con,$sql_especes);

    $array_especes = array();
    while ($result_especes = $query -> fetch()){
      $array_especes[$result_especes[0]] = $result_especes[1];    
    }
    
    return $array_especes;
}

/**
 * CONNEXION : METHODE PDO
 * @param $host
 * @param $db
 * @param $user
 * @param $pw
 * @return PDO
 */

function pdo_connection($host,$db,$user,$pw){

    //Tentative de connexion
    try {
        $con = new PDO('mysql:host='. $host .';dbname='. $db .';charset=utf8', $user, $pw);
        $con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

        // Si erreur: attraper l'erreur et afficher le message d'erreur
    catch (Exception $e)
    {
        die('Erreur : ' . $e->getMessage());
    }

    return $con;
}

/**
 * CONNEXION : METHODE MYQLI
 * @param $host
 * @param $db
 * @param $user
 * @param $pw
 * @return mysqli
 */

    function mysqli_connection($host,$db,$user,$pw){

        $con = mysqli_connect($host,$user,$pw);

        mysqli_select_db($con,$db)
            or die('Impossible de sélectionner la base de données '. $db .' : '. mysqli_error($con));
        return $con;
    }

/**
 * EXECUTE MULTIPLE QUERIES ARRAY : METHODE PDO
 * @param $con
 * @param $queries
 * @return mixed
 */

    function pdo_sql_query_array($con,$queries){
        $queries_array = array();
        try {
            $con -> beginTransaction();
            $j=0;
            $n = count($queries);
            for ($i=0;$i<$n;$i++){
                $sql = $queries[$i];
                $sub_sql = substr($sql,0,6);
                if (strcasecmp($sub_sql,'select')==0){
                    $queries_array[$j] = $con -> query($sql);
                    $j++;
                } else {
                    $con->query($sql);
                }
            }

            $transactioncommit= $con -> commit();

        } catch (Exception $e) {
            $con->rollback();
        }
        if (isset($e)){
            $err = $con -> errorCode();
            if ($err == 23000){
                echo 'Erreur #'. $err .' : Vous avez entré un identifiant déjà existant ! Merci d\'en choisir un autre !';
                return $err;
            } else {
                return $e;
            }
        }
        else
            return $queries_array;
    }

/**
 * EXECUTE QUERY : METHODE PDO
 * @param $con
 * @param $sql
 * @return mixed
 */

function pdo_sql_query($con,$sql){
    try {
        $con -> beginTransaction();

        $result = $con->query($sql);

        $con -> commit();

    } catch (Exception $e) {
        $con->rollback();
        $result = $e->getMessage();
    }
    return $result;
}


/**
 * FETCH MULTIPLE RECORDSETS ARRAY : METHODE PDO
 * @param $query_array
 * @param $labels_array
 * @param $query_number
 * Comment : type index of wanted array
 * @return mixed
 */

    function pdo_query_fetch_array($query_array,$labels_array,$query_number=0){
        if (is_array($query_array)){
            $n = count($query_array);
            $result_arrays = array();
            for ($i=0;$i<$n;$i++){                           //recordset

                $recordset_array = array();
                $m = count($labels_array[$i]);
                while ($list = $query_array[$i] -> fetch()) {         //nb de lignes de chaque recordset
                    $array = array();
                    for ($k=0;$k<$m;$k++) {                                 //nb colonnes de chaque ligne
                        $array[$k] = $list[$labels_array[$i][$k]];
                    }
                    array_push($recordset_array,$array);
                }
                array_push($result_arrays,$recordset_array);
            }
            if ($query_number!=0)
                return $result_arrays[$query_number];
            else
                return $result_arrays;
        } else {
            echo 'Erreur : La variable entrée pour "$query_array" n\'est pas un tableau !';
            return false;
        }
    }

/**
 * EXECUTE SINGLE SQL QUERY
 * @param $query
 * @return array
 */

function pdo_query_fetch($query){
    $array = array();
    $m = $query->columnCount();
    $i = 0;
    while ($list = $query -> fetch()) {         //nb de lignes de chaque recordset

        for ($k=0;$k<$m;$k++) {                                 //nb colonnes de chaque ligne
            array_push($array,$list[$k]);
        }
        $i++;
    }
    return $array;
}


/**
 * FUNCTION : create select list
 * @param $index
 * @param $labels
 * @param $name
 * @param int $first_option
 * @param string $class
 * @param int $size
 * @param $otherAttributes
 */

    function tableau_choix($index,$labels,$name,$first_option=1,$otherAttributes,$class="form-control",$size=1){
        if(count($index)!=count($labels)){
            echo 'error';
        } else {
            echo '<select name="'. $name .'" size='. $size .' class="'. $class .'" '. $otherAttributes .'>';
            for ($i=0;$i<count($labels);$i++){
                if ($index[$i]==$first_option){
                    echo '<option value="'. $index[$i] .'">'. $labels[$i] .'</option>';
                }
            }
            for ($i=0;$i<count($labels);$i++){
                if ($index[$i]!=$first_option){
                    echo '<option value="'. $index[$i] .'">'. $labels[$i] .'</option>';
                }
            }
            echo '</select>';
        }
    }

function reset_auto_increment($con, $db, $table, $field){
    
    //Get ID number of last row
    $sql_get_last_row = "SELECT MAX({$field}) FROM {$db}.{$table}";
    
    try {
        $con->beginTransaction();
        $result = $con->query($sql_get_last_row);
        $last_row = $result->fetch()[0];
        $new_AI = $last_row + 1;
        $sql_alter_auto_increment = "ALTER TABLE `animal` auto_increment = {$new_AI}";
        $con->query($sql_alter_auto_increment);
        $con->commit();
    } catch (Exception $ex) {
        $con->rollback();
    }
    return $new_AI;
}

function getNomAncetre($con, $parentID, $sexe){
    $ancestorNotFound = true;
    if ($sexe == 1){
        $mom_or_dad = 'id_pere';
    } else {
        $mom_or_dad = 'id_mere';
    }
    $nomAncetre = 'Parenté Inconnue';
    $i = 0;
    while ($ancestorNotFound){
        $queryancetre = "SELECT id_animal, {$mom_or_dad}, nom_animal FROM animal WHERE id_animal = {$parentID}";
        
        $recordset = $con->query($queryancetre);
        
        $result = $recordset->fetch();
        
        if (in_array(intval($result[0]), array(1, 2)) && is_null($result[1])) {
            if ($i == 1) {
                $nomAncetre = $result[2];
            }
            $ancestorNotFound = false;
        } else {
            $nomAncetre = $result[2];
            $parentID = intval($result[1]);
        }
        $i++;
    }
    return $nomAncetre;
}

function prepare_error_log($folder_name, $filename){
    $error_log_dir = ERROR_LOG_FOLDER .'\\'. $folder_name;
    $error_log_file = $error_log_dir . '\\'. $filename;
    if (!is_dir($error_log_dir)){
        mkdir($error_log_dir, 0777, true);
    }
    
    $fd = fopen($error_log_file, 'w');
    ftruncate($fd, 0);
    fclose($fd);
}

function remove_spaces($string) {
    return rtrim(ltrim($string, ' '), ' ');
}

function transpose($array) {
    return array_map(null, ...$array);
}

/*
 * Input validation rules
 */

function input_validation_contains_errors($input_validation) {
    if ($input_validation) throw new Exception(GLOBAL_EXCEPTIONS[0]);
}

function validate_input($input, $filter, $input_name) {
    $errors = array();
    foreach ($filter as $f) {
        switch ($f):
            case "NOT_NULL": $validation_result = check_input_array_for_empty_values($input); break;
            case "INTEGER": $validation_result = check_if_input_is_integer($input); break;
            case "GT_ZERO": $validation_result = check_if_input_is_greater_than_zero($input); break;
        endswitch;
        if ($validation_result) array_push($errors, $validation_result);
    }
    return count($errors)?build_input_error_message($input_name, $errors):NULL;
}

function check_input_array_for_empty_values($input) {
    return !in_array(NULL, $input)?false:INPUT_EXCEPTIONS[0];
}

function check_if_input_is_integer($input) {
    return is_integer($input)?false:INPUT_EXCEPTIONS[1];
}

function check_if_input_is_greater_than_zero($input) {
    return $input>0?false:INPUT_EXCEPTIONS[2];
}

function build_input_error_message($input_name, $errors) {
    $error_message = "Error with passed parameter '". $input_name . "':";
    foreach ($errors as $e) {
        $error_message .= " " . $e;
    }
    return $error_message;
}

function replace_sql_null_values($column, $value) {
    $default_label = (is_null($value) && array_key_exists($column, DEFAULT_LABELS)) ? DEFAULT_LABELS[$column] : $value;
    return $default_label;
}

function sum_equi_dimensional_array($array1, $array2) {
    $array_sum = array();
    for ($i=0; $i<count($array1); $i++) {
        for ($j=0; $j<count($array2[$i]); $j++) {
            $array_sum[$i][$j] = $array1[$i][$j] + $array2[$i][$j];
        }
    }
    return $array_sum;
}