<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>

  <title>GenIS</title>

  <?php 
  require_once '../libraries/constants.php';
  require_once HEAD_START;
  ?>

  <!--Optional sources start -->
  <script type="text/javascript" src="js/script_naissances.js"></script>
  <!-- Optional sources end -->

</head>

<body>

<?php
session_start();

$_SESSION['current_page']='naiss';

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

          <!-- Début de formulaire pour la naissance d'un animal -->

          <form onsubmit="return checkForm()" class="form-horizontal" id="naissance" role="form" action="insert_db_naissance.php" method="GET" name="naissance">

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
              <label class="col-lg-2 control-label" for="fatherID">N° d'identification/Nom du père</label>
              <div class="col-lg-5">
                <input class="form-control" placeholder="Sélectionner un mâle de la liste" type="text" name="fatherID" id="fatherID" onkeyup="triggerAutocompleteMale(event)" onblur="check_if_empty('fatherID','fatherId')" disabled>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-2 control-label" for="motherID">N° d'identification/Nom de la mère</label>
              <div class="col-lg-5">
                <input class="form-control" placeholder="Sélectionner une femelle de la liste" type="text" name="motherID" id="motherID" onkeyup="triggerAutocompleteFemale(event)" onblur="check_if_empty('motherID','motherId')" disabled>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-2 control-label" for="lignee">Lignée</label>
              <div class="col-lg-5">
                <input class="form-control" style="font-style: italic; color: slategrey" placeholder="Lignée" type="text" name="lignee" id="lignee" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-2 control-label" for="famille">Famille</label>
              <div class="col-lg-5">
                <input class="form-control" style="font-style: italic; color: slategrey" placeholder="Famille" type="text" name="famille" id="famille" readonly>
              </div>
            </div>

          </fieldset>
          <fieldset>
            <div class="form-group">
              <label class="col-lg-2 control-label" for="animalID">N° d'identification de l'animal</label>
              <div class="col-lg-5">
                <input class="form-control" placeholder="exemple : 3365982645" type="number" name="animalID" id="animalID" maxlength="12" required>
              </div>
            </div>
            <div class="form-group">
              <label class="col-lg-2 control-label" for="animalName">Nom de l'animal</label>
              <div class="col-lg-5">
                <input class="form-control" placeholder="Nom de l'animal" type="text" name="animalName" id="animalName" required>
              </div>
            </div>
            <div class="form-group">
              <label class="col-lg-2 control-label" for="animalMale">Mâle</label>
              <div class="col-lg-5">
                <div class="radio">
                  <label>
                    <input name="animalSex" id="animalMale" value="1" checked="checked" type="radio">
                    Mâle
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input name="animalSex" id="animalFemale" value="2" type="radio">
                    Femelle
                  </label>
                </div>
              </div>
            </div>
            <div class="form-group">
                <label class="col-lg-2 control-label" for="livre_gene">Livre généalogique</label>
                <div class="col-lg-2">
                    <select id="livre_gene" name="livre_gene" class="form-control">
                        <option value="NULL" selected>Non applicable</option>
                        <option value="1">Livre principal</option>
                        <option value="2">Livre annexe</option>
                        <option value="3">Hors-livre</option>
                  </select>
                </div>
            </div>
            <div class="form-group">
              <label class="col-lg-2 control-label" for="birthDate">Date de naissance</label>
              <div id="datetimepicker1" class="input-append input-group dtpicker datetimepicker1" style="padding-left: 15px;">
                <input id="birthDate" name="birthDate" data-format="yyyy-MM-dd" placeholder="AAA-MM-JJ" class="form-control" type="text" required>
                          <span class="input-group-addon add-on">
                              <i class="fa fa-calendar" data-time-icon="fa fa-times" data-date-icon="fa fa-calendar"></i>
                          </span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-lg-2 control-label" for="birthFarm">Lieu de naissance</label>
              <div class="col-lg-5">
                <input class="form-control" placeholder="Lieu de naissance" type="text" name="birthFarm" id="birthFarm" onkeyup="triggerAutoCompleteFarm(event)" required>
              </div>
            </div>
            <div class="form-group">
              <label class="col-lg-2 control-label" for="conserv">Appartient au conservatoire</label>
              <div class="col-lg-5">
                <div class="radio">
                  <label for="conserv1">
                    <input name="conserv" id="conserv1" value="1" checked="checked" type="radio">
                    Non
                  </label>
                </div>
                <div class="radio">
                  <label for="conserv2">
                    <input name="conserv" id="conserv2" value="2" type="radio">
                    Oui
                  </label>
                </div>
              </div>
            </div>
          </fieldset>
          <fieldset>
            <input type="text" class="" id="fatherId" name="fatherId" value="1" style="display:none">
            <input type="text" class="" id="motherId" name="motherId" value="2" style="display:none">
            <input type="text" class="" id="farmId" name="farmId" style="display:none">

            <div class="col-lg-offset-2 col-lg-6">
              <button id="birthValid" type="button" class="btn btn-sm btn-success">Valider</button>
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
<script type="text/javascript">
    $('#birthValid').on('click', function() {
        $('#naissance').submit();
    });
</script>
<!-- Optional scripts end -->


</body>
</html>