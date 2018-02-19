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
$_SESSION['current_page']='meuw';

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

          $_SESSION['prog'] = 'meuw';
          $prog = $_SESSION['prog'];
          $race = $_SESSION['race'];

          $result_ped_util = $_POST["entreeMeuw"]; //rÃ©cupÃ©ration du rÃ©sultat de ped_util
          $f_sortie_meuw = $_POST["sortieMeuw"]; // fichier de sortie du programme meuw

          echo '<form id="hiddenForm" name="hiddenForm" method="POST" action="postCalculs.php" role="form">
                <input type = "hidden" value = "'. $race .'" id="race" name="race">
                <input type = "hidden" value = "'. $prog .'" id="prog" name="prog">
                <input type = "hidden" value = "'. $result_ped_util .'" id="ped_util" name="ped_util">
                <input type = "hidden" value = "'. $f_sortie_meuw .'" id="sortie" name="sortie">
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
    var prog = $('#prog').val(), race = $('#race').val(), ped_util = $('#ped_util').val(), sortie = $('#sortie').val();
    $.ajax({
      method: "GET",
      url: "../libraries/ajax/ajaxmeuw.php?key1="+ race +"&key2="+ ped_util +"&key3="+ sortie,
      success: function(){
        window.location.replace('postCalculs.php?race='+ race +'&prog='+ prog +'&ped_util='+ ped_util +'&sortie='+ sortie);
      },
      error: function(){
        alert('Le programme meuw.exe ne s\'est pas exécuté correctement.\nVérifiez que le fichier '+ sortie +'n\'est pas ouvert dans Excel.');
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
        <h4 class="modal-title">Meuw.exe est en cours d'exécution...</h4>
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