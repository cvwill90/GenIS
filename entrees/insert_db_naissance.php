<!DOCTYPE html>
<html lang="en">
    <head>

        <title>GenIS</title>

        <?php
        require_once '../libraries/constants.php';
        require_once HEAD_START;
        ?>

        <!--Optional sources start -->

        <!-- Optional sources end -->

    </head>

    <body>

        <?php
        session_start();

        $_SESSION['current_page'] = 'naiss';

        require BODY_START;

        /*
         * Starting connection to database
         */

        $con = pdo_connection(HOST_DB, DB_NAME, USER_DB, PW_DB);
        ?>

        <div class="row">
            <div class="col-md-12">

                <div class="widget">
                    <div class="widget-head">
                        <div class="pull-left">Naissance</div>
                        <div class="widget-icons pull-right">
                            <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
                            <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="widget-content">
                        <div class="padd">
                            <!-- Content goes here -->

<?php
$race = (isset($_GET['race'])) ? intval($_GET['race']) : null;
$nom = (isset($_GET['animalName'])) ? $_GET['animalName'] : null;
$sexe = (isset($_GET['animalSex'])) ? $_GET['animalSex'] : null;
$cra = (isset($_GET['conserv'])) ? $_GET['conserv'] : null;
$id = (isset($_GET['animalID'])) ? $_GET['animalID'] : null;
$date = (isset($_GET['birthDate'])) ? $_GET['birthDate'] : null;
$farmID = (isset($_GET['farmId'])) ? $_GET['farmId'] : null;
$livre_gene = (isset($_GET['livre_gene'])) ? intval($_GET['livre_gene']) : null;
$pere = (isset($_GET['fatherId'])) ? $_GET['fatherId'] : 1;
$mere = (isset($_GET['motherId'])) ? $_GET['motherId'] : 2;
$pourcentage_sang_race = (isset($_GET['pourcentage_sang_animal'])) ? floatval($_GET['pourcentage_sang_animal']) : null;

//Début de transaction pour les requêtes
try {
    $con->beginTransaction();
    // 1.Insertion de l'animal né dans la table animal
    $sql1 = "INSERT INTO animal (id_animal, nom_animal, sexe, no_identification, date_naiss, reproducteur, fecondation, coeff_consang, conservatoire, valide_animal, code_race, id_pere, id_mere)
            VALUES (NULL,'{$nom}',{$sexe},'{$id}','{$date}',0,0,0,{$cra},0,{$race},{$pere},{$mere})";

    // On exécute les requetes les unes après les autres

    $query1 = $con->query($sql1);
    // 2. On récupère l'id de la dernière ligne enregistrée dans la BD
    $animal_id = $con->lastInsertId();
    
    // 3. On annonce la naissance :
    $sql2 = "INSERT INTO periode(date_entree, date_sortie, valide_periode, id_animal, id_elevage, id_type)
            VALUES('$date',NULL,0,$animal_id,$farmID,3)";

    // 4. On déclare le séjour dans l'élevage :
    $sql3 = "INSERT INTO periode(date_entree, date_sortie, valide_periode, id_animal, id_elevage, id_type)
            VALUES('$date',NULL,0,$animal_id,$farmID,2)";

    // 5. On déclare l'élevage naisseur, aussi élevage propriétaire
    $sql4 = "INSERT INTO periode(date_entree, date_sortie, valide_periode, id_animal, id_elevage, id_type)
            VALUES('$date',NULL,0,$animal_id,$farmID,4)";
        
    // Exécution des requêtes 2, 3 et 4
    $query2 = $con->query($sql2);
    $query3 = $con->query($sql3);
    $query4 = $con->query($sql4);
    
    
    // 6. Au cas ou l'animal est un veau à titre initial, on le déclare en tant que tel dans la bd
    if (($pere == 1 || $mere == 2) && in_array($race, RACES_SOUMISES_A_GESTION_GENETIQUES)) {
        $sql5 = "INSERT INTO ancetre(id_ancetre, type_ancetre, pourcentage_sang_race, id_livre)"
                . "VALUES ($animal_id, 'titre_initial', $pourcentage_sang_race, $livre_gene)";
        $query5 = $con->query($sql5);
    }

    //Pas d'erreur jusque là donc on peut faire le commit()
    $transactioncommit = $con->commit();
} catch (Exception $e) {
    $con->rollback();
}

if (isset($e)) {
    $err = $con->errorCode();

    echo '
            <div class="alert alert-warning">
                <b>Un problème est survenu !</b> L\'erreur N°' . $err . ' a été retournée : <br><br>';
    echo '<i>' . $e . '</i>';
    echo '
            </div>';
} else {
    echo '
            <div class="alert alert-success">
                <b>Insertion réussie !</b> La naissance a bien été enregistrée.
            </div>';
}
?>

                        </div>
                        <div class="widget-foot">
                            <!-- Footer goes here -->
                        </div>
                    </div>
                </div>

            </div>
        </div>

<?php require BODY_END; ?>

        <!--Optional scripts start -->

        <!-- Optional scripts end -->

    </body>
</html>