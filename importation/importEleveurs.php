<!DOCTYPE html>
<html lang="en">
<head>

  <title>GenIS</title>

  <?php 
  require_once '../libraries/constants.php';
  require_once HEAD_START;
  ?>

  <!--Optional sources start -->
    <script type="text/javascript" src="js/script_import.js"></script>
  <!-- Optional sources end -->

</head>

<body>

<?php
session_start();
$_SESSION['current_page']='importEleveurs';

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
        <div class="pull-left">Importation éleveurs</div>
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
            if (isset($_POST['import_result'])){
                echo $_POST['import_result'];
            } else {
            ?>
            <form class="form-horizontal" id="import_eleveurs" method="POST" role="form" action="importEleveurs.php">
                <fieldset>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="espece">Espèce</label>
                        <div class="col-lg-5">
                            <?php
                            $sql_especes = "SELECT id_espece, lib_espece FROM espece ORDER BY lib_espece";

                            $query = pdo_sql_query($con,$sql_especes);

                            $array_code_espece = array();
                            $array_label_espece = array();
                            while ($result_especes = $query -> fetch()){
                              array_push($array_code_espece,$result_especes[0]);
                              array_push($array_label_espece,$result_especes[1]);
                            }

                            tableau_choix($array_code_espece,$array_label_espece,'espece',1,'onchange="fillup_race()" id="espece" class="form-control" required');
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="race">Race</label>
                        <div class="col-lg-5">
                            <select class="form-control" name="race" id="race" size="1" class="form-control" disabled required></select>
                        </div>
                    </div>
                    <div id="fileSelect" class="form-group" style="display: none;">
                        <label class="col-lg-2 control-label">Fichier</label>
                        <div class="col-lg-5">
                            <table style="width: 100%;">
                                <tr>
                                  <td width="10%"><input type="button" value="Sélectionner un fichier ..." class="btn btn-sm btn-primary" onclick='$("#csvFileInput").trigger("click");'></td>
                                  <td><div id="selectedFile" class="col-lg-12"><p></p></div></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <input type="file" id="csvFileInput" style="display: none;" onchange="handleFiles(this.files)" onclick="initFile()" accept=".csv">

                    <div><textarea style="display: none;" id="import_content" name="import_content"></textarea></div>   
                    <input type="hidden" name="import_result" id="import_result">
                    <div class="form-group">
                        <label class="col-lg-2 control-label"></label>
                        <div class="col-lg-5">
                            <button class="btn btn-sm btn-success" type="button" id="buttonImportEleveurs">Importer les éleveurs</button>
                        </div>
                    </div>
                </fieldset>
            </form>
            <?php } ?>

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
        $('#buttonImportEleveurs').click(importEleveurs);
    });

    if (window.File && window.FileReader && window.FileList && window.Blob) {
        // Great success! All the File APIs are supported.
    } else {
      alert('Le navigateur ne supporte pas totalement le module "API File".');
    }

    function initFile(){
      $('#csvFileInput').prop('value','');
      $('#import_content').val('');
    }

    function handleFiles(files) {
      // Check for the various File API support.
      if (window.FileReader) {
        // FileReader are supported.
        $('#selectedFile p').html(files[0].name);
        getAsText(files[0]);
        $('#checkAnimals').prop('disabled',false);
      } else {
        alert('FileReader ne fonctionne pas sur ce navigateur.');
      }
    }

    function getAsText(fileToRead) {
      var reader = new FileReader();
      // Read file into memory as UTF-8
      reader.readAsText(fileToRead, 'windows-1252');
      // Handle errors load
      reader.onload = loadHandler;
      reader.onerror = errorHandler;
    }

    function loadHandler(event) {
      var csv = event.target.result;
      processData(csv);
    }

    function processData(csv) {
      var allTextLines = csv.split(/\r\n|\n/);
      var lines = [];

      for (var i=0; i<allTextLines.length; i++) {
        if (i !== 0){
          lines.push(allTextLines[i]);
        }
      }
      
      var firstLine = lines[0];
      var data_string = '';
      var data = '';
      if (firstLine.split(';').length === 12){
          data_string = lines.join('');
          data = data_string.substr(0, data_string.length-1);
      } else {
          data = lines.join(';');
      }
      $('#import_content').val(data);

    }

    function errorHandler(evt) {
      if(evt.target.error.name === "NotReadableError") {
        alert("Canno't read file !");
      }
    }

    function importEleveurs(evt){
        //var status = 'importAnimals';
        var eleveurs = document.getElementById('import_content').value;
        var race = document.getElementById('race').value;
        $.ajax({
          method: "POST",
          dataType: "json",
          data: "&data=" + eleveurs + "&race=" + race,
          url: "../libraries/ajax/ajaxImportEleveurs.php",
          success: function (data) {
            if (data.statusMsg === 'ok'){
              $('#import_result').prop('value',JSON.stringify(data.msg));
              $('#import_eleveurs').submit();
            } else {
              alert(data.errorMsg);
            }
          },
          error: function () {
            alert('Erreur lors de l\'imoprtation');
          }
        });
    }

</script>
<!-- Optional scripts end -->

</body>
</html>
