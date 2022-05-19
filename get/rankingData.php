<?php
include_once('class/Database.php');
include_once('class/Auth.php');
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
    $validation = parse();
    json_encode($database->getRankingData($validation->rankingId, $id));
} catch (Exception $error) {
    print_r($error);
}
