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

          //header("Location: http://localhost/Genis/SiteWeb/Calculs/Pedig/postCalculs.php");

          $entreeParente = $_SESSION['entreeParente'];
          $refParente = $_SESSION['refParente'];
          $sortieParente = $_SESSION['sortieParente'];
          $race = $_SESSION['race'];
          $prog = $_SESSION['prog'];

          /*
           * ECRITURE DU FICHIER REF_PARENTE.TXT
           */
          //if (isset($_POST['secondColumn']) && $_POST['secondColumn'] != '') {
          //  $col = $_POST["secondColumn"];
          //}
          $col = 1;

          $i = 0;
          $tab = array();
          $correspond = array();
          if (isset($_POST['selectedAnimals']) && $_POST['selectedAnimals'] != '') {
            foreach ($_POST['selectedAnimals'] as $key) {
              $keys = explode(";",$key);
              $tab[$i][0] = $keys[0];
              
              $pedig_id = strval($keys[0]);
              $correspond[$pedig_id] = array();
              $correspond[$pedig_id][0] = $keys[1];
              $correspond[$pedig_id][1] = $keys[2];
              $i++;
            }
          }

          $_SESSION['cor'] = $correspond;

          $refPar = fopen("C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\ref_parente_". $race .".txt","w+");

          foreach ($tab as $row) {
            fputs($refPar, $row[0] ."\t". $col ."\r\n");
          }

          fclose($refPar);

          echo '<form id="hiddenForm" name="hiddenForm" method="Post" action="postCalculs.php" role="form">
                  <input type = "hidden" value = "'. $race .'" id="race">
                  <input type = "hidden" value = "'. $prog .'" id="prog">
                  <input type = "hidden" value = "'. $entreeParente .'" id="entreeParente">
                  <input type = "hidden" value = "'. $refParente .'" id="refParente">
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
    var prog = $('#prog').val(), race = $('#race').val(), ref = $('#refParente').val(), entree = $('#entreeParente').val();
    $.ajax({
      method: "GET",
      url: "../libraries/ajax/ajaxparente.php?key1="+ race +"&key2="+ ref +"&key3="+ entree,
      dataType: "json",
      success: function(data){
        if(data.statusMsg=='wrong'){
          window.location.replace('postCalculs.php?race='+ race +'&errorMsg=' + data.errorMessage);
        } else {
          window.location.replace('postCalculs.php?race='+ race);
        }
      },
      error: function(data){
          errorMsg = 'Erreur lors de l\'exploitation des résultats de parente.exe.';
          window.location.replace('postCalculs.php?race='+ race +'&errorMsg=' + errorMessage);
      }
    });
  })
  
</script>
<!-- Optional scripts end -->

<div style="display: block; padding-right: 24px;" id="myModal" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Parente.exe est en cours d'exécution...</h4>
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