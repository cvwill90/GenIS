<?php
/**
 * Created by PhpStorm.
 * User: Christophe_2
 * Date: 23/02/2016
 * Time: 19:04
 */

session_start();
session_destroy();

header('Location: login.html');
exit();
