<?php
/**
 * Created by PhpStorm.
 * User: Christophe_2
 * Date: 23/02/2016
 * Time: 19:38
 */

?>

<!-- Require needed php pages -->
<?php
require_once 'fonctions.php';

/////// Session

// Check if session expired
invalidate_session_if_expired();

if (isset($_SESSION['compte'])){
    //$user = $_SESSION['contact'];
    $user = $_SESSION['utilisateur'];
    $account = $_SESSION['compte'];
    $priv = $_SESSION['privilege'];
} else {
    $location = get_web_location('/connexion/login.html');
    header('Location: ' . $location);
    exit();
}

?>

<!-- Navbar starts -->
<div class="navbar navbar-fixed-top bs-docs-nav" role="banner">

    <div class="conjtainer">
        <!-- Menu button for smallar screens -->
        <div class="navbar-header">
            <button class="navbar-toggle btn-navbar" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                <span>Menu</span>
            </button>
            <!-- Site name for smallar screens -->
            <a href="../mac_bootstrap/macadmin/theme/index.html" class="navbar-brand hidden-lg">MacBeth</a>
        </div>



        <!-- Navigation starts -->
        <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">


            <!-- Search form -->
            <!--<form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search">
                </div>
            </form>-->
            <!-- Links -->
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown pull-right">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="../mac_bootstrap/macadmin/theme/#">
                        <i class="fa fa-user"></i> Menu <b class="caret"></b>
                    </a>

                    <!-- Dropdown menu -->
                    <ul class="dropdown-menu">
                        <!--<li><a href="../mac_bootstrap/macadmin/theme/#"><i class="fa fa-user"></i> Profile</a></li>-->
                        <li><a id="<?php echo EXPORT_TYPES['intranet']?>" onclick="export_database(this)"><i class="fa fa-cloud-upload"></i> Exportation Intranet</a></li>
                        <li><a id="<?php echo EXPORT_TYPES['intern']?>" onclick="export_database(this)"><i class="fa fa-cloud-upload"></i> Exportation pour usage interne</a></li>
                        <li><a id="save"><i class="fa fa-floppy-o"></i> Sauvegarde base de données</a></li>
                        <li><a href="../connexion/deconnexion.php"><i class="fa fa-sign-out"></i> Déconnexion</a></li>

                    </ul>
                </li>

            </ul>
        </nav>

    </div>
</div>
<!-- Navbar ends -->

<!-- Header starts -->
<header>
    <div class="container">
        <div class="row">

            <!-- Logo section -->
            <div class="col-md-4">
                <!-- Logo. -->
                <div class="logo">
                    <h1><a href="../">GenIS<!--<span class="bold">Admin</span>--></a></h1>
                    <p class="meta">Conservatoire des Races d'Aquitaine</p>
                </div>
                <!-- Logo ends -->
            </div>

            <!-- Button section -->


            <!-- Data section -->


        </div>
    </div>
</header>
<!-- Header ends -->

<!-- Main content starts -->
<div class="content">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-dropdown"><a href="../mac_bootstrap/macadmin/theme/#">Navigation</a></div>

        <!--- Sidebar navigation -->
        <!-- If the main navigation has sub navigation, then add the class "has_sub" to "li" of main navigation. -->
        <ul id="nav">
            <!-- Main menu with font awesome icon -->
            <li class="has_sub <?php if ($_SESSION['current_page']=='naiss' || $_SESSION['current_page']=='mort' || $_SESSION['current_page']=='mouv' || $_SESSION['current_page']=='prop' || $_SESSION['current_page']=='modifAnim') echo 'open'; ?>"><a href="../entrees/#"><i class="fa fa-database"></i> Entrées <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
                <ul>
                    <li <?php if ($_SESSION['current_page']=='naiss') echo 'class="current"'; ?>><a href="../entrees/naissances.php">Naissances</a></li>
                    <li <?php if ($_SESSION['current_page']=='mort') echo 'class="current"'; ?>><a href="../entrees/mort.php">Morts</a></li>
                    <li <?php if ($_SESSION['current_page']=='mouv') echo 'class="current"'; ?>><a href="../entrees/mouvement.php">Mouvements</a></li>
                    <li <?php if ($_SESSION['current_page']=='modifAnim') echo 'class="current"'; ?>><a href="../entrees/modifAnimal.php">Modifier un animal</a></li>
                    <!--<li <?php if ($_SESSION['current_page']=='prop') echo 'class="current"'; ?>><a href="../entrees/proprietaire.php">Changement de propriétaire</a></li>-->
                </ul>
            </li>
            <li class="has_sub <?php if ($_SESSION['current_page']=='calcGen' || $_SESSION['current_page']=='calcNoGen' || $_SESSION['current_page']=='calcDemo') echo 'open'; ?>"><a href="../calculs/#"><i class="fa fa-calculator"></i> Calculs <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
                <ul>
                    <li <?php if ($_SESSION['current_page']=='calcGen') echo 'class="current"'; ?>><a href="../calculs/selectionPedig.php">Calculs avec généalogie</a></li>
                    <!--<li <?php if ($_SESSION['current_page']=='calcNoGen') echo 'class="current"'; ?>><a href="../calculs/???.php">Calculs sans généalogie</a></li>
                    <li <?php if ($_SESSION['current_page']=='calcDemo') echo 'class="current"'; ?>><a href="../calculs/???.php">Calculs démographiques</a></li>-->
                </ul>
            </li>
            <li class="has_sub <?php if ($_SESSION['current_page'] == 'newContact' || $_SESSION['current_page'] == 'modifyContact') echo 'open'; ?>"><a href="../contacts/#"><i class="fa fa-users"></i> Contacts <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
                <ul>
                    <li <?php if ($_SESSION['current_page']=='newContact') echo 'class="current"'; ?>><a href="../contacts/nouveauContact.php">Nouveau contact</a></li>
                    <li <?php if ($_SESSION['current_page']=='modifyContact') echo 'class="current"'; ?>><a href="../contacts/modifierContact.php">Modification de contact</a></li>
                </ul>
            </li>
            <li class="has_sub <?php if ($_SESSION['current_page']=='import' || $_SESSION['current_page']=='importEleveurs') echo 'open'; ?>"><a href="../importation/#"><i class="fa fa-upload"></i> Importations <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
                <ul>
                    <li <?php if ($_SESSION['current_page']=='import' ) echo 'class="current"'; ?>><a href="../importation/import.php">Importation des animaux </a></li>
                    <li <?php if ($_SESSION['current_page']=='importEleveurs' ) echo 'class="current"'; ?>><a href="../importation/importEleveurs.php">Importation des éleveurs </a></li>
                </ul>
            </li>
            <li class="has_sub <?php if ($_SESSION['current_page']=='visu_animal' || $_SESSION['current_page']=='visu_elevage' || $_SESSION['current_page']=='del_animal') echo 'open'; ?>"><a href="../animaux/#"><i class="fa fa-info"></i> Animaux <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
                <ul>
                    <li <?php if ($_SESSION['current_page']=='visu_animal') echo 'class="current"'; ?>><a href="../animaux/visuAnimal.php">Fiches individuelles </a></li>
                    <li <?php if ($_SESSION['current_page']=='visu_elevage') echo 'class="current"'; ?>><a href="../animaux/visuElevage.php">Fiches élevage</a></li>
                    <li <?php if ($_SESSION['current_page']=='del_animal') echo 'class="current"'; ?>><a href="../animaux/delete_animals.php">Supprimer des animaux</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <!-- Sidebar ends -->

    <!-- Main bar -->
    <div class="mainbar">

        <!-- Page heading -->
        <div class="page-head">
            <h2 class="pull-left"><i class="fa fa-home"></i> GenIS</h2>

            <!-- Breadcrumb -->
            <!--<div class="bread-crumb pull-right">
                <a href="../mac_bootstrap/macadmin/theme/index.html"><i class="fa fa-home"></i> Home</a>
                <!-- Divider -->
                <!--<span class="divider">/</span>
                <a href="../mac_bootstrap/macadmin/theme/#" class="bread-current">Dashboard</a>
            </div>-->

            <div class="clearfix"></div>

        </div>
        <!-- Page heading ends -->


        <!-- Matter -->

        <div class="matter">
            <div class="container">
