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

$_SESSION['current_page']='prop';

require BODY_START;

/*
 * Starting connection to database
 */

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

?>

<div class="row">
  <div class="col-md-12">

    <div class="widget">
      <div class="widget-head">
        <div class="pull-left">Propriétaire</div>
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
          
          $id = $_GET['animalId'];
          $date = $_GET['propriChangeDate'];
          $farmID = $_GET['dFarmId'];
          
          //Début de transaction pour les requêtes
          try {
            $con -> beginTransaction();

            //La première requete permet de mettre à jour la période de propriété d'un élevage pour lui mettre une date de fin
            $sql1 = "UPDATE ". DB_NAME .".periode p
                     SET p.date_sortie = '$date'
                     WHERE p.id_type = 4
                       AND p.date_sortie IS NULL
                       AND p.id_animal = ". $id;

            //La 2e requête ajoute une nouvelle période de propriété dans la BDD
            $sql2 = "INSERT INTO periode(date_entree, date_sortie, valide_periode, id_animal, id_elevage, id_type)
					VALUES('$date',NULL, 1,'$id','$farmID',4)";

            $query1 = $con -> query($sql1);
            $query2 = $con -> query($sql2);

            //Pas d'erreur jusque là donc on peut faire le commit()
            $transactioncommit= $con -> commit();

          } catch (Exception $e) {
            $con -> rollback();
          }

          if (isset($e)){
            $err = $con -> errorCode();

            echo '
            <div class="alert alert-warning">
                <b>Un problème est survenu !</b> L\'erreur N°'. $err .' a été retournée : <br><br>';
                echo '<i>'. $e .'</i>';
            echo '
            </div>';

          } else {
            echo '
            <div class="alert alert-success">
                <b>Insertion réussie !</b> Le changement de propriété a bien été enregistrée.
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

<?php require BODY_END;?>

<!--Optional scripts start -->

<!-- Optional scripts end -->

</body>
</html>