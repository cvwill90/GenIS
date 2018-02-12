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
/*if (is_dir('libraries')){
    require 'libraries/constants.php';
} else {
    require '../../libraries/constants.php';
}*/
?>

<!-- Session starts -->
<div>
    <?php

    if (isset($_SESSION['compte'])){
        $user = $_SESSION['contact'];
        $account = $_SESSION['compte'];
        $priv = $_SESSION['privilege'];
    } else {
        header('Location: http://genis.cra/connexion/login.html');
        exit();
    }

    ?>
</div>

<!-- Navbar starts -->
<div class="navbar navbar-fixed-top bs-docs-nav" role="banner">

    <div class="conjtainer">
        <!-- Menu button for smallar screens -->
        <div class="navbar-header">
            <button class="navbar-toggle btn-navbar" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                <span>Menu</span>
            </button>
            <!-- Site name for smallar screens -->
            <a href="../../mac_bootstrap/macadmin/theme/index.html" class="navbar-brand hidden-lg">MacBeth</a>
        </div>



        <!-- Navigation starts -->
        <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">


            <!-- Search form -->
            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search">
                </div>
            </form>
            <!-- Links -->
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown pull-right">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="../../mac_bootstrap/macadmin/theme/#">
                        <i class="fa fa-user"></i> Admin <b class="caret"></b>
                    </a>

                    <!-- Dropdown menu -->
                    <ul class="dropdown-menu">
                        <li><a href="../../mac_bootstrap/macadmin/theme/#"><i class="fa fa-user"></i> Profile</a></li>
                        <li><a href="../../mac_bootstrap/macadmin/theme/#"><i class="fa fa-cogs"></i> Settings</a></li>

                        <li><a href="../../connexion/deconnexion.php"><i class="fa fa-sign-out"></i> Logout</a></li>

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
                    <h1><a href="../../mac_bootstrap/macadmin/theme/#">Mac<span class="bold">Admin</span></a></h1>
                    <p class="meta">something goes in meta area</p>
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
        <div class="sidebar-dropdown"><a href="../../mac_bootstrap/macadmin/theme/#">Navigation</a></div>

        <!--- Sidebar navigation -->
        <!-- If the main navigation has sub navigation, then add the class "has_sub" to "li" of main navigation. -->
        <ul id="nav">
            <!-- Main menu with font awesome icon -->
            <li><a href="../../mac_bootstrap/macadmin/theme/index.html"><i class="fa fa-home"></i> Dashboard</a>
                <!-- Sub menu markup
                <ul>
                  <li><a href="../../mac_bootstrap/macadmin/theme/#">Submenu #1</a></li>
                  <li><a href="../../mac_bootstrap/macadmin/theme/#">Submenu #2</a></li>
                  <li><a href="../../mac_bootstrap/macadmin/theme/#">Submenu #3</a></li>
                </ul>-->
            </li>
            <li class="has_sub"><a href="../../mac_bootstrap/macadmin/theme/#"><i class="fa fa-list-alt"></i> Widgets  <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
                <ul>
                    <li><a href="../../mac_bootstrap/macadmin/theme/widgets1.html">Widgets #1</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/widgets2.html">Widgets #2</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/widgets3.html">Widgets #3</a></li>
                </ul>
            </li>
            <li class="has_sub"><a href="../../mac_bootstrap/macadmin/theme/#"><i class="fa fa-file-o"></i> Pages #1  <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
                <ul>
                    <li><a href="../../mac_bootstrap/macadmin/theme/post.html">Post</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/login.html">Login</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/register.html">Register</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/support.html">Support</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/invoice.html">Invoice</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/gallery.html">Gallery</a></li>
                </ul>
            </li>
            <li class="has_sub"><a href="../../mac_bootstrap/macadmin/theme/#"><i class="fa fa-file-o"></i> Pages #2  <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
                <ul>
                    <li><a href="../../mac_bootstrap/macadmin/theme/media.html">Media</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/statement.html">Statement</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/error.html">Error</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/error-log.html">Error Log</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/calendar.html">Calendar</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/grid.html">Grid</a></li>
                </ul>
            </li>
            <li class="has_sub"><a href="../../mac_bootstrap/macadmin/theme/#"><i class="fa fa-table"></i> Tables  <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>
                <ul>
                    <li><a href="../../mac_bootstrap/macadmin/theme/tables.html">Tables</a></li>
                    <li><a href="../../mac_bootstrap/macadmin/theme/dynamic-tables.html">Dynamic Tables</a></li>
                </ul>
            </li>
            <li><a href="../../mac_bootstrap/macadmin/theme/charts.html"><i class="fa fa-bar-chart-o"></i> Charts</a></li>
            <li><a href="../../mac_bootstrap/macadmin/theme/forms.html"><i class="fa fa-tasks"></i> Forms</a></li>
            <li><a href="../../mac_bootstrap/macadmin/theme/ui.html"><i class="fa fa-magic"></i> User Interface</a></li>
        </ul>
    </div>
    <!-- Sidebar ends -->

    <!-- Main bar -->
    <div class="mainbar">

        <!-- Page heading -->
        <div class="page-head">
            <h2 class="pull-left"><i class="fa fa-home"></i> Dashboard</h2>

            <!-- Breadcrumb -->
            <div class="bread-crumb pull-right">
                <a href="../../mac_bootstrap/macadmin/theme/index.html"><i class="fa fa-home"></i> Home</a>
                <!-- Divider -->
                <span class="divider">/</span>
                <a href="../../mac_bootstrap/macadmin/theme/#" class="bread-current">Dashboard</a>
            </div>

            <div class="clearfix"></div>

        </div>
        <!-- Page heading ends -->


        <!-- Matter -->

        <div class="matter">
            <div class="container">
