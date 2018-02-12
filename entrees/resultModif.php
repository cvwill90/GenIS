<!DOCTYPE html>
<html lang="en">
<head>

  <title>GenIS</title>

  <?php require '../libraries/html_head1.php';?>

  <!--Optional sources start -->

  <!-- Optional sources end -->

</head>

<body>

<?php
session_start();
$_SESSION['current_page']='modifAnim';

require '../libraries/html_bodystart1.php';

/*
 * Starting connection to database
 */

include '../libraries/fonctions.php';

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

?>

<div class="row">
  <div class="col-md-12">

    <div class="widget">
      <div class="widget-head">
        <div class="pull-left">Modification d'un animal</div>
        <div class="widget-icons pull-right">
          <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
          <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="widget-content">
        <div class="padd">
          <!-- Content goes here -->

            <div class="alert alert-success">
                <b>Modification réussie !</b> Les modifications de l'animal ont bien été enregistrées dans la base de données.
            </div>

        </div>
        <div class="widget-foot">
          <!-- Footer goes here -->
        </div>
      </div>
    </div>

  </div>
</div>

<?php require '../libraries/html_bodyend1.php';?>

<!--Optional scripts start -->

<!-- Optional scripts end -->

</body>
</html>