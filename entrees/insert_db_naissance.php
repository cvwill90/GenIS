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

$_SESSION['current_page']='naiss';

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
        <div class="pull-left">Naissance</div>
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

          $race = $_GET['race'];
          $nom = $_GET['animalName'];
          $sexe = $_GET['animalSex'];
          $cra = $_GET['conserv'];
          $id = $_GET['animalID'];
          $date = $_GET['birthDate'];
          $farmID = $_GET['farmId'];
          $famille = $_GET['famille'];
          $lignee = $_GET['lignee'];
          $livre_gene = $_GET['livre_gene'];

          if ($_GET['fatherId']!='') {
            $pere = $_GET['fatherId'];
          }else {
            $pere = 1;
          }
          if ($_GET['motherId']!='') {
            $mere = $_GET['motherId'];
          }else{
            $mere = 2;
          }
          //Début de transaction pour les requêtes
          try {
            $con -> beginTransaction();

            //Les deux premièrers requetes:
            // 1. Il faut récupérer le numéro de l'élevage de l'utilisateur connecté
            //	$sql0 = "SELECT ligne_elev.id_elevage FROM ligne_elev INNER JOIN eleveur ON ligne_elev.id_eleveur = eleveur.id_eleveur WHERE eleveur.id_joomla = '487'";

            // 2.Insertion de l'animal né dans la table animal
            $sql1 = "INSERT INTO animal (id_animal, nom_animal, sexe, no_identification, date_naiss, id_livre, reproducteur, fecondation, coeff_consang, conservatoire, valide_animal, code_race, id_pere, id_mere)
                                        VALUES (NULL,'{$nom}',{$sexe},'{$id}','{$date}',{$livre_gene},0,0,0,{$cra},0,{$race},{$pere},{$mere})";

            // On exécute les requetes les unes après les autres

            $query1 = $con -> query($sql1);
            $animal_id= $con -> lastInsertId(); 	//On récupère l'id de la dernière ligne enregistrée dans la BD

            //Les deux dernières requêtes:
            //3. On annonce la naissance :
            $sql2 = "INSERT INTO periode(date_entree, date_sortie, valide_periode, id_animal, id_elevage, id_type)
					VALUES('$date',NULL,0,$animal_id,$farmID,3)";

            //4. On déclare le séjour dans l'élevage :
            $sql3 = "INSERT INTO periode(date_entree, date_sortie, valide_periode, id_animal, id_elevage, id_type)
					VALUES('$date',NULL,0,$animal_id,$farmID,2)";

            //5. On déclare l'élevage naisseur, aussi élevage propriétaire
            $sql4 = "INSERT INTO periode(date_entree, date_sortie, valide_periode, id_animal, id_elevage, id_type)
					VALUES('$date',NULL,0,$animal_id,$farmID,4)";
            
            // Et on exécute les requêtes
            $query2 = $con -> query($sql2);
            $query3 = $con -> query($sql3);
            $query4 = $con -> query($sql4);

            //Pas d'erreur jusque là donc on peut faire le commit()
            $transactioncommit= $con -> commit();

          } catch (Exception $e) {
            $con -> rollback();
          }

          if (isset($e)){
            $err = $con -> errorCode();

            echo '
            <div class="alert alert-warning">
                <b>Un problème est survenu !</b> L\'erreur N°'. $err .' a été retournée : <br><br>';
                echo '<i>'. $e .'</i>';
            echo '
            </div>';

          } else {
            echo '
            <div class="alert alert-success">
                <b>Insertion réussie !</b> La naissance a bien été enregistrée.
            </div>';
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