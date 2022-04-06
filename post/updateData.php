<?php
include_once('class/Database.php');
include_once('class/Auth.php');
$database = new Database();
$database->connection();
$validation = parse();
$token = getClientToken();
$decoded = AUTH::decodeToken($token);
$id = $decoded->data[1];
try {
    $database->updateData($validation->id, $validation->idUser, $validation->points);
    $response = new stdClass();
    $response->status = true;
    print_r(json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
} catch (Exception $error) {
    print_r(json_encode($database->responseError(403, 'Error while updating the profile picture')));
}
