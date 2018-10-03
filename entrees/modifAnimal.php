<!DOCTYPE html>
<html lang="en">
<head>

  <title>GenIS</title>

  <?php 
  require_once '../libraries/constants.php';
  require_once HEAD_START;
  ?>

  <!--Optional sources start -->
  <script type="text/javascript" src="js/script_modif.js"></script>
  <!-- Optional sources end -->

</head>

<body>

<?php
session_start();
$_SESSION['current_page']='modifAnim';

require BODY_START;

/*
 * Starting connection to database
 */

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

?>

<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal" id="formModif" role="form" action="insert_db_mort.php" method="GET" name="mort">

            <div class="widget">
                <div class="widget-head">
                  <div class="pull-left">Choisir un animal à modifier</div>
                  <div class="widget-icons pull-right">
                    <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
                    <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
                  </div>
                  <div class="clearfix"></div>
                </div>
                <div class="widget-content">
                  <div class="padd">
                    <!-- Content goes here -->

                    <!-- Début de formulaire pour la modification d'un animal -->

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
                        <label class="col-lg-2 control-label" for="chooseAnimal">N°/Nom de l'animal à modifier</label>
                        <div class="col-lg-5">
                            <input class="form-control" placeholder="Sélectionner un animal de la liste" type="text" name="chooseAnimal" id="chooseAnimal" onkeyup="triggerAutocompleteAnimal(event)" onblur="check_if_empty('chooseAnimal','IDanimalChoisi')" disabled required>
                        </div>
                      </div>

                    </div>
                </div>
                <div class="widget-foot">
                  <!-- Footer goes here -->
                </div>
            </div>

            <div class="widget">
                <div class="widget-head">
                    <div class="pull-left">Données relatives à l'animal</div>
                    <div class="widget-icons pull-right">
                        <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
                        <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="widget-content">
                    <div class="padd">        
                        <fieldset>
                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="father">N°/Nom du père</label>
                            <div class="col-lg-5">
                              <input class="form-control" placeholder="Sélectionner un mâle de la liste" type="text" name="father" id="father" onkeyup="triggerAutocompleteMale(event)" onblur="check_if_empty('father','fatherId')" required>
                            </div>
                        </div>

                        <div class="form-group">
                              <label class="col-lg-2 control-label" for="mother">N°/Nom de la mère</label>
                              <div class="col-lg-5">
                                <input class="form-control" placeholder="Sélectionner une femelle de la liste" type="text" name="mother" id="mother" onkeyup="triggerAutocompleteFemale(event)" onblur="check_if_empty('mother','motherId')" required>
                              </div>
                        </div>

                        <!--<div class="form-group">
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
                        </div>-->

                        </fieldset>
                        <fieldset>
                        <div class="form-group">
                          <label class="col-lg-2 control-label" for="animalID">N° d'identification de l'animal</label>
                          <div class="col-lg-5">
                            <input class="form-control" placeholder="exemple : 3365982645" type="number" name="animalID" id="animalID" maxlength="10" required>
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
                                <input name="animalSex" id="animalCastre" value="3" type="radio">
                                Mâle Castré
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
                          <label class="col-lg-2 control-label" for="birthDate">Date de décès</label>
                          <table><tbody>
                              <tr>
                                  <td>
                                    <div class="input-append input-group dtpicker datetimepicker1" style="padding-left: 15px;">
                                      <input id="deathDate" name="deathDate" data-format="yyyy-MM-dd" placeholder="AAA-MM-JJ" class="form-control" type="text" required>
                                          <span class="input-group-addon add-on">
                                              <i class="fa fa-calendar" data-time-icon="fa fa-times" data-date-icon="fa fa-calendar"></i>
                                          </span>
                                    </div>
                                  </td>
                                  <td style="padding-left: 15px;">L'animal est mort</td>
                                  <td style="padding-left: 15px;">
                                      <input id="animal_dead" name="animal_dead" value="dead" type="checkbox" onclick="animal_death(event)">
                                  <td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="form-group">
                          <label class="col-lg-2 control-label" for="conserv">Appartient au conservatoire</label>
                          <div class="col-lg-5">
                            <div class="radio">
                              <label for="conserv1">
                                <input name="conserv" id="conserv1" value="0" checked="checked" type="radio">
                                Non
                              </label>
                            </div>
                            <div class="radio">
                              <label for="conserv2">
                                <input name="conserv" id="conserv2" value="1" type="radio">
                                Oui
                              </label>
                            </div>
                          </div>
                        </div>
                        </fieldset>
                    </div>
                </div>
                <div class="widget-foot">
                  <!-- Footer goes here -->
                  <fieldset>
                    <input type="hidden" class="" id="IDanimalChoisi" name="IDanimalChoisi" value="">
                    <input type="text" class="" id="fatherId" name="fatherId" value="1" style="display:none">
                    <input type="text" class="" id="motherId" name="motherId" value="2" style="display:none">
                    <input type="text" class="" id="farmId" name="farmId" value="0" style="display:none">

                    <div class="col-lg-offset-2 col-lg-6">
                        <button type="button" name="birthValid" type="submit" class="btn btn-sm btn-success" onclick="modifAnimal()">Valider</button>
                      <a href="naissances.php"><button type="button" class="btn btn-sm btn-primary">Recommencer</button></a>
                      <a href="../index.php"><button type="button" class="btn btn-sm btn-danger" href="">Annuler</button></a>
                    </div>

                  </fieldset>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require BODY_END;?>

<!--Optional scripts start -->

<!-- Optional scripts end -->

</body>
</html>