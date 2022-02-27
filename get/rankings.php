<?php
include_once('class/Database.php');
include_once('class/Auth.php');
$database = new Database();
$database->connection();
try {
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NDYwODcyOTQsImF1ZCI6ImRiYTZjZTg2NzQzZTNkOWE0Zjk0YzBkMDQ0ZDIyY2M1Y2JhODgzNGQiLCJkYXRhIjpbImFhYUBhYWEuYWFhIl19.1UY3EwHsa2YoUGA_wFZLGptFBzkdkzt35hyrild6MGQ';
    $decoded = AUTH::decodeToken($token);
    $email = $decoded->data[0];
    $timeExpiration = $decoded->exp;
    $isExpired = AUTH::isExpired($timeExpiration);
    $isAdmin = $database->isAdmin($email);
    if (!$isExpired && $isAdmin) {
        json_encode($database->getRankings());
    } else if (AUTH::isExpired($timeExpiration)) {
        print_r(json_encode($database->responseError(403, 'Your token access is expired.')));
    } else if (!$isAdmin) {
        print_r(json_encode($database->responseError(403, 'You are not allowed for this action.')));
    }
} catch (Exception $error) {
    print_r($error);
}
