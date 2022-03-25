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
    $database->updateAvatarById($id, $validation->image);
    $database->getProfile($id);
} catch (Exception $error) {
    print_r(json_encode($database->responseError(403, 'Invalid data')));
}
