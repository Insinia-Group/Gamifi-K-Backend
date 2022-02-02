<?php 
    require_once('class/Router.php');

    /* GETs */
    post('/status', 'get/status.php');

    /* POSTs */
    post('/login', 'views/login.php');
?>
