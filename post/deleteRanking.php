<?php
    include_once('class/Database.php');
    $database = new Database();
    $database->connection();
    $validation = parse();
    try {
        $response = $database->deleteRanking($validation->idRanking);
        print_r(json_encode($response));
    
    } catch (Exception $error) {
        print_r(json_encode($database->responseError(403, 'Invalid data')));
    }