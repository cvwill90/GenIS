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
$_SESSION['current_page']='del_animal';

require BODY_START;

/*
 * Starting connection to database
 */

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

?>

<div class="row">
  <div class="col-md-6">

    <div class="widget">
      <div class="widget-head">
        <div class="pull-left">Supprimer un animal</div>
        <div class="widget-icons pull-right">
          <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
          <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="widget-content">
        <div class="padd">
          <!-- Content goes here -->

          <form class="form-horizontal" role="form" _lpchecked="1">
              <div class="form-group">
                  <label class="col-lg-4 control-label">Nom ou numéro d'identification de l'animal</label>
                  <div class="col-lg-5">
                      <input type="text" id="animal" class="form-control">
                      <label id='selected_animal' style="padding-top: 0.6em;font-size: 1.4em; font-style: italic; padding-left: 0.5em;"></label>
                  </div>
              </div>
              <div class="form-group">
                <div class="col-lg-offset-4 col-lg-5">
                    <button type="button" class="btn btn-sm btn-danger" id="del_animal">Supprimer</button>
                </div>
              </div>
          </form>

        </div>
        <div class="widget-foot">
          <!-- Footer goes here -->
        </div>
      </div>
    </div>

  </div>
    
  <!--<div class="col-md-6">

    <div class="widget">
      <div class="widget-head">
        <div class="pull-left">Supprimer tous les animaux d'une race</div>
        <div class="widget-icons pull-right">
          <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
          <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="widget-content">
        <div class="padd">

          

        </div>
        <div class="widget-foot">
          
        </div>
      </div>
    </div>

  </div>-->
</div>

<?php require BODY_END;?>

<!--Optional scripts start -->
<script type="text/javascript" src="js/del_animal.js"></script>
<!-- Optional scripts end -->

</body>
</html>