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
$_SESSION['current_page']='modifyContact';

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
            <div class="pull-left">Choisissez un contact existant</div>
            <div class="widget-icons pull-right">
              <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
              <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="widget-content">
            <div class="padd">
                <!-- Content goes here -->
                <fieldset>
                    <div class="form-group">
                      <label class="col-lg-2 control-label">Nom</label>
                      <div class="col-lg-5">
                          <input type="text" class="form-control" placeholder="" name="chooseContact" id="chooseContact" value="" onkeyup="autocompleteContact(event)">
                      </div>
                    </div>
                </fieldset>
            </div>
          </div>
          <div class="widget-foot">
              <!-- Footer goes here -->
          </div>
        </div>    
      </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="widget">
                <div class="widget-head">
                  <div class="pull-left">Modifier contact</div>
                  <div class="widget-icons pull-right">
                    <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
                    <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
                  </div>
                  <div class="clearfix"></div>
                </div>
                <div class="widget-content">
                  <div class="padd">
                    <!-- Content goes here -->
                      <form id="modifyContact" class="form-horizontal" role="form" method="post">

                      <ul id="coordContact" class="nav nav-tabs">
                        <li class="active"><a href="#coord" data-toggle="tab">Coordonnées</a></li>
                        <li><a href="#elev" data-toggle="tab">Elevage</a></li>
                      </ul>

                      <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade in active" id="coord">
                          <fieldset>
                            <legend>Informations du contact</legend>
                            <div class="form-group">
                              <label class="col-lg-2 control-label">Nom</label>
                              <div class="col-lg-5">
                                  <input type="text" class="form-control" placeholder="" name="nom" id="nom" value="" required="true">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-lg-2 control-label">Prénom</label>
                              <div class="col-lg-5">
                                  <input type="text" class="form-control" placeholder="" name="prenom" id="prenom" value="">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-lg-2 control-label">Adresse</label>
                              <div class="col-lg-5">
                                  <input type="text" class="form-control" placeholder="" name="adresse" id="adresse" value="">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label">Complément d'adresse</label>
                              <div class="col-lg-5">
                                <textarea class="form-control" rows="5" placeholder="" style="resize: vertical" name="adresseCompl" id="adresseCompl"></textarea>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label" for="espece">Région</label>
                              <div class="col-lg-3">
                                <?php
                                $sql_regions = "SELECT no_region, lib_region FROM region ORDER BY lib_region";

                                $query = pdo_sql_query($con,$sql_regions);

                                $array_code_region = array();
                                $array_label_region = array();
                                while ($result_regions = $query -> fetch()){
                                  array_push($array_code_region,$result_regions[0]);
                                  array_push($array_label_region,$result_regions[1]);
                                }

                                tableau_choix($array_code_region,$array_label_region,'region',2,'onchange="fillup_dep()" id="region" required');

                                ?>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-lg-2 control-label" for="race">Département</label>
                              <div class="col-lg-3">
                                <select name="departement" id="departement" size="1" class="form-control" required>
                                  <option value="24">Dordogne</option>
                                  <option value="33" selected>Gironde</option>
                                  <option value="40">Landes</option>
                                  <option value="47">Lot-et-Garonne</option>
                                  <option value="64">Pyrénées-Atlantiques</option>
                                </select>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label">Ville</label>
                              <div class="col-lg-5">
                                  <input type="text" class="form-control" placeholder="" id="ville" name="ville" value="" onkeyup="autocompleteTown(event)" required="true">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label">Code postal</label>
                              <div class="col-lg-5">
                                  <input type="text" class="form-control" placeholder="" id="codePostal" name="codePostal" required="true" disabled="true">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label">Adresse Mail</label>
                              <div class="col-lg-5">
                                  <input type="email" class="form-control" placeholder="" name="mail" id="mail" value="">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label">Numéro de téléphone 1</label>
                              <div class="col-lg-5">
                                  <input type="email" class="form-control" placeholder="" name="tel1" id="tel1" value="">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label">Numéro de téléphone 2</label>
                              <div class="col-lg-5">
                                  <input type="email" class="form-control" placeholder="" name="tel2" id="tel2" value="">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label">Notes</label>
                              <div class="col-lg-5">
                                  <textarea class="form-control" rows="5" placeholder="" style="resize: vertical" name="notes" id="notes"></textarea>
                              </div>
                            </div>

                            <div class="form-group" style="display:none">
                              <input type="text" id="idVille" name="idVille" value="1">
                            </div>
                          </fieldset>
                        </div>

                        <div class="tab-pane fade" id="elev">
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Le contact est un éleveur</label>
                            <div class="col-lg-5">
                              <div class="radio">
                                <label>
                                    <input type="radio" name="eleveur" id="eleveur1" value="1" onchange="radioChange()">
                                  Oui
                                </label>
                              </div>
                              <div class="radio">
                                <label>
                                    <input type="radio" name="eleveur" id="eleveur2" value="0" onchange="radioChange()" checked="checked">
                                  Non
                                </label>
                              </div>
                            </div>
                          </div>
                          <fieldset class="fieldsetEleveur" style="display: none;">
                            <legend>Informations de l'élevage</legend>
                            
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Insérer un nouvel élevage ?</label>
                                <div class="col-lg-5">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" id="nouvelElevage" name="nouvelElevage" value="1">
                                    </label>
                                  <!--<div class="radio">
                                    <label>
                                      <input type="radio" name="existElevage" id="nouvelElevage" value="1" checked="" onclick="nonexistingFarm()">
                                      Oui
                                    </label>
                                  </div>
                                  <div class="radio">
                                    <label>
                                      <input type="radio" name="existElevage" id="ancienElevage" value="0" onclick="existingFarm(true)">
                                      Non
                                    </label>
                                  </div>-->
                                </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="col-lg-2 control-label">Nom de l'élevage</label>
                              <div class="col-lg-5">
                                  <input type="text" class="form-control" placeholder="" name="nomElevage" id="nomElevage" value="" onkeyup="triggerAutoCompleteFarm(event, true)" required="true">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label" for="animalID">N° d'identification de l'élevage</label>
                              <div class="col-lg-5">
                                  <input class="form-control" placeholder="exemple : 3365982645" type="number" name="idElevage" id="idElevage" value="" maxlength="10">
                              </div>
                            </div>

                            <div class="form-group" style="display: none;">
                              <input type="text" id="idDbElevage" name="idDbElevage">
                            </div>
                          </fieldset>
                          <fieldset class="fieldsetEleveur" style="display:none;">
                            <legend>Races élevées</legend>

                            <?php
                            $con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

                            $sql = "SELECT e.id_espece, lib_espece, code_race, lib_race FROM race r left join espece e on e.id_espece = r.id_espece ORDER BY lib_espece asc";
                            $queryRaces = $con->query($sql);

                            $i = 1;
                            $prevSpecie='';
                            $checkboxColor = '';
                            while ($list = $queryRaces->fetch()){

                              if($list['id_espece'] != 1){
                                if ($list["id_espece"] != $prevSpecie && $prevSpecie!=''){
                                  echo '</table>
                                      </div>
                                      </div>';
                                }

                                if($list["id_espece"]!=$prevSpecie){

                                  if ($i == 1)
                                    $checkboxColor='sw-orange';
                                  elseif ($i == 2)
                                    $checkboxColor='sw-green';
                                  elseif ($i == 3)
                                    $checkboxColor='sw-red';
                                  else {
                                    $checkboxColor='sw-bleu';
                                    $i=0;
                                  }

                                  echo '<div class="form-group">
                                        <label class="col-lg-2 control-label">'. $list["lib_espece"] .'</label>
                                        <div class="col-lg-5">
                                            <table>';
                                  echo '<tr>
                                                <td style="padding: 0 10px 0 10px; width: 250px;">'. $list["lib_race"] .'</td>
                                                <td style="padding: 0 10px 0 10px">
                                                  <div class="'. $checkboxColor .'">
                                                    <div class="onoffswitch toggleBtn">
                                                      <input id="race'. $list["code_race"] .'" name="races[]" value="'. $list["code_race"] .'" type="checkbox" class="toggleBtn onoffswitch-checkbox"><label for="race'. $list["code_race"] .'" class="onoffswitch-label"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div>
                                                  </div>
                                                </td>
                                              </tr>';
                                  $i++;
                                } else {
                                  echo '<tr>
                                                <td style="padding: 0 10px 0 10px; width: 250px;">'. $list["lib_race"] .'</td>
                                                <td style="padding: 0 10px 0 10px">
                                                  <div class="'. $checkboxColor .'">
                                                    <div class="onoffswitch toggleBtn">
                                                      <input id="race'. $list["code_race"] .'" name="races[]" value="'. $list["code_race"] .'" type="checkbox" class="toggleBtn onoffswitch-checkbox"><label for="race'. $list["code_race"] .'" class="onoffswitch-label"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div>
                                                  </div>
                                                </td>
                                              </tr>';
                                }
                              }
                              $prevSpecie = $list["id_espece"];
                            }
                            echo '</table>
                                      </div>
                                      </div>';

                            ?>

                          </fieldset>
                        </div>
                      </div>

                      <input type="hidden" id="idDbContact" name="idDbContact">
                    </form>

                  </div>
                </div>
                <div class="widget-foot">
                    <!-- Footer goes here -->
                    <div class="form-group">
                        <button type="button" id="contactValid" class="btn btn-sm btn-success" onclick="modifyContactDB()" disabled="true">Valider</button>
                        <a href="nouveauContact.php"><button type="button" class="btn btn-sm btn-primary">Recommencer</button></a>
                        <a href="../index.php"><button type="button" class="btn btn-sm btn-danger" href="">Annuler</button></a>
                  </div>
                </div>
            </div>
        </div>
    </div>

<?php require BODY_END;?>

<!--Optional scripts start -->

<script type="text/javascript" src="js/scriptContacts.js"></script>

<!-- Optional scripts end -->

</body>
</html>