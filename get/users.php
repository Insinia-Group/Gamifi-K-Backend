<?php
    include_once('class/Database.php');
    include_once('class/Auth.php');
    $database = new Database();
    try {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NDQ0MjQ3MTgsImF1ZCI6IjJhZmIwYzM1MjliNTk4MGI5MmQ0MTM5NmU3MDU5YWYxNjZlMDI2NTEiLCJkYXRhIjpbInRlc3RAYXNkLmNvbSJdfQ.F6gRudNQTqD5AucywE_NKv4-R6XF1LWkPzLerdm8d-s';
        $decoded = AUTH::decodeToken($token);
        $email = $decoded -> data[0];
        echo AUTH::isExpired($token);
    } catch (Exception $error) {

    }