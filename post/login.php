<?php
include_once('class/Database.php');
include_once('class/Auth.php');

$validation = parse();
$database = new Database();
$database->connection();
try {
    $response = $database->login($validation->email, $validation->password);
    header("Authorization:" . json_encode($response));
    print_r(json_encode($response));
    $responses = Auth::decodeToken($response);
    print_r($responses);
} catch (Exception $error) {
    print_r(json_encode($database->responseError(403, 'Invalid data')));
}
