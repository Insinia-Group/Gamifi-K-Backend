<?php  
    include_once('class/Database.php');
    $validation = parse();
    $database = new Database();
    $database -> connection();
    $response = $database -> login($validation -> email, $validation -> password);
    // header('Auth: ' . $response -> token);
    print_r(json_encode($response));