<?php  
    include_once('class/Database.php');
    $validation = parse();
    $database = new Database();
    $database -> connection();
    $database -> login($validation -> email, $validation -> password);
    