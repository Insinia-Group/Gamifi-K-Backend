<?php 
    include_once('class/Database.php');
    $database = new Database();
    $database -> connection();
    $data = $database -> getUsers();
    print_r(json_encode($data));