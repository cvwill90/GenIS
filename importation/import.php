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
$_SESSION['current_page']='import';
?>
    
    
    <div style="display: none; padding-right: 24px;" id="myModal" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="margin-top: 250px">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title">Importation des individus en cours...</h4>
            </div>
            <div class="modal-body">
              <div class="progress progress-striped active">
                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                  <span class="sr-only"></span>
                </div>
              </div>
            </div>
            <div class="modal-footer">

            </div>
          </div>
        </div>
    </div>
    <!-- Loading Modal End -->

    <div id="fadeModal" class="modal-backdrop fade in" style="display: none"></div>
    
<?php

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
        <div class="pull-left">Importation animaux<!--Sélectionner un fichier pour importer de nouvelles naissances--></div>
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
          if (!isset($_POST['import'])) {
            ?>
            <ul id="myTab" class="nav nav-tabs">
              <li class="active" id="step1Tab"><a href="#importStep1" data-toggle="tab" style="pointer-events: none;"><b>Etape 1 : Fichier d'importation</b></a></li>
              <li class="" id="step2Tab"><a href="#importStep2" data-toggle="tab" style="pointer-events: none;">Etape 2: Vérification des individus</a></li>
            </ul>
            <div id="myTabContent" class="tab-content">
              <div class="tab-pane fade in active" id="importStep1">
                <p>
                  Avant d'importer un fichier assurez-vous de trier les animaux par ordre de naissance croissant.
                  <br><br>
                  1. Vérification préalable de l'existence des animaux dans la base de données.<br>
                  2. Confirmation.<br>
                  3. Envoi de la liste d'animaux.<br>
                </p>

                <form method="post" class="form-horizontal" role="form" id="importForm" name="importForm" action="import.php">
                  <div class="form-group">
                    <label class="col-lg-2 control-label">Race</label>
                    <div class="col-lg-5">
                      <?php
                      $index = json_decode(IMPORT_RACES);
                      $labels = json_decode(IMPORT_RACES_LABEL);
                      $name = 'selectRace';
                      tableau_choix($index,$labels,$name,1,'id="'. $name .'" onchange="toggleForm()"');
                      ?>
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
                  <div class="form-group">
                    <label class="col-lg-2 control-label"></label>
                    <div class="col-lg-5">
                      <button type="button" id="checkAnimals" class="btn btn-sm btn-success" disabled>Vérification des individus</button>
                    </div>
                  </div>
                  <input type="file" id="csvFileInput" class="hidden" onchange="handleFiles(this.files)" onclick="initFile()" accept=".csv">
                  <input type="hidden" id="import" name="import" value="check1">
                  <div><textarea style="display: none;" id="import_content" name="import_content"></textarea></div>
                </form>
              </div>
              <div class="tab-pane fade" id="importStep2" ></div>
              <div class="tab-pane fade" id="importStep3"></div>
            </div>
            <?php
          } elseif ($_POST['import']=="check1"){
            ?>
            <ul id="myTab" class="nav nav-tabs">
              <li class="" id="step1Tab"><a href="#importStep1" data-toggle="tab" style="pointer-events: none;">Etape 1</a></li>
              <li class="active" id="step2Tab"><a href="#importStep2" data-toggle="tab" style="pointer-events: none;"><b>Etape 2 : Vérification des individus</b></a></li>
            </ul>
            <div id="myTabContent" class="tab-content">
              <div class="tab-pane fade" id="importStep1"></div>
              <div class="tab-pane fade in active" id="importStep2">
                <?php

                if (isset($_POST['import_content'])){
                  $jsonAnimals = $_POST['import_content'];
                  $animals = json_decode($jsonAnimals);
                }

                if(count($animals)) {
                  if (isset($animals->existing)) {
                    ?>
                    <p>
                      Les animaux suivants se trouvent déjà dans la base de données.<br>
                      Ils ne seront pas réinsérés et seront donc exclus de l'importation.<br>
                      Vous pouvez vérifier votre fichier d'importation et le recharger ou poursuivre l'importation.
                    </p>
                    <!-- Table Page -->
                    <div class="page-tables">
                      <!-- Table -->
                      <div class="table-responsive">
                        <div class="dataTables_wrapper" id="data-table-1_wrapper">
                          <table style="width: 100%;" aria-describedby="data-table-1_info" role="grid" class="dataTable"
                                 id="data-table-1" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <thead>
                            <tr role="row">
                              <th style="width: 324px;" colspan="1" rowspan="1" aria-controls="data-table-1"
                                  tabindex="0"
                                  class="sorting">N° SIRE
                              </th>
                              <th style="width: 285px;" colspan="1" rowspan="1" aria-controls="data-table-1"
                                  tabindex="0"
                                  class="sorting">Nom
                              </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            foreach ($animals->existing as $ea) {
                              echo '<tr class="odd" role="row"><td>' . $ea[0] . '</td><td>' . $ea[1] . '</td></tr>';
                            }


                            ?>
                            </tbody>
                            <tfoot>

                            </tfoot>
                          </table>
                        </div>
                        <div class="clearfix"></div>
                      </div>
                    </div>
                    <?php
                  } else {
                    echo '<br><p>' .
                        'La base de données ne contient aucun des animaux du fichier sélectionné.<br>' .
                        'Vous pouvez poursuivre l\'importation des animaux en cliquant sur "Poursuivre l\'importation"' .
                        '</p>';
                  }
                  if (isset($animals->missing)) {
                    ?>
                      <br><br>
                    <form id="importForm2" role="form" class="form-horizontal" action="import.php" method="post">
                      <input type="hidden" name="import" id="import" value="check2">
                      <input type="hidden" name="import_content" id="import_content"
                             value="<?php echo str_replace('"', '&quot;', json_encode($animals->missing)); ?>">
                      <input type="hidden" name="race" id="race" value="<?php echo $_POST['selectRace']; ?>">
                      <input type="hidden" name="import_result" id="import_result" value="">
                      <div class="form-group">
                        <label class="col-lg-2 control-label"></label>
                        <div class="col-lg-5">
                          <a href="import.php">
                            <button type="button" class="btn btn-sm btn-info">Recharger le fichier</button>
                          </a>
                          <button type="button" id="importAnimals" class="btn btn-sm btn-success">Poursuivre l'importation</button>
                        </div>
                      </div>
                    </form>

                    <?php
                  } else {
                    echo '<p>La base de données contient déjà tous les animaux renseignés dans le fichier sélectionné</p>';
                  }
                } else {
                  echo '<p>Tous les animaux sont déjà présents dans la base données.</p>';
                }
              ?>
              </div>
              <div class="tab-pane fade in active" id="importStep3"></div>
            </div>
            <?php
          } else {
            if (isset($_POST['import_result'])) {
              $msg = $_POST['import_result'];
              echo '<div class="alert alert-success">' .
                  '<b>Importation réussie !</b> ' . $msg .
                  '</div>';
            } else {
              echo '<div class="alert alert-danger">' .
                  '<b>Importation échouée...</b> L\'importation a rencontré un problème.' .
                  '</div>';
            }
          }
          ?>

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
<script>

  $(document).ready(function(){
    $('#checkAnimals').click(checkAnimals);
  });

  $(document).ready(function(){
    $('#importAnimals').click(importAnimals);
  });

  if (window.File && window.FileReader && window.FileList && window.Blob) {
    // Great success! All the File APIs are supported.
  } else {
    alert('Le navigateur ne supporte pas totalement l\'API File.');
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
      if (i!=0){
        lines.push(allTextLines[i]);
      }
    }
    var firstLine = lines[0];
    var data_string = '';
    var data = '';
    if (firstLine.substr(firstLine.length-1, firstLine.length) === ';'){
        data_string = lines.join('');
        data = data_string.substr(0, data_string.length-1)
    } else {
        data = lines.join(';');
    }
    $('#import_content').val(data);
    console.log(data);
  }

  function errorHandler(evt) {
    if(evt.target.error.name == "NotReadableError") {
      alert("Canno't read file !");
    }
  }

  function checkAnimals(evt){
    var status = 'testAnimals';
    var animals = document.getElementById('import_content').value;
    var race = document.getElementById('selectRace').value;
    $.ajax({
      method: "POST",
      dataType: "json",
      data: "key1=" + status + "&key2=" + animals + "&key3=" + race,
      url: "../libraries/ajax/ajaximport.php",
      success: function(data){
        if (data.statusMsg == 'ok'){
          $('#import_content').prop('value',JSON.stringify(data.response.msg));
          $('#importForm').submit();
        } else {
          alert(data.response.errorMsg);
        }
      },
      error: function(){
        alert('Global import error (Step 1)');
      }
    });
  }
  
  function importAnimals(evt){
    var status = 'importAnimals';
    var animals = document.getElementById('import_content').value;
    var race = document.getElementById('race').value;
    document.getElementById('myModal').style.display='block';
    document.getElementById('fadeModal').style.display='block';
    $.ajax({
      method: "POST",
      dataType: "json",
      data: "key1=" + status + "&key2=" + animals + "&key3=" + race,
      url: "../libraries/ajax/ajaximport.php",
      complete: function () {
          document.getElementById('myModal').style.display='none';
          document.getElementById('fadeModal').style.display='none';
          
      },
      success: function (data) {
        if (data.statusMsg == 'ok'){
          $('#import_result').prop('value',JSON.stringify(data.response.msg));
          $('#importForm2').submit();
        } else {
          alert(data.response.errorMsg);
        }
      },
      error: function () {
        alert('Global import error (Step 2)');
      }
    });
  }

  function toggleForm(){
    var selected = $('#selectRace').val();
    if (selected == 0){
      $('#fileSelect').css('display','none');
    } else {
      $('#fileSelect').css('display','block');
    }
  }

</script>
<!-- Optional scripts end -->

</body>
</html>