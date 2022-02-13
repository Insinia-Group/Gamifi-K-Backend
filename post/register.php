<?php  
    include_once('class/Database.php');
    $database = new Database();
    $database -> connection();
    // header('Auth: ' . $response -> token);
    print_r(json_encode($response));