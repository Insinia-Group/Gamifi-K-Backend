<?php 
    include_once('class/Database.php');
    $database = new Database();
    try {
        $database -> connection();
        echo json_encode($database -> status());
    } catch(Exception $error) {
        echo 'error';
    }                           