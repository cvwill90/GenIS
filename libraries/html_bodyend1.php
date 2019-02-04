<?php
/**
 * Created by PhpStorm.
 * User: Christophe_2
 * Date: 24/02/2016
 * Time: 00:25
 */

?>

</div>
</div>

<!-- Matter ends -->

</div>
<!-- Mainbar ends -->
<div class="clearfix"></div>

</div>
<!-- Content ends -->

<!-- Footer starts -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Copyright info -->
                <p class="copy">Copyright &copy; 2015 | <a href="../mac_bootstrap/macadmin/theme/#">Conservatoire des Races d'Aquitaine</a> </p>
            </div>
        </div>
    </div>
</footer>

<!-- Footer ends -->

<!-- Scroll to top -->
<span class="totop"><a href="../mac_bootstrap/macadmin/theme/#"><i class="fa fa-chevron-up"></i></a></span>

<!-- JS --->
<script src="../mac_bootstrap/macadmin/theme/js/bootstrap.min.js"></script> <!-- Bootstrap -->
<script src="../mac_bootstrap/macadmin/theme/js/jquery-ui.min.js"></script> <!-- jQuery UI -->
<script src="../mac_bootstrap/macadmin/theme/js/moment.min.js"></script> <!-- Moment js for full calendar -->
<script src="../mac_bootstrap/macadmin/theme/js/fullcalendar.min.js"></script> <!-- Full Google Calendar - Calendar -->
<script src="../mac_bootstrap/macadmin/theme/js/jquery.rateit.min.js"></script> <!-- RateIt - Star rating -->
<script src="../mac_bootstrap/macadmin/theme/js/jquery.prettyPhoto.js"></script> <!-- prettyPhoto -->
<script src="../mac_bootstrap/macadmin/theme/js/jquery.slimscroll.min.js"></script> <!-- jQuery Slim Scroll -->
<script src="../mac_bootstrap/macadmin/theme/js/jquery.dataTables.min.js"></script> <!-- Data tables -->

<!-- jQuery validator -->
<script type="text/javascript" src="../libraries/js/jquery-validation-1.17.0/dist/jquery.validate.js"></script>
<script type="text/javascript" src="../libraries/js/jquery-validation-1.17.0/dist/localization/messages_fr.js"></script>
<script type="text/javascript" src="../libraries/js/jquery-validation-1.17.0/src/custom/custom.js"></script>

<!-- jQuery Flot -->
<script src="../mac_bootstrap/macadmin/theme/js/excanvas.min.js"></script>
<script src="../mac_bootstrap/macadmin/theme/js/jquery.flot.js"></script>
<script src="../mac_bootstrap/macadmin/theme/js/jquery.flot.resize.js"></script>
<script src="../mac_bootstrap/macadmin/theme/js/jquery.flot.pie.js"></script>
<script src="../mac_bootstrap/macadmin/theme/js/jquery.flot.stack.js"></script>

<!-- jQuery Notification - Noty -->
<script src="../mac_bootstrap/macadmin/theme/js/jquery.noty.js"></script> <!-- jQuery Notify -->
<script src="../mac_bootstrap/macadmin/theme/js/themes/default.js"></script> <!-- jQuery Notify -->
<script src="../mac_bootstrap/macadmin/theme/js/layouts/bottom.js"></script> <!-- jQuery Notify -->
<script src="../mac_bootstrap/macadmin/theme/js/layouts/topRight.js"></script> <!-- jQuery Notify -->
<script src="../mac_bootstrap/macadmin/theme/js/layouts/top.js"></script> <!-- jQuery Notify -->
<!-- jQuery Notification ends -->

<script src="../mac_bootstrap/macadmin/theme/js/sparklines.js"></script> <!-- Sparklines -->
<script src="../mac_bootstrap/macadmin/theme/js/jquery.cleditor.min.js"></script> <!-- CLEditor -->
<script src="../mac_bootstrap/macadmin/theme/js/bootstrap-datetimepicker.min.js"></script> <!-- Date picker -->
<script src="../mac_bootstrap/macadmin/theme/js/jquery.onoff.min.js"></script> <!-- Bootstrap Toggle -->
<script src="../mac_bootstrap/macadmin/theme/js/filter.js"></script> <!-- Filter for support page -->
<script src="../mac_bootstrap/macadmin/theme/js/custom.js"></script> <!-- Custom codes -->
<script src="../mac_bootstrap/macadmin/theme/js/charts.js"></script> <!-- Charts & Graphs -->

<script>
    $('#save').click(function(){
        $.ajax({
            method: "POST",
            url: "../libraries/ajax/ajaxSaveDB.php",
            dataType: "json",
            error: function(data){
                alert('Une erreur inattendue s\'est produite lors de la sauvegarde de la base de données...');
            },
            success: function(data){
                if (!data.status){
                    alert('La base de données a bien été sauvegardée.');
                } else {
                    alert('La sauvegarde de la base de données a échoué !');
                }
            }
        });
    });
    
    function export_database(evt){
        //console.log(evt)
        $.ajax({
            method: "POST",
            url: "../libraries/ajax/ajax_exportation.php?type=" + evt.id,
            dataType: "html",
            error: function(data){
                alert(errorMsg);
            },
            success: function(data){
                if (!data.status){
                    alert('La base de données a bien été exportée.');
                } else {
                    alert('L\'exportation de la base de données a échoué !');
                }
            }
        });
    }
    
    // $('#export_intranet').click(function(){
        
    // });
</script>
