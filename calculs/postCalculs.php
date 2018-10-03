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

          <?php
          $code = $_SESSION['prog'];
          $race = $_SESSION['race'];

          
          if ($code == 'meuw'){
            echo 'Les résultats de meuw.exe se trouvent dans le fichier suivant : <b>C:/wamp64/www/genis.cra/calculs/pedigFiles/meuw_'. $race .'.csv</b>';
          } elseif ($code == 'vanrad') {
            echo 'Les résultats de vanrad.exe se trouvent dans le fichier suivant : <b>C:/wamp64/www/genis.cra/calculs/pedigFiles/vanrad_'. $race .'.csv</b>';
          } elseif ($code == 'orig') {
            echo 'Les fichiers de sortie de prob_orig.exe se trouvent dans le dossier <b>'. PEDIG_DUMP_FOLDER .'\\prob_orig' .'</b><br><br>';
          } else {
            if (isset($_GET['errorMsg'])) {
              echo '<div class="alert alert-danger">';
              echo $_GET['errorMsg'] . '<br>';
              echo 'Vous pouvez consulter les erreurs dans le dossier suivant :<br><br><b>C:/wamp64/www/genis.cra/calculs/error_log/error_log.txt</b> ';
              echo '</div>';
            } else {
              echo '<div class="alert alert-success">';
              echo 'Les résultats de parente.exe se trouvent dans le dossier <b>'. PEDIG_DUMP_FOLDER .'\\parente' .'</b>';
              echo '</div>';
            }
          }

          if ($code == "vanrad" || $code == "meuw") {
            echo '<br><br>
                Si vous le souhaitez, vous pouvez importer les coefficients de consanguinité calculés par '. $code .'.exe dans la base de données, en cliquant sur ce bouton : <br><br>';
            echo "	<input 	type='hidden'
					id='code'
					value='". $code ."'>
			<input	type='hidden'
					id='race'
					value='". $race ."'>
			<input 	type='button'
					name='importCoeff'
					id='importCoeff'
					value='Importer les coefficients'>";
          }
          
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

  $(function () {
    $('#importCoeff').on('click', function () {
      var code = $('#code').val(), race = $('#race').val();
      $.ajax({
        url: 'http://localhost/Genis/SiteWeb/Calculs/Pedig/importCoeff.php',
        data: {key1:code,key2:race},
        success : function() {
          $('#importCoeff').prop('disabled',true);
          alert('Les coefficients des animaux ont bien été ajoutés à la base de données.');
        }
      });
    });
  });

</script>
<!-- Optional scripts end -->

</body>
</html>