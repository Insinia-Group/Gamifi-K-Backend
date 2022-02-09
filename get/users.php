<?php
    include_once('class/Database.php');
    include_once('class/Auth.php');
    $database = new Database();
    $database -> connection();
    try {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NDQ1MTcyODUsImF1ZCI6IjJhZmIwYzM1MjliNTk4MGI5MmQ0MTM5NmU3MDU5YWYxNjZlMDI2NTEiLCJkYXRhIjpbInVzZXJAYXNkLmNvbSJdfQ.0sWnK_B4vrqC9e8M3zcLUWV5Qso5IBV93UOo4xRIaEc';
        $decoded = AUTH::decodeToken($token);
        $email = $decoded -> data[0];
        $timeExpiration = $decoded -> exp;
        $isExpired = AUTH::isExpired($timeExpiration);
        $isAdmin = $database -> isAdmin($email);
        if (!$isExpired && $isAdmin) {
            print_r(json_encode($database -> getUsers()));
        } else if (AUTH::isExpired($timeExpiration)) {
            print_r(json_encode($database -> responseError(403, 'Your token access is expired.')));
        } else if (!$isAdmin) {
            print_r(json_encode($database -> responseError(403, 'You are not allowed for this action.')));
        }
    } catch (Exception $error) {
        echo $error;
    }
?>