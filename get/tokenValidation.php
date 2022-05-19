<?php
include_once('class/Database.php');
include_once('class/Auth.php');
include_once('class/Helper.php');
$database = new Database();
$database->connection();
try {
    if (getClientToken() == null) {
        $obj->isValid = false;
        return print_r(json_encode($obj));
    } else {
        $token = getClientToken();
        $decoded = AUTH::decodeToken($token);
        $email = $decoded->data[0];
        $timeExpiration = $decoded->exp;
        $isExpired = AUTH::isExpired($timeExpiration);
        $isAdmin = $database->isAdmin($email);
        $obj = new stdClass();
        $obj->isValid = false;

        if (AUTH::isExpired($timeExpiration)) {
            $obj->isValid = false;
            return  print_r(json_encode($obj));
        } else {
            $obj->isValid = true;
            return  print_r(json_encode($obj));
        }
    }
} catch (Exception $error) {
    echo $error;
}
