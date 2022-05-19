<?php
include_once('class/Database.php');
include_once('class/Auth.php');
$database = new Database();
$database->connection();
try {
    $token = getClientToken();
    $decoded = AUTH::decodeToken($token);
    $id = $decoded->data[1];
    $timeExpiration = $decoded->exp;
    $isExpired = AUTH::isExpired($timeExpiration);
    if ($isExpired) {
        print_r(json_encode($database->responseError(403, 'Your token access is expired.')));
    }
    if ($email) {
        if (!$isExpired) {
            $response = new stdClass();
            $response->exists = $database->emailExists($email);
            $response->admin = $database->isAdmin($email);
            print_r(json_encode($response));
            return;
        }
    } else {
        $response = new stdClass();
        $response->exists = $database->emailExists($email);
        $response->admin = $database->isAdmin($email);
        print_r(json_encode($response));
        return;
    }
} catch (Exception $error) {
    print_r(json_encode($database->responseError(403, 'Error with your token.')));
}
