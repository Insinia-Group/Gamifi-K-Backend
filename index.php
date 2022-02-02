<?php 
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    require_once('class/Router.php');

    /* GETs */
    get('/status', 'get/status.php');

    /* POSTs */
    post('/login', 'post/login.php');
?>
