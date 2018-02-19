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
$_SESSION['current_page']='';

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
        <div class="pull-left">Choix du programme à utiliser</div>
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
          $race = $_SESSION['race'];
          ?>

          <p>
          Le fichier <b>ped_<?php echo $race; ?>.csv</b> a été créé/édité.<br><br>Vous pouvez changer son contenu en l'ouvrant dans le répertoire suivant :<br><b>C:\wamp64\www\genis.cra\calculs\pedigFiles\ped_<?php echo $race; ?>.csv</b>

          <br><br>

          Sélectionnez maintenant le programme à utiliser :
          </p>

          <ul id="myTab" class="nav nav-tabs">
            <li class="active"><a href="#meuw" data-toggle="tab">Meuw</a></li>
            <li><a href="#vanrad" data-toggle="tab">Vanrad</a></li>
            <li><a href="#prob_orig" data-toggle="tab">Prob_orig</a></li>
            <li><a href="#parente" data-toggle="tab">Parente</a></li>
          </ul>

          <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade in active" id="meuw">
              <form class="customForm" action='meuw.php' method='POST'>
                <b>Les paramètres de meuw.exe sont prédéfinis : vous pouvez tout de suite lancer le calcul.</b>
                  <BR><BR>
                  <p class="customP">
                    <label class="customLabel" for="entreeMeuw"><span class="customSpan">Le fichier d'entrée est : &nbsp;</span>
                      <input type='text' id='entreeMeuw' name='entreeMeuw' class='customInputs' value='ped_<?php echo $race; ?>.csv' readonly>
                    </label>
                  </p>
                  <p class="customP">
                    <label class="customLabel" for="sortieMeuw"><span class="customSpan">Nom du fichier de sortie : &nbsp;</span>
                      <input type='text' id='sortieMeuw' name='sortieMeuw' class='customInputs' value='meuw_<?php echo $race; ?>.csv' readonly>
                    </label>
                  </p>

                  <br>

                <input type='SUBMIT' name='meuw_submit' value='Lancer meuw.exe'>

              </form>
            </div>
            <div class="tab-pane fade" id="vanrad">
              <form class= "customForm" action='vanrad.php' method='POST'>
                  <fieldset class="customFieldset">
                    <legend class="customLegend"><span class="legend"> Méthode de Van Raden </legend>
                    <b>Les paramètres de vanrad.exe sont prédéfinis : vous pouvez tout de suite lancer le calcul.</b>
                    <BR><BR>
                    <p class="customP">
                      <label class="customLabel" for="entreeVanR"><span class="customSpan">Le fichier d'entrée est : &nbsp;</span>
                        <input type='text' id='entreeVanR' name='entreeVanR' class='customInputs' value='ped_<?php echo $race; ?>.csv' readonly>
                      </label>
                    </p>
                    <p class="customP">
                      <label class="customLabel" for="sortievanR"><span class="customSpan">Nom du fichier de sortie : &nbsp;</span>
                        <input type='text' id='sortieVanR' name='sortieVanR' class='customInputs' value='vanrad_<?php echo $race; ?>.csv' readonly>
                      </label>
                    </p>
                    <br>
                  </fieldset>
                  <input type='SUBMIT' name='vanrad_submit' value='Lancer vanrad.exe'>

                </form>
            </div>
            <div class="tab-pane fade" id="prob_orig">
              <form class= "customForm" action='prob_orig.php' method='POST'>

                  <fieldset class="customFieldset">
                    <legend class="customLegend"><span class="legend"> Probabilité d'origine des gènes </legend>
                    <b>Saisissez les paramètres d'entrée de prob_orig.exe : </b>

                    <BR><BR>

                    <p class="customP">
                      <label class="customLabel" for="entreePO"><span class="customSpan">Le fichier d'entrée est : &nbsp;</span>
                        <input type='text' id='entreePO' name='entreePO' class='customInputs'  value='ped_<?php echo $race; ?>.csv' readonly>
                      </label>
                    </p>

                    <p class="customP">
                      <label class="customLabel" for="sortiePO1"><span class="customSpan">Nom du fichier contenant les contributions des ancêtres : &nbsp;</span>
                        <input type='text' id='sortiePO1' name='sortiePO1' class='customInputs' value='contributions_ancetres_<?php echo $race; ?>.csv' readonly>
                      </label>
                    </p>
                    
                    <p class="customP">
                      <label class="customLabel" for="sortiePO"><span class="customSpan">Nom du fichier contenant la liste des ancêtres : &nbsp;</span>
                        <input type='text' id='sortiePO2' name='sortiePO2' class='customInputs' value='list_ancetres_<?php echo $race; ?>.csv' readonly>
                      </label>
                    </p>

                    <p class="customP">
                      <label class="customLabel" for="ancestors"><span class="customSpan">Nombre d'ancêtres &nbsp;</span>
                        <input type='text' id='ancestors' name='ancestors' class='customInputs'>
                      </label>
                    </p>

                    <p class="customP">
                      <label class="customLabel" for="male"><span class="customSpan">Sexe &nbsp;</span></label>
                      <label class="radioLabel" for="male">Mâle &nbsp;</label>
                      <input type='radio' id='male' name='sex' value='1' class='customInputs'>
                      <label class="radioLabel" for="female">Femelle &nbsp;</label>
                      <input type='radio' id='female' name='sex' value='2' class='customInputs'>
                    </p>

                    <p class="customP">
                      <label class="customLabel" for="startRef"><span class="customSpan">Première année de naissance de référence &nbsp;</span>
                        <input type='text' id='startRef' name='startRef' class='customInputs'>
                      </label>
                    </p>

                    <p class="customP">
                      <label class="customLabel" for="endRef"><span class="customSpan">Dernière année de naissance de référence &nbsp;</span>
                        <input type='text' id='endRef' name='endRef' class='customInputs'>
                      </label>
                    </p>

                    <br>

                  </fieldset>

                  <input type='SUBMIT' name='prob_orig_submit' value='Lancer prob_orig.exe'>

                </form>
            </div>
            <div class="tab-pane fade" id="parente">
              <form class="customForm" action="parente1.php" method = "POST">

                  <fieldset class="customFieldset">
                    <legend class="customLegend"><span class="legend"> Probabilité d'origine des gènes </legend>
                    Vous allez être redirigé vers une page de sélection des animaux de référence. Aucune modification manuelle de fichier n'est requise.
                    <b>Les noms des fichiers d'entrée de parente.exe sont prédéfinis : </b>

                    <BR><BR>

                    <p class="customP">
                      <label class="customLabel" for="entreeParente"><span class="customSpan">Le fichier d'entrée est : &nbsp;</span>
                        <input type='text' id='entreeParente' name='entreeParente' class='customInputs' value='ped_<?php echo $race; ?>.csv' readonly>
                      </label>
                    </p>

                    <p class="customP">
                      <label class="customLabel" for="refParente"><span class="customSpan">Le fichier de référence est : &nbsp;</span>
                        <input type='text' id='refParente' name='refParente' class='customInputs' value='ref_parente_<?php echo $race; ?>.txt' readonly>
                      </label>
                    </p>

                    <p class="customP">
                      <label class="customLabel" for="sortieParente"><span class="customSpan">Le fichier de sortie est : &nbsp;</span>
                        <input type='text' id='sortieParente' name='sortieParente' class='customInputs' value='parente_<?php echo $race; ?>.txt' readonly>
                      </label>
                    </p>

                    <input type="submit" name="submit_parente">

                  </fieldset>

                </form>
            </div>
          </div>
        </div>
      </div>
      <div class="widget-foot">
          <!-- Footer goes here -->
      </div>
    </div>
  </div>

</div>

<?php require BODY_END;?>

<!--Optional scripts start -->

<!-- Optional scripts end -->

</body>
</html>