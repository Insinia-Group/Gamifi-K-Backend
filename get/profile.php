<?php
include_once('class/Database.php');
include_once('class/Auth.php');
include_once('class/Helper.php');
$database = new Database();
$database->connection();
try {
    $token = getClientToken();
    $decoded = AUTH::decodeToken($token);
    $email = $decoded->data[0];
    $id = $decoded->data[1];
    $timeExpiration = $decoded->exp;
    $isExpired = AUTH::isExpired($timeExpiration);
    $isAdmin = $database->isAdmin($email);
    if (!$isExpired && $isAdmin) {
        $database->getProfile($id);
    } else if (AUTH::isExpired($timeExpiration)) {
        print_r(json_encode($database->responseError(403, 'Your token access is expired.')));
    } else if (!$isAdmin) {
        print_r(json_encode($database->responseError(403, 'You are not allowed for this action.')));
    }
} catch (Exception $error) {
    print_r($error);
}
