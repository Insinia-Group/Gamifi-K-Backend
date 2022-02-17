<?php  
    include_once('class/Database.php');
    $validation = parse();
    $database = new Database();
    $database -> connection();
    try {
        print_r(json_encode($_FILES['pictureProfile']['name']));
    } catch (Exception $error) {
        print_r(json_encode($database -> responseError(403, 'Invalid data')));
    }
    