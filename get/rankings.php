<?php
include_once('class/Database.php');
include_once('class/Auth.php');
$database = new Database();
$database->connection();
try {
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NDYzMjk0NzcsImF1ZCI6ImVmYWVjMWY2YjE4YmYyYjc3ZTRmN2I0YjA2OWUwOTRlODJhYjI3NDUiLCJkYXRhIjpbImNhcmFAY2FyYS5jYXJhIiwyMV19.jGHITmLoqbwf_jEH4IiIcKCoPERBcHhWRY4P-pouwVU';
    $decoded = AUTH::decodeToken($token);
    $email = $decoded->data[0];
    $id = $decoded->data[1];
    $timeExpiration = $decoded->exp;
    $isExpired = AUTH::isExpired($timeExpiration);
    $isAdmin = $database->isAdmin($email);
    if (!$isExpired && $isAdmin) {
        json_encode($database->getRankings());
    } else if (!$isExpired && !$isAdmin) {
        json_encode($database->getRankingByUser($id));
    } else if (AUTH::isExpired($timeExpiration)) {
        print_r(json_encode($database->responseError(403, 'Your token access is expired.')));
    } else if (!$isAdmin) {
        print_r(json_encode($database->responseError(403, 'You are not allowed for this action.')));
    }
} catch (Exception $error) {
    print_r($error);
}
