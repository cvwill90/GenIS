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
$_SESSION['current_page']='prob_orig';

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
        <div class="pull-left">Résultats de calculs</div>
        <div class="widget-icons pull-right">
          <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
          <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="widget-content">
        <div class="padd">
          <!-- Content goes here -->
          <p>
            Calculs en cours...
          </p>
          <?php

          $_SESSION['prog'] = 'orig';
          $prog = $_SESSION['prog'];
          $race = $_SESSION['race'];


          $result_ped_util = $_POST["entreePO"]; //récupération du résultat de ped_util
          $f_sortie_contrib = $_POST["sortiePO1"]; // fichier de sortie du programme prob_orig
          $f_sortie_liste = $_POST["sortiePO2"];
          $nb_ancetre = $_POST["ancestors"];
          $sexe = $_POST["sex"];
          $prem_annee = $_POST["startRef"];
          $dern_annee = $_POST["endRef"];

          echo '<form id="hiddenForm" name="hiddenForm" method="POST" action="postCalculs.php" role="form">
            <input type = "hidden" value = "'. $race .'" id="race">
            <input type = "hidden" value = "'. $prog .'" id="prog">
            <input type = "hidden" value = "'. $result_ped_util .'" id="ped_util">
            <input type = "hidden" value = "'. $f_sortie_contrib .'" id="sortie_contrib">
            <input type = "hidden" value = "'. $f_sortie_liste .'" id="sortie_list">
            <input type = "hidden" value = "'. $nb_ancetre .'" id="nbAncetres">
            <input type = "hidden" value = "'. $sexe .'" id="sex">
            <input type = "hidden" value = "'. $prem_annee .'" id="year1">
            <input type = "hidden" value = "'. $dern_annee .'" id="year2">
            </form>';

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
<script>

  $(document).ready(function(){
    var prog = $('#prog').val(), race = $('#race').val(), ped_util = $('#ped_util').val(), sortie_contrib = $('#sortie_contrib').val(), sortie_liste = $('#sortie_list').val();
    var nb_anc = $('#nbAncetres').val(), sex = $('#sex').val(), year1 = $('#year1').val(), year2 = $('#year2').val();
    $.ajax({
      method: "GET",
      url: "../libraries/ajax/ajaxproborig.php?key1="+ race +"&key2="+ ped_util +"&key3="+ sortie_contrib +"&key4="+ nb_anc +"&key5="+ sex +"&key6="+ year1 +"&key7="+ year2 + "&key8=" + sortie_liste,
      dataType: 'json',
      success: function(){
        window.location.replace('postCalculs.php?race='+ race +'&prog='+ prog +'&ped_util='+ ped_util +'&sortie_contrib='+ sortie_contrib +'&sortie_list='+ sortie_liste);
      },
      error: function(){
        alert('Le programme prob_orig.exe ne s\'est pas exécuté correctement.\nVérifiez que les fichiers '+ sortie_contrib +' et ' + sortie_liste + 'ne sont pas ouverts dans Excel.');
      }
    });
  });


</script>
<!-- Optional scripts end -->

<div style="display: block; padding-right: 24px;" id="myModal" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Prob_orig.exe est en cours d'exécution...</h4>
      </div>
      <div class="modal-body">
        <div class="progress progress-striped active">
          <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
            <span class="sr-only"></span>
          </div>
        </div>
      </div>
      <div class="modal-footer">

      </div>
    </div>
  </div>
</div>
<div class="modal-backdrop fade in"></div>

</body>
</html>