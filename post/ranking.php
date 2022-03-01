<?php
include_once('class/Database.php');
include_once('class/Auth.php');
$validation = parse();
$database = new Database();
$database->connection();
try {
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NDYyNTQxNzUsImF1ZCI6ImFmMTNlNjk5YjA5OTgxYTM3YjYyYTM4NDUwYTBiZWYyYjdjZGI1M2YiLCJkYXRhIjpbImFhYUBhYWEuYWFhIiwxOF19.T4nRVQ5KDLh5T6kpOOBWPuwxLNl1A0-FkL3QEOvVehc';
    $decoded = AUTH::decodeToken($token);
    $email = $decoded->data[0];
    $id =  $decoded->data[1];
    $timeExpiration = $decoded->exp;
    $isExpired = AUTH::isExpired($timeExpiration);
    $isAdmin = $database->isAdmin($email);
    if (!$isExpired) {
        print_r($database->getRankingByUser($id));
    } else if (AUTH::isExpired($timeExpiration)) {
        echo "Ss";
        print_r(json_encode($database->responseError(403, 'Your token access is expired.')));
    } else if (!$isAdmin) {
        echo "Nn";
        print_r(json_encode($database->responseError(403, 'You are not allowed for this action.')));
    }
} catch (Exception $error) {

    echo $error;
}
