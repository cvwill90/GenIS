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

$race = $_SESSION['race'];

?>

<!-- Loading Modal Start -->
<div style="display: block; padding-right: 24px;" id="myModal" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="margin-top: 250px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Chargement des individus de ped_<?php echo $race .".csv"; ?> ...</h4>
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

<div id="fadeModal" class="modal-backdrop fade in"></div>

<?php

require BODY_START;

/*
 * Starting connection to database
 */

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

$entreeParente = $_POST["entreeParente"];
$refParente = $_POST["refParente"];
$sortieParente = $_POST["sortieParente"];

$_SESSION['prog'] = 'parente';
$_SESSION['entreeParente'] = $entreeParente;
$_SESSION['refParente'] = $refParente;
$_SESSION['sortieParente'] = $sortieParente;
$prog = $_SESSION['prog'];

$pedFile = fopen("C:\\wamp64\\www\\genis.cra\\calculs\\pedigFiles\\ped_". $race .".csv","r");

$tab = array(); //Création et initialisation d'un tableau qui contiendra l'id et le no_identification

$k = 0;

while (($data = fgets($pedFile, 115)) !== false) { //On récupère chaque ligne du fichier une par une, et elle est ensuite traitée comme une chaine de caractères
  $data =str_replace(" ",";",$data); //Remplacement de tous les caractères " " (espaces) par des ";"

  for ($i=12; $i>1; $i--) { //Cette boucle sert à créer des chaines de ";;;;;" de différentes longuers, de chercher chacune d'elle dans la ligne en cours de lecture, et de la remplacer par un point-virgule unique
    $str = ";"; //On commence à $i=12 car le nombre d'espaces consécutifs peut aller jusqu'à 10 dans ped_...csv
    $j=0; // Je mets 12 pour etre sûr
    while ($j<$i) {
      $str = $str.";";
      $j++; // Explication supplémentaire: Comme un "\t" est fait de plusieurs espaces, on obtient des séries
    } // séries de ";" inutiles => donc on réduit leur nombre
    $data = str_replace($str,";",$data);
  }
  $data = substr($data,1,100); // Il faut enleveer le ";" au début de la chaîne
  $array = explode(";",$data); // on segmente la chaine de caractères
  if ($array[0] != ''){
    $tab[$k][0] = $array[0]; // on met l'id attribué par pedig et le numéro d'identification
    $tab[$k][1] = $array[7]; // dans un même tableau
    $k++;
  }
}

$k=0;
$i=0;
$j=0; // réinitialisation des compteurs

?>

<div class="row">
  <div class="col-md-12">

    <div class="widget">
      <div class="widget-head">
        <div class="pull-left">Page de sélection des individus de référence</div>
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
          echo "Veuillez choisir parmi les animaux de <b>ped_". $race .".csv.</b>";
          ?>

        </div>
        <div class="widget-foot">
          <!-- Footer goes here -->
        </div>
      </div>
    </div>

  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="widget">
      <div class="widget-head">
        <div class="pull-left">Sélection des individus de référence</div>
        <div class="widget-icons pull-right">
          <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
          <a href="#" class="wclose"><i class="fa fa-times"></i></a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="widget-content medias">
        <div class="padd">

          <!-- Table Page -->
          <div class="page-tables">
            <!-- Table -->
              <div class="table-responsive">
                <div class="dataTables_wrapper" id="data-table-1_wrapper">
                  <table style="width: 100%;" aria-describedby="data-table-1_info" role="grid" class="dataTable" id="data-table-1" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <thead>
                      <tr role="row">
                        <th aria-sort="ascending" style="width: 201px;" colspan="1" rowspan="1" aria-controls="data-table-1" tabindex="0" class="sorting_asc">N° Pedig</th>
                        <th style="width: 324px;" colspan="1" rowspan="1" aria-controls="data-table-1" tabindex="0" class="sorting">N° SIRE</th>
                        <th style="width: 285px;" colspan="1" rowspan="1" aria-controls="data-table-1" tabindex="0" class="sorting">Nom</th>
                        <th style="width: 285px;" colspan="1" rowspan="1" aria-controls="data-table-1" tabindex="0" class="sorting">Ajouter</th>
                      </tr>
                    </thead>
                    <tbody>

                    <?php
                    //Début de transaction pour les requêtes

                    try {
                      $con -> beginTransaction();

                      $sqlRace = "SELECT code_race FROM race WHERE abbrev = '". $race ."'";

                      foreach ($tab as $t) {
                          //echo $t[0] .' '. $t[1] .'<br>'; // juste pour avoir un apercu
                          echo '<tr class="odd" role="row">
                          <td>
                            ' . $t[0] . '
                          </td>
                          <td>
                            ' . $t[1] . '
                          </td>';

                          // Association du nom de l'animal

                          $sql = 'SELECT nom_animal FROM animal
                          WHERE code_race = (' . $sqlRace . ') AND no_identification = ' . $t[1];

                          $query = $con->query($sql);

                          $result = $query->fetch();

                          $nom_animal = $result['nom_animal'];

                          echo '<td>
                            ' . $nom_animal . '
                          </td>
                          <td>
                            <button id="add_animal'. $t[0] .'" class="btn btn-sm btn-success" value="' . $t[0] . ';' . $t[1] . ';' . $nom_animal . '" onclick="return addAnimal(this);">
                              <i class="fa fa-plus"></i>
                            </button>
                          </td>
                          </tr>';
                      }

                      $transactioncommit= $con -> commit();

                    } catch (Exception $e) {
                      $con -> rollback();
                    }

                    fclose($pedFile); // Fermeture du fichier ped_...csv

                    ?>
                    </tbody>
                    <tfoot>

                    </tfoot>
                  </table>
                </div>
              <div class="clearfix"></div>
            </div>
          </div>
        </div>


      </div>
      <div class="widget-foot">
        <!-- Footer goes here -->
      </div>
    </div>
    </div>
  <div class="col-md-6">
    <div class="widget">
      <div class="widget-head">
        <div class="pull-left">Animaux sélectionnés</div>
        <div class="widget-icons pull-right">
          <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
          <a href="#" class="wclose"><i class="fa fa-times"></i></a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="widget-content medias">

        <div class="page-tables">
          <form class="" method="POST" action="parente2.php">
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover" id="selectedAnimals">
                <thead>
                <tr>
                  <th>Numéro Pedig</th>
                  <th>Numéro SIRE</th>
                  <th>Nom</th>
                  <th>Retirer</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
              </table>
              <div id="tempDiv" style="display: block;" class="col-md-12" ><p style="text-align: center"><br><i>Veuillez sélectionner des animaux dans la liste ci-contre</i></p><br></div>
            </div>
            <!--<div>
              <label class="customLabel" for="one"><span class="customSpan">Valeur de la 2e colonne &nbsp;</span></label>
              <label class="radioLabel" for="one"> 1
                <input 	type="radio"
                          name="secondColumn"
                          id="one"
                          value = "1"
                          checked/>
              </label>
              <label class="radioLabel" for="two"> 2
                <input 	type="radio"
                          name="secondColumn"
                          id="two"
                          value = "2"/>
              </label>
            </div>-->
            <div>
              <input type="submit" name="launchParente" id="launchParente">
            </div>
          </form>
        </div>

        <div class="widget-foot">

          <div class="clearfix"></div>

        </div>

      </div>
    </div>
  </div>
</div>



<?php require BODY_END;?>

<!--Optional scripts start -->

<script type="text/javascript">
  function addAnimal(evt){
    var splitString = evt.value.split(';');
    var pedig=splitString[0], sire=splitString[1], nom=splitString[2];
    var button = '<button class="btn btn-sm btn-danger" value="'+ evt.value +'" onclick="return deleteAnimal(this);"><i class="fa fa-minus"></i></button>';
    var hiddenText = '<input type="hidden" value="'+ evt.value +'" name="selectedAnimals[]">';
    var newRow = '<tr id="'+ pedig +'"><td>'+ pedig +'</td><td>'+ sire +'</td><td>'+ nom +'</td><td>'+ button + hiddenText +'</td></tr>';
    $(newRow).appendTo('#selectedAnimals');
    
    document.getElementById(evt.id).disabled = true;
    document.getElementById('tempDiv').style.display = 'none';
    
    $('#search_box').focus();
    $('#search_box').val('');
    
    return false;
  }

  function deleteAnimal(evt){
    var thisId = evt.value.split(';')[0];
    var row = document.getElementById(thisId);
    row.parentNode.removeChild(row);
    if (!document.getElementById('selectedAnimals').getElementsByTagName("tbody")[0].getElementsByTagName("tr").length){
      document.getElementById('tempDiv').style.display ='block';
    }
    document.getElementById('add_animal' + thisId).disabled = false;
    
    return false;
  }
</script>

<script type="text/javascript">
  $(document).ready(function(){
    document.getElementById('myModal').style.display='none';
    document.getElementById('fadeModal').style.display='none';
    
    inputs = $('input');
    
    for (var i = 0; i < inputs.length; i++) {
        if(inputs[i].type.toLowerCase() == 'search') {
            searchbox = inputs[i];
            searchbox.setAttribute('id', 'search_box');
            searchbox.focus();
        }
    }
    
  });
</script>

<!-- Optional scripts end -->

</body>
</html>