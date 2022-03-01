<?php
include_once('class/Database.php');
include_once('class/Auth.php');
$validation = parse();
$database = new Database();
$database->connection();
try {
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NDYyNDc2NjgsImF1ZCI6ImVmYWVjMWY2YjE4YmYyYjc3ZTRmN2I0YjA2OWUwOTRlODJhYjI3NDUiLCJkYXRhIjpbImFhYUBhYWEuYWFhIiwxOF19.Yu2aR6rCjz_AzXtonriovSbyL_Cp2AV7YVyH5F-v9LQ';
    $decoded = AUTH::decodeToken($token);
    $email = $decoded->data[0];
    $decoded->data[1] = 18;
    $idUser = $decoded->data[1];
    $timeExpiration = $decoded->exp;
    $isExpired = AUTH::isExpired($timeExpiration);
    $isAdmin = $database->isAdmin($email);
    if (!$isExpired && $isAdmin) {
        print_r(json_encode($database->getRankingByUser($idUser)));
    } else if (AUTH::isExpired($timeExpiration)) {
        print_r(json_encode($database->responseError(403, 'Your token access is expired.')));
    } else if (!$isAdmin) {
        print_r(json_encode($database->responseError(403, 'You are not allowed for this action.')));
    }
} catch (Exception $error) {

    echo $error;
}
