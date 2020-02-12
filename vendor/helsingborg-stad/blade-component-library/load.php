<?php 

    //Define basepath
    define('BCL_BASEPATH', dirname(__FILE__) . '/');

    //Autload controllers etc
    require_once BCL_BASEPATH . 'vendor/autoload.php';

    //Include base classes
    include BCL_BASEPATH . '/src/Init.php';
    include BCL_BASEPATH . '/src/Register.php';
    