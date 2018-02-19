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

$current_page = $_SESSION['current_page'];

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
          <?php
          if($current_page == 'newContact'){
            echo '<div class="pull-left">Nouveau contact</div>';
          }else{
            echo '<div class="pull-left">Modification de contact</div>';
          }          
          ?>
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

          if($current_page == 'newContact'){
          ?>
            <div class="alert alert-success">
                <b>Insertion réussie !</b> Le nouveau contact a bien été enregistré.
            </div>
          <?php
          }else{
          ?>
            <div class="alert alert-success">
                <b>Modification réussie !</b> Les renseignements du contact ont bien été modifiés.
            </div>
          <?php
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

<!-- Optional scripts end -->

</body>
</html>