<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>

  <title>GenIS</title>

  <?php 
  require_once '../libraries/constants.php';
  require_once HEAD_START;
  ?>

  <!--Optional sources start -->
  <script type="text/javascript" src="js/script_select.js"></script>
  <!-- Optional sources end -->

</head>

<body>

<?php
session_start();

$_SESSION['current_page']='calcGen';

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
        <div class="pull-left">Sélection des animaux</div>
        <div class="widget-icons pull-right">
          <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
          <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="widget-content">
        <div class="padd">
          <!-- Content goes here -->

          <!-- Début du formulaire pour la sélection des animaux sur lesquels effectuer des calculs -->
          <br>
          <form class="form-horizontal" id="animalSelection" name="animalSelection" method="GET" role="form">
            
            <fieldset>
              <legend class="customLegend"><span class="legend">Caractéristiques animales</span></legend>

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

                  tableau_choix($array_code_espece,$array_label_espece,'espece',11,'onchange="fillup_race()" id="espece" required');

                  ?>
                </div>
              </div>
              <hr>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="race">Race</label>
                <div class="col-lg-3">
                  <select name="race" id="race" size="1" class="form-control" disabled required>
                  </select>
                </div>
              </div>
              <!--<hr>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="animalMale">Sexe</label>
                <div class="col-lg-8">
                  <div class="radio">
                    <table style="width: 100%;">
                      <tbody>
                        <tr>
                          <td style="width: 25%;">
                            <label>
                              <input name="animalSex" id="animalMale" value="1" type="radio">
                              Mâle reproducteur
                            </label>
                          </td>
                          <td style="width: 25%;">
                            <label>
                              <input name="animalSex" id="animalFemale" value="2" type="radio">
                              Femelle
                            </label>
                          </td>
                          <td style="width: 25%;">
                            <label>
                              <input name="animalSex"  id="animalAll" value="3" type="radio" checked>
                              Tous
                            </label>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>-->
              <hr>
              <div class="form-group">
                <label class="col-lg-2 control-label">Période de naissance</label>
                <table>
                  <tbody>
                    <tr>
                      <td style="padding-left: 15px;">
                        Du
                      </td>
                      <td>
                        <div class="input-append input-group dtpicker datetimepicker1" style="padding-left: 15px;">
                            <input id="minBirthDate" name="minBirthDate" data-format="yyyy-MM-dd" placeholder="AAA-MM-JJ" class="form-control" type="text">
                          <span class="input-group-addon add-on">
                            <i class="fa fa-calendar" data-time-icon="fa fa-times" data-date-icon="fa fa-calendar"></i>
                          </span>
                        </div>
                      </td>
                      <td style="padding-left: 1em;">
                        Au
                      </td>
                      <td>
                        <div class="input-append input-group dtpicker datetimepicker1" style="padding-left: 15px;">
                            <input id="maxBirthDate" name="maxBirthDate" data-format="yyyy-MM-dd" placeholder="AAA-MM-JJ" class="form-control" type="text">
                          <span class="input-group-addon add-on">
                            <i class="fa fa-calendar" data-time-icon="fa fa-times" data-date-icon="fa fa-calendar"></i>
                          </span>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <hr>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="repro1">Reproducteurs</label>
                <div class="col-lg-3">
                  <div class="radio">
                    <table style="width: 50%;">
                      <tbody>
                        <tr>
                          <td style="width: 25%;">
                            <label>
                                <input name="repro" id="repro1" value="1" type="radio" checked="true">
                              Oui
                            </label>
                          </td>
                          <td style="width: 25%;">
                            <label>
                              <input name="repro" id="repro2" value="2" type="radio">
                              Non
                            </label>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <hr>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="vivant1">Etat</label>
                <div class="col-lg-5">
                  <div class="radio">
                    <table style="width: 50%;">
                      <tbody>
                        <tr>
                          <td style="width: 25%;">
                            <label>
                              <input name="etat" id="vivant" value="1" type="radio">
                              Vivant
                            </label>
                          </td>
                          <td style="width: 25%;">
                            <label>
                              <input name="etat" id="mort" value="2" type="radio">
                              Mort
                            </label>
                          </td>
                          <td style="width: 25%;">
                            <label>
                              <input name="etat"  id="all" value="3" type="radio" checked>
                              Tous
                            </label>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </fieldset>
            <br>
            <!--<fieldset>
              <legend class="customLegend"><span class="legend">Caractéristiques géographiques</span></legend>

              <div class="form-group">
                <label class="col-lg-2 control-label" for="elevage">Elevage</label>
                <div class="col-lg-5">
                  <input class="form-control" placeholder="Elevage" type="text" name="elevage" id="elevage" onkeyup="triggerAutoCompleteFarm(event)">
                </div>
              </div>
              <hr>
              <div class="form-group">
                <label class="col-lg-2 control-label">Période de présence</label>
                <table>
                  <tbody>
                  <tr>
                    <td style="padding-left: 15px;">
                      Du
                    </td>
                    <td>
                      <div class="input-append input-group dtpicker datetimepicker1" style="padding-left: 15px;">
                        <input id="startPeriod" name="startPeriod" data-format="yyyy-MM-dd" placeholder="AAA-MM-JJ" class="form-control" type="text">
                          <span class="input-group-addon add-on">
                              <i class="fa fa-calendar" data-time-icon="fa fa-times" data-date-icon="fa fa-calendar"></i>
                          </span>
                      </div>
                    </td>
                    <td style="padding-left: 1em;">
                      Au
                    </td>
                    <td>
                      <div class="input-append input-group dtpicker datetimepicker1" style="padding-left: 15px;">
                        <input id="endPeriod" name="endPeriod" data-format="yyyy-MM-dd" placeholder="AAA-MM-JJ" class="form-control" type="text">
                          <span class="input-group-addon add-on">
                              <i class="fa fa-calendar" data-time-icon="fa fa-times" data-date-icon="fa fa-calendar"></i>
                          </span>
                      </div>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </fieldset>-->
            <hr>
            <fieldset>
              <!--<input type="hidden" id="hidElevage" name="hidElevage">-->

              <div class="col-lg-offset-2 col-lg-6">
                <input type="button" id="submitSelection" name="submitSelection" onclick="validSelect()" value="Afficher le résultat" class="btn btn-sm btn-success" disabled>
                <a href="selectionPedig.php"><button type="button" class="btn btn-sm btn-primary">Recommencer</button></a>
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