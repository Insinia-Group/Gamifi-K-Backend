<?php 
    require_once('class/Router.php');
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Expose-Headers: Authorization');
    require_once('class/Router.php');


    /* GETs */
    get('/status', 'get/status.php');
    get('/users', 'get/users.php');

    /* POSTs */
    post('/login', 'post/login.php');
    post('/singup', 'post/signup.php');
    post('/register', 'post/register.php');
    post('/uploadAvatar', 'post/uploadAvatar.php');
