<?php 
    require_once("{$_SERVER['DOCUMENT_ROOT']}/class/Router.php");
    require_once('class/Auth.php');
    require_once('class/Database.php');
    require_once('class/Router.php');
    require_once('configuration.php');

    /* GETs */
    get('/status', 'get/status.php');

    /* POSTs */
    post('/login', 'views/login.php');
?>
