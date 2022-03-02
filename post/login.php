<?php
include_once('class/Database.php');
include_once('class/Auth.php');
$validation = parse();
$database = new Database();
$database->connection();
try {
    $response = $database->login($validation->email, $validation->password);
    if ($response) {
        header("Authorization:" . json_encode($response));
    }
} catch (Exception $error) {
    print_r(json_encode($database->responseError(403, 'Invalid data')));
}
