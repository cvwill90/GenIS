<!DOCTYPE html>
<html lang="en">
<head>

  <title>GenIS</title>

  <?php 
  require_once '../libraries/constants.php';
  require_once HEAD_START;
  ?>

  <!--Optional sources start -->
  <script type="text/javascript" src="js/script_proprietaire.js"></script>

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

          <!-- Début de formulaire pour le changement de propriétaire d'un animal -->

          <form class="form-horizontal" id="proprietaire" role="form" action="insert_db_proprietaire.php" method="GET" name="proprietaire">

            <fieldset>

              <div class="form-group">
                <label class="col-lg-2 control-label" for="espece">Espèce</label>
                <div class="col-lg-3">
                  <?php
                  $sql_especes = "SELECT id_espece, lib_espece FROM espece ORDER BY lib_espece";

                  $query = pdo_sql_query($con,$sql_especes);

                  $array_code_espece = array();
                  $array_label_espece = array();
                  while ($result_especes = $query -> fetch()){
                    array_push($array_code_espece,$result_especes[0]);
                    array_push($array_label_espece,$result_especes[1]);
                  }

                  tableau_choix($array_code_espece,$array_label_espece,'espece',1,'onchange="fillup_race()" id="espece" required');

                  ?>
                </div>
              </div>

              <div class="form-group">
                <label class="col-lg-2 control-label" for="race">Race</label>
                <div class="col-lg-3">
                  <select name="race" id="race" size="1" class="form-control" disabled required>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-lg-2 control-label" for="animalID">N° d'identification/Nom de l'animal</label>
                <div class="col-lg-5">
                  <input class="form-control" placeholder="Sélectionner un animal de la liste" type="text" name="animalID" id="animalID" onkeyup="triggerAutocompleteAnimal(event)" onblur="check_if_empty('animalID','animalId')" disabled required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-lg-2 control-label" for="proprietaireActu">Ancien élevage propriétaire</label>
                <div class="col-lg-5">
                  <input class="form-control" placeholder="Ancien élevage" type="text" name="proprietaireActu" id="proprietaireActu" readonly>
                </div>
              </div>

              <div class="form-group">
                <label class="col-lg-2 control-label" for="proprietaireFutur">Futur élevage propriétaire</label>
                <div class="col-lg-5">
                  <input class="form-control" placeholder="Veuillez choisir le nouvel élevage propriétaire" type="text" name="proprietaireFutur" id="proprietaireFutur" onkeyup="triggerAutoCompleteFarm(event)" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-lg-2 control-label" for="propriChangeDate">Date du changement de propriétaire</label>
                <div id="datetimepicker1" class="input-append input-group dtpicker datetimepicker1" style="padding-left: 15px;">
                  <input id="propriChangeDate" name="propriChangeDate" data-format="yyyy-MM-dd" placeholder="AAA-MM-JJ" class="form-control" type="text">
                          <span class="input-group-addon add-on">
                              <i class="fa fa-calendar" data-time-icon="fa fa-times" data-date-icon="fa fa-calendar"></i>
                          </span>
                </div>
              </div>

              <input type="text" class="" id="animalId" name="animalId" style="display:none">
              <input type="text" class="" id="oFarmId" name="oFarmId" style="display:none">
              <input type="text" class="" id="dFarmId" name="dFarmId" style="display:none">

              <div class="col-lg-offset-2 col-lg-6">
                <input name="moveValid" type="submit" class="btn btn-sm btn-success" value="Valider">
                <a href="mouvement.php"><button type="button" class="btn btn-sm btn-primary">Recommencer</button></a>
                <a href="../index.php"><button type="button" class="btn btn-sm btn-danger" href="">Annuler</button></a>
              </div>

            </fieldset>

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