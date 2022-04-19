<?php
include_once('class/Database.php');
include_once('class/Auth.php');
$database = new Database();
$database->connection();
$validation = parse();
try {
    $response = $database->validateEmail($validation->email);
    print_r(json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
} catch (Exception $error) {
    print_r(json_encode($database->responseError(403, 'Error while updating the profile picture')));
}
