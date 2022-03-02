<?php 
    include_once('class/Database.php');
    $database = new Database();
    try {
        $database -> connection();
        print_r(json_encode($database -> status()));
    } catch(Exception $error) {
        print_r(json_encode($database->responseError(403, 'false')));
    }                           