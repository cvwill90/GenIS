<!DOCTYPE html>
<html lang="en">
<head>

  <title>GenIS</title>

  <?php 
  require_once '../libraries/constants.php';
  require_once HEAD_START;
  ?>

  <!--Optional sources start -->
  <script type="text/javascript" src="js/script_select.js"></script>
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
        <div class="pull-left">Paramètres d'entrée pour ped_util.exe</div>
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

          $fichier_req = $_GET['nameReq'];
          $fichier_ref = $_GET['nameRef'];
          $fichier_sortie = str_replace("req","ped",$fichier_req);
          $fichier_sortie = str_replace("txt","csv",$fichier_sortie);
          $race_lib = $_GET['race_lib'];
          $race = $_GET['race'];
          
          // on aura souvent besoin de $race donc on le garde dans la session

          $_SESSION['race'] = $race_lib;

          ?>


          Deux fichiers, le fichier d'entrée et le fichier de référence de ped_util.exe, contenant tous les animaux sélectionnés ont été créés.
          <br>
          Pour visualiser leur contenu et éventuellement l'éditer, copiez le lien suivant et collez-le dans la barre de recherche de l'explorateur Windows:
          <br> <br>
          Fichier d'entrée : "C:\wamp\www\Genis\SiteWeb\Calculs\Pedig\<?php echo $fichier_req;?>"
          <br>
          Fichier de référence : "C:\wamp\www\Genis\SiteWeb\Calculs\Pedig\<?php echo $fichier_ref;?>"
          <br> <br>
          Si vous souhaitez éditer le contenu, vous pouvez supprimer des lignes pour ne garder que les animaux souhaités. <b>Veillez cependant à ne pas changer les nom de fichiers!!</b>
          <br> <br>

          Avant de lancer Pedig, saisissez-en les paramètres d'entrée :
          <br>

          <form class="form-horizontal" id="launchPedig" method="GET" role="form">
            <fieldset>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="reqFile">Fichier d'entrée</label>
                <div class="col-lg-5">
                  <input class="form-control" type="text" name="reqFile" id="reqFile" value = "<?php echo $fichier_req?>" readonly>
                </div>
              </div>
              <hr>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="refFile">Fichier de référence</label>
                <div class="col-lg-5">
                  <input class="form-control" type="text" name="refFile" id="refFile" value = "<?php echo $fichier_ref?>" readonly>
                </div>
              </div>
              <hr>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="outputFile">Fichier de sortie</label>
                <div class="col-lg-5">
                  <input class="form-control" type="text" name="outputFile" id="outputFile" value = "<?php echo $fichier_sortie?>" readonly>
                </div>
              </div>
              <hr>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="maxGen">Nombre maximal de générations à prendre en compte</label>
                <div class="col-lg-5">
                  <input class="form-control" type="number" name="maxGen" id="maxGen" value = "100">
                </div>
              </div>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="param">Nombre de paramètres supplémentaires à lire en cas de besoin</label>
                <div class="col-lg-5">
                    <input class="form-control" type="number" name="param" id="param" value = "4" max="4">
                </div>
              </div>
              <hr>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="pedigree_yes">Eliminer les pedigrees inutiles?</label>
                <div class="col-lg-5">
                  <div class="radio">
                    <table style="width: 100%;">
                      <tbody>
                      <tr>
                        <td style="width: 33%;">
                          <label>
                            <input name="pedigree" id="pedigree_yes" value="y" type="radio" checked>
                            Oui
                          </label>
                        </td>
                        <td style="width: 33%;">
                          <label>
                            <input name="pedigree" id="pedigree_no" value="n" type="radio">
                            Non
                          </label>
                        </td>
                      </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <hr>
              <div class="col-lg-offset-2 col-lg-6">
                <input type="button" id="launchPedUtil" name="launchPedUtil" value="Lancer ped_util.exe" onclick="launchPedutil(<?php echo $race;?>)" class="btn btn-sm btn-success">
                <a href="pedUtil1.php"><button type="button" class="btn btn-sm btn-primary">Recommencer</button></a>
                <a href="../index.php"><button type="button" class="btn btn-sm btn-danger">Annuler</button></a>
              </div>
            </fieldset>
          </form>

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

<div style="display: none; padding-right: 24px;" id="myModal" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Exécution de ped_util.exe en cours...</h4>
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
<div id="fadeModal" class="modal-backdrop fade in" style="display: none;"></div>

</body>
</html>