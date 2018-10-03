<!DOCTYPE html>
<html lang="en">
<head>

  <title>GenIS</title>

  <?php 
  require_once '../libraries/constants.php';
  require_once HEAD_START;
  ?>

  <!--Optional sources start -->
  <script type="text/javascript" src="js/script_mort.js"></script>

  <!-- Optional sources end -->

</head>

<body>

<?php

session_start();

$_SESSION['current_page']='mort';

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
        <div class="pull-left">Décès</div>
        <div class="widget-icons pull-right">
          <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
          <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="widget-content">
        <div class="padd">
          <!-- Content goes here -->

          <!-- Début de formulaire pour la mort d'un animal -->

          <form class="form-horizontal" id="mort" role="form" action="insert_db_mort.php" method="GET" name="mort">

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
              <label class="col-lg-2 control-label" for="deathDate">Date du décès</label>
              <div id="datetimepicker1" class="input-append input-group dtpicker datetimepicker1" style="padding-left: 15px;">
                <input id="deathDate" name="deathDate" data-format="yyyy-MM-dd" placeholder="AAA-MM-JJ" class="form-control" type="text" required>
                          <span class="input-group-addon add-on">
                              <i class="fa fa-calendar" data-time-icon="fa fa-times" data-date-icon="fa fa-calendar"></i>
                          </span>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-2 control-label" for="deathFarm">Lieu du décès</label>
              <div class="col-lg-5">
                <input class="form-control" placeholder="Lieu du décès" type="text" name="deathFarm" id="deathFarm" readonly required>
              </div>
            </div>

            <input type="text" class="hidden" id="animalId" name="animalId" value="1">
            <input type="text" class="hidden" id="farmId" name="farmId">

            <div class="col-lg-offset-2 col-lg-6">
              <input name="deathValid" type="submit" class="btn btn-sm btn-success" value="Valider">
              <a href="naissances.php"><button type="button" class="btn btn-sm btn-primary">Recommencer</button></a>
              <a href="../index.php"><button type="button" class="btn btn-sm btn-danger" href="">Annuler</button></a>
            </div>

          </fieldset>

          </form>

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