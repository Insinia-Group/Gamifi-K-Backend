<?php
include_once('class/Database.php');
include_once('class/Auth.php');
$validation = parse();
$database = new Database();
$database->connection();
try {
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NDU4OTg3NDUsImF1ZCI6ImVmYWVjMWY2YjE4YmYyYjc3ZTRmN2I0YjA2OWUwOTRlODJhYjI3NDUiLCJkYXRhIjpbImFhYUBhYWEuYWFhIl19.rP8htpbqdFMdfteBjrx_jTEn9n7UKjFgCJNaw9XAE2M';
    $decoded = AUTH::decodeToken($token);
    $email = $decoded->data[0];
    $timeExpiration = $decoded->exp;
    $isExpired = AUTH::isExpired($timeExpiration);
    $isAdmin = $database->isAdmin($email);
    if (!$isExpired && $isAdmin) {
        print_r(json_encode($database->getRankingByUser($validation->idUser)));
    } else if (AUTH::isExpired($timeExpiration)) {
        print_r(json_encode($database->responseError(403, 'Your token access is expired.')));
    } else if (!$isAdmin) {
        print_r(json_encode($database->responseError(403, 'You are not allowed for this action.')));
    }
} catch (Exception $error) {
    echo $error;
}
