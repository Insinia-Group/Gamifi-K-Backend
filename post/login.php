<?php  
    include_once('class/Database.php');
    $validation = parse();
    $database = new Database();
    $database -> connection();
    try {
        foreach($validation as $prop => $val) {
            unset($validation->{$prop});
            $validation->{trim($prop)} = trim($val);
        }
        $response = $database -> login($validation -> email, $validation -> password);
        print_r(json_encode($response));
    } catch (Exception $error) {
        print_r(json_encode($database -> responseError(403, 'Invalid data')));
    }
    