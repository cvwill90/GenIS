<!DOCTYPE html>
<html lang="en">
<head>

  <title>GenIS</title>

<?php 
require_once '../libraries/constants.php';
require_once HEAD_START;
?>

  <!--Optional sources start -->
<script type="text/javascript" src="js/visu_anim.js"></script>
<script type="text/javascript" src="html2canvas.js"></script>
<script type="text/javascript" src="../libraries/js/jspdf.min.js"></script>
  <!-- Optional sources end -->

<meta http-equiv="Cache-Control" content="no-store"/>
</head>

<body>

<?php
session_start();
$_SESSION['current_page']='visu_animal';

require BODY_START;

/*
 * Starting connection to database
 */

$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

/*
 * Autoload classes
 */

autoload_classes();

?>

<div class="row">
  <div class="col-md-12">

    <div class="widget">
      <div class="widget-head">
        <div class="pull-left">Rechercher un animal</div>
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
          $div_form = new HtmlContainer('form');
          $div_form->attr('class', 'form-horizontal');
          $div_form->attr('role', 'form');
          
            $div_fieldset1 = new HtmlContainer('fieldset');

              // Select espece

              $div_esp = new HtmlContainer('div');
              $div_esp->attr('class', 'form-group');

                $id_espece = 'espece';

                $lab_espece = new HtmlContainer('label');
                $lab_espece->attr('class', 'col-lg-2 control-label');
                $lab_espece->attr('for', $id_espece);
                $lab_espece->content('Espèce');
                $div_esp->content($lab_espece->get_html());

                $div_sel_espece = new HtmlContainer('div');
                $div_sel_espece->attr('class', 'col-lg-3');

                    $sel_espece = new HtmlContainer('select');
                    $sel_espece->attr('id', $id_espece);
                    $sel_espece->attr('name', $id_espece);
                    $sel_espece->attr('class', 'form-control');
                    $sel_espece->attr('size', 1);
                    $sel_espece->attr('onchange', 'fillup_race()');

                    $especes = get_all_especes($con);

                        $list_options_especes = '<option value="1">Inconnue</option>';
                        foreach ($especes as $esp_k => $esp_v){
                            $opt_espece = new HtmlContainer('option');
                            $opt_espece->attr('value', $esp_k);
                            $opt_espece->content($esp_v);
                            $list_options_especes .= $opt_espece->get_html();
                        }

                    $sel_espece->content($list_options_especes);
                $div_sel_espece->content($sel_espece->get_html());
              $div_esp->content($div_sel_espece->get_html());

              // Select race

              $div_race = new HtmlContainer('div');
              $div_race->attr('class', 'form-group');

                $id_race = 'race';

                $lab_race = new HtmlContainer('label');
                $lab_race->attr('class', 'col-lg-2 control-label');
                $lab_race->attr('for', $id_race);
                $lab_race->content('Race');

                $div_sel_race = new HtmlContainer('div');
                $div_sel_race->attr('class', 'col-lg-3');

                    $sel_race = new HtmlContainer('select');
                    $sel_race->attr('id', $id_race);
                    $sel_race->attr('name', $id_race);
                    $sel_race->attr('class', 'form-control');
                    $sel_race->attr('size', 1);
                    $sel_race->attr('disabled', 'True');

                $div_sel_race->content($sel_race->get_html());

              $div_race->content($lab_race->get_html());
              $div_race->content($div_sel_race->get_html());

              // Choose animal
              $div_animal = new HtmlContainer('div');
              $div_animal->attr('class', 'form-group');

                $id_animal = 'choose_animal';

                $lab_animal = new HtmlContainer('label');
                $lab_animal->attr('class', 'col-lg-2 control-label');
                $lab_animal->attr('for', $id_animal);
                $lab_animal->content('Nom de l\'animal');

                $div_text_animal = new HtmlContainer('div');
                $div_text_animal->attr('class', 'col-lg-5');

                    $text_animal = new HtmlObject('input');
                    $text_animal->attr('type', 'text');
                    $text_animal->attr('id', $id_animal);
                    $text_animal->attr('name', $id_animal);
                    $text_animal->attr('class', 'form-control');
                    $text_animal->attr('placeholder', 'Sélectionner un animal de la liste');
                    $text_animal->attr('onkeyup', 'autocomplete_animal(event)');
                    
                $div_text_animal->content($text_animal->get_html());

              $div_animal->content($lab_animal->get_html());
              $div_animal->content($div_text_animal->get_html());          

            $div_fieldset1->content($div_esp->get_html());
            $div_fieldset1->content($div_race->get_html());
            $div_fieldset1->content($div_animal->get_html());
          
          $div_form->content($div_fieldset1->get_html());
          $div_form->print_html();
          
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
  <div class="col-md-12">

    <div class="widget">
      <div class="widget-head">
        <div class="pull-left">Fiche descriptive de l'animal</div>
        <div class="widget-icons pull-right">
            <!--<a href="javascript:undo_death()"><i class="fa fa-undo"></i></a>-->
            <a href="javascript:print_info()"><i class="fa fa-print"></i></a>
            <a href="javascript:export_csv()"><i class="fa fa-download"></i></a>
            <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
            <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="widget-content">
          <div id="print_div" class="padd">
            
            <?php
            
            $title_animal = new HtmlContainer('h3');
            $title_animal->attr('id', 'nom_animal');
            $title_animal->attr('style', 'font-weight: bold; color: black !important; text-align: center');
            $title_animal->print_html();
            
            $title_info = new HtmlContainer('h4');
            $title_info->content('Informations de l\'animal');
            $title_info->print_html();
            
            echo '<hr style="margin-top: 1px">';
            
            $div_row_info = new HtmlContainer('div');
            $div_row_info->attr('class', 'row');
            
                $div_col_info1 = new HtmlContainer('div');
                $div_col_info1->attr('class', 'col-md-4');
                
                    $table_info1 = new HtmlContainer('table');
                    $table_info1->attr('style', 'width: 100%;');
                    
                        $table1_entries = array(
                            'no_identification' => 'No d\'identification',
                            'date_naiss' => 'Date de naissance',
                            'coeff_consang' => 'Consanguinité',
                            'lignee' => 'Lignée',
                            'nom_pere' => 'Nom du père',
                            'no_identification_pere' => 'No du père',
                            'livre_gene' => 'Livre généalogique'
                        );
                        $table1_rows = '';
                        foreach ($table1_entries as $te_k => $te_v){
                            $table1_row = new HtmlContainer('tr');
                                $table1_cell1 = new HtmlContainer('td');
                                $table1_cell1->attr('style', 'width: 40%; padding-top: 15px !important; font-weight: bold; border-bottom: 1px solid #ccc');
                                $table1_cell1->content($te_v);
                                $table1_cell2 = new HtmlContainer('td');
                                $table1_cell2->attr('style', 'padding-top: 15px;  border-bottom: 1px solid #ccc');
                                $table1_cell2->attr('name', 'table_value');
                                $table1_cell2->attr('id', $te_k);
                            $table1_row->content($table1_cell1->get_html());
                            $table1_row->content($table1_cell2->get_html());
                            
                            $table1_rows .= $table1_row->get_html();
                        }
                    $table_info1->content($table1_rows);
                $div_col_info1->content($table_info1->get_html());
            
                $div_col_info2 = new HtmlContainer('div');
                $div_col_info2->attr('class', 'col-md-4');
                
                    $table_info2 = new HtmlContainer('table');
                    $table_info2->attr('style', 'width: 100%;');
                    
                        $table2_entries = array(
                            'lib_race' => 'Race',
                            'lieu_naiss' => 'Lieu de naissance',
                            'sexe' => 'Sexe',
                            'famille' => 'Famille',
                            'nom_mere' => 'Nom de la mère',
                            'no_identification_mere' => 'No de la mère'
                        );
                        $table2_rows = '';
                        foreach ($table2_entries as $te_k => $te_v){
                            $table2_row = new HtmlContainer('tr');
                                $table2_cell1 = new HtmlContainer('td');
                                $table2_cell1->attr('style', 'width: 40%; padding-top: 15px !important; font-weight: bold; border-bottom: 1px solid #ccc');
                                $table2_cell1->content($te_v);
                                $table2_cell2 = new HtmlContainer('td');
                                $table2_cell2->attr('style', 'padding-top: 15px;  border-bottom: 1px solid #ccc');
                                $table2_cell2->attr('name', 'table_value');
                                $table2_cell2->attr('id', $te_k);
                            $table2_row->content($table2_cell1->get_html());
                            $table2_row->content($table2_cell2->get_html());
                            
                            $table2_rows .= $table2_row->get_html();
                        }
                    $table_info2->content($table2_rows);
                $div_col_info2->content($table_info2->get_html());
                
                $div_col_photo = new HtmlContainer('div');
                $div_col_photo->attr('class', 'col-md-4');
                    
                    $photo = new HtmlContainer('div');
                    $photo->attr('style', 'width:65%; border:1px solid #000;padding: 100px 0px; margin:40px 0px 40px 70px; text-align: center');
                    $photo->content('Photo');
                
                $div_col_photo->content($photo->get_html());
            $div_row_info->content($div_col_info1->get_html());
            $div_row_info->content($div_col_info2->get_html());
            $div_row_info->content($div_col_photo->get_html());
            $div_row_info->print_html();
            
            $title_history = new HtmlContainer('h4');
            $title_history->content('Historique des mouvements de l\'animal');
            $title_history->print_html();
            
            echo '<hr style="margin-top: 1px">';
            
            $history_table = new HtmlContainer('table');
            $history_table->attr('id', 'hist_table');
            $history_table->attr('class', 'table table-striped table-bordered table-hover');
                
                $history_head = new HtmlContainer('thead');
                
                    $history_head_row = new HtmlContainer('tr');

                        $history_table_columns = array('Lieu','Date d\'entrée','Date de sortie','Remarque');
                        $history_table_heads = '';
                        foreach ($history_table_columns as $tc){
                            $hist_table_head = new HtmlContainer('th');
                            $hist_table_head->content($tc);
                            $history_table_heads .= $hist_table_head->get_html();
                        }
                    $history_head_row->content($history_table_heads);
                
                $history_head->content($history_head_row->get_html());
                    
                $history_table_body = new HtmlContainer('tbody');
                $history_table_body->attr('id', 'story_table_body');
                
            $history_table->content($history_head->get_html());
            $history_table->content($history_table_body->get_html());
            $history_table->print_html();
            
            $id_animal2 = 'id_animal';
            $hidden_id_animal = new HtmlObject('input');
            $hidden_id_animal->attr('type', 'hidden');
            $hidden_id_animal->attr('id', $id_animal2);
            $hidden_id_animal->print_html();
            
            $id_elevage = 'id_elevage';
            $hidden_id_elevage = new HtmlObject('input');
            $hidden_id_elevage->attr('type', 'hidden');
            $hidden_id_elevage->attr('id', $id_elevage);
            $hidden_id_elevage->print_html();
            
            ?>
            
        </div>
      </div>
      <div class="widget-foot">
          
      </div>
    </div>
  </div>
</div>

<?php require BODY_END;?>

<!--Optional scripts start -->

<!-- Optional scripts end -->

</body>
</html>