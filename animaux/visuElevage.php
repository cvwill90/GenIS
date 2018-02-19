<!DOCTYPE html>
<html lang="en">
<head>

  <title>GenIS</title>

  <?php
  require_once '../libraries/constants.php';
  require_once HEAD_START;
  ?>

  <!--Optional sources start -->
  <script type="text/javascript" src="js/visu_elevage.js"></script>
  <!-- Optional sources end -->

</head>

<body>

<?php
session_start();
$_SESSION['current_page']='visu_elevage';

require BODY_START;

/*
 * Starting connection to database
 */
$con = pdo_connection(HOST_DB,DB_NAME,USER_DB,PW_DB);

/*
 * Autoload classes
 */

autoload_classes();

if (isset($_GET['id_elevage']) && isset($_GET['nom_elevage'])) {
    $caught_farm_id = $_GET['id_elevage'];
    $caught_farm_name = $_GET['nom_elevage'];
    $called_by_get_request = TRUE;
} else {
    $called_by_get_request = FALSE;
}

?>

<div class="row">
  <div class="col-md-12">

    <div class="widget">
      <div class="widget-head">
        <div class="pull-left">Choisir l'élevage</div>
        <div class="widget-icons pull-right">
          <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
          <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="widget-content">
        <div class="padd">
          <?php
          $div_form = new HtmlContainer('form');
          $div_form->attr('class', 'form-horizontal');
          $div_form->attr('role', 'form');
          $div_form->attr('id', 'param_form');
          
            $div_fieldset1 = new HtmlContainer('fieldset');

              // Select farm

              $div_esp = new HtmlContainer('div');
              $div_esp->attr('class', 'form-group');

                $id_farm = 'farm';

                $lab_farm = new HtmlContainer('label');
                $lab_farm->attr('class', 'col-lg-2 control-label');
                $lab_farm->attr('for', $id_farm);
                $lab_farm->content('Elevage');
                $div_esp->content($lab_farm->get_html());

                $div_sel_farm = new HtmlContainer('div');
                $div_sel_farm->attr('class', 'col-lg-3');

                    $sel_farm = new HtmlContainer('input');
                    $sel_farm->attr('id', $id_farm);
                    $sel_farm->attr('name', $id_farm);
                    $sel_farm->attr('class', 'form-control');
                    $sel_farm->attr('required', 'true');
                    $sel_farm->attr('onkeyup', 'autocomplete_farm()');
                    if ($called_by_get_request) {
                        $sel_farm->attr('value', $caught_farm_name);
                    }

                $div_sel_farm->content($sel_farm->get_html());
              $div_esp->content($div_sel_farm->get_html());

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
                    $sel_race->attr('required', 'true');

                $div_sel_race->content($sel_race->get_html());

              $div_race->content($lab_race->get_html());
              $div_race->content($div_sel_race->get_html());

              // Choose sex of animals to be displayed
              $div_sex = new HtmlContainer('div');
              $div_sex->attr('class', 'form-group');

                $id_sex = 'sex';

                $lab_sex = new HtmlContainer('label');
                $lab_sex->attr('class', 'col-lg-2 control-label');
                $lab_sex->attr('for', $id_sex);
                $lab_sex->content('Sexe');

                $div_text_sex = new HtmlContainer('div');
                $div_text_sex->attr('class', 'col-lg-5');
                
                    $div_radio_sex_male = new HtmlContainer('div');
                    $div_radio_sex_male->attr('class', 'radio');
                        
                        $lab_radio_sex_male = new HtmlContainer('label');
                        
                            $text_sex_male = new HtmlObject('input');
                            $text_sex_male->attr('type', 'radio');
                            $text_sex_male->attr('id', 'male');
                            $text_sex_male->attr('name', $id_sex);
                            $text_sex_male->attr('value', 1);
                            
                        $lab_radio_sex_male->content($text_sex_male->get_html() . 'Mâle');
                    $div_radio_sex_male->content($lab_radio_sex_male->get_html());
                    
                    $div_radio_sex_female = new HtmlContainer('div');
                    $div_radio_sex_female->attr('class', 'radio');
                        
                        $lab_radio_sex_female = new HtmlContainer('label');
                        
                            $text_sex_female = new HtmlObject('input');
                            $text_sex_female->attr('type', 'radio');
                            $text_sex_female->attr('id', 'female');
                            $text_sex_female->attr('name', $id_sex);
                            $text_sex_female->attr('value', 2);
                            
                        $lab_radio_sex_female->content($text_sex_female->get_html() . 'Femelle');
                    $div_radio_sex_female->content($lab_radio_sex_female->get_html());
                    
                    $div_radio_sex_all = new HtmlContainer('div');
                    $div_radio_sex_all->attr('class', 'radio');
                    
                        $lab_radio_sex_all = new HtmlContainer('label');
                        
                            $text_sex_all = new HtmlObject('input');
                            $text_sex_all->attr('type', 'radio');
                            $text_sex_all->attr('id', 'all');
                            $text_sex_all->attr('name', $id_sex);
                            $text_sex_all->attr('value', 0);
                            $text_sex_all->attr('checked', 'true');
                            
                        $lab_radio_sex_all->content($text_sex_all->get_html() . 'Tous');
                    $div_radio_sex_all->content($lab_radio_sex_all->get_html());
                    
                $div_text_sex->content($div_radio_sex_male->get_html());
                $div_text_sex->content($div_radio_sex_female->get_html());
                $div_text_sex->content($div_radio_sex_all->get_html());

              $div_sex->content($lab_sex->get_html());
              $div_sex->content($div_text_sex->get_html());
              
              $div_repro = new HtmlContainer('div');
              $div_repro->attr('class', 'form-group');

                $id_repro = 'repro';

                $lab_repro = new HtmlContainer('label');
                $lab_repro->attr('class', 'col-lg-2 control-label');
                $lab_repro->attr('for', $id_repro);
                $lab_repro->content('Reproducteurs uniquement');

                $div_check_repro = new HtmlContainer('div');
                $div_check_repro->attr('class', 'col-lg-3');
                
                    $lab_check_repro = new HtmlContainer('label');
                    $lab_check_repro->attr('class', 'checkbox-inline');
                
                        $check_repro = new HtmlObject('input');
                        $check_repro->attr('type', 'checkbox');
                        $check_repro->attr('id', $id_repro);
                        $check_repro->attr('name', $id_repro);
                        $check_repro->attr('value', 1);
                    
                    $lab_check_repro->content($check_repro->get_html() . '<br>');
                    
                $div_check_repro->content($lab_check_repro->get_html());

              $div_repro->content($lab_repro->get_html());
              $div_repro->content($div_check_repro->get_html());
              
              $div_periode = new HtmlContainer('div');
              $div_periode->attr('class', 'form-group');
              
              $id_periode = 'periode';
              
                $lab_periode = new HtmlContainer('label');
                $lab_periode->attr('class', 'col-lg-2 control-label');
                $lab_periode->attr('for', $id_periode);
                $lab_periode->content('Periode de séjour');
                
                if ($called_by_get_request) {
                    $date = date('Y-m-d');
                } else {
                    $date = '';
                }
                
                $period_table = '<table id="'. $id_periode .'">
                  <tbody>
                    <tr>
                      <td style="padding-left: 15px;">
                        Du
                      </td>
                      <td>
                        <div class="input-append input-group dtpicker datetimepicker1" style="padding-left: 15px;">
                          <input id="minDate" name="minDate" value="'. $date .'" data-format="yyyy-MM-dd" placeholder="AAAA-MM-JJ" class="form-control" type="text" required="true">
                          <span id="minDatePicker" class="input-group-addon add-on">
                              <i class="fa fa-calendar" data-time-icon="fa fa-times" data-date-icon="fa fa-calendar"></i>
                          </span>
                        </div>
                      </td>
                      <td style="padding-left: 1em;">
                        Au
                      </td>
                      <td>
                        <div class="input-append input-group dtpicker datetimepicker1" style="padding-left: 15px;">
                          <input id="maxDate" name="maxDate" value="'. $date .'" data-format="yyyy-MM-dd" placeholder="AAAA-MM-JJ" class="form-control" type="text" required="true">
                          <span class="input-group-addon add-on">
                              <i class="fa fa-calendar" data-time-icon="fa fa-times" data-date-icon="fa fa-calendar"></i>
                          </span>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>';
                
              $div_periode->content($lab_periode->get_html());
              $div_periode->content($period_table);
              
              $div_hidden_farm = new HtmlObject('input');
              $div_hidden_farm->attr('type', 'hidden');
              $div_hidden_farm->attr('id', 'caught_farm_id');
              
              if ($called_by_get_request) {
                $div_hidden_farm->attr('value', $caught_farm_id);
              }
              
            $div_fieldset1->content($div_esp->get_html());
            $div_fieldset1->content($div_race->get_html());
            $div_fieldset1->content($div_sex->get_html());
            $div_fieldset1->content($div_repro->get_html());
            $div_fieldset1->content($div_periode->get_html());
            $div_fieldset1->content($div_hidden_farm->get_html());
          
          $div_form->content($div_fieldset1->get_html());
          $div_form->print_html();
          
          ?>

        </div>
      </div>
      <div class="widget-foot">
        <!-- Footer goes here -->
        <button type="submit" class="btn btn-sm btn-success" id="submit_selection">Rechercher</button>
      </div>
    </div>

  </div>
</div>
    
<div class="row">
  <div class="col-md-12">

    <div class="widget">
      <div class="widget-head">
        <div class="pull-left">Historique des présences sur l'élevage</div>
        <div class="widget-icons pull-right">
          <a href="javascript:export_csv()"><i class="fa fa-download"></i></a>
          <a href="../mac_bootstrap/macadmin/theme/#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
          <a href="../mac_bootstrap/macadmin/theme/#" class="wclose"><i class="fa fa-times"></i></a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="widget-content">
        <?php
        $history_table = new HtmlContainer('table');
        $history_table->attr('id', 'hist_table');
        $history_table->attr('class', 'table table-striped table-bordered table-hover');

            $history_head = new HtmlContainer('thead');

                $history_head_row = new HtmlContainer('tr');

                    $history_table_columns = array('Nom','N° d\'identification','Race','Sexe', 'Date de naissance', 'Nom du prère', 'N° d\'identitication père', 'Nom de la mère', 'N° d\'identification mère');
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

        ?>
            
          <div id="result" style="display: none">
            Veuillez choisir l'élevage
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
    $('#param_form').validate();
    
    $(document).ready(function(){
        window.addEventListener('click', function() {
            auto_fill_maxdate();
        });
        
        if ($('#caught_farm_id').val() !== '') {
            farm_id = parseInt($('#caught_farm_id').val());
            get_animals_after_races();
        }
        
    });
    
    async function get_animals_after_races() {
        await fillup_races();
        get_animals();
    }

    $('#submit_selection').on('click', function() {
        if ($('#param_form').valid()) {
            get_animals();
        }
    });

</script>
<!-- Optional scripts end -->

</body>
</html>