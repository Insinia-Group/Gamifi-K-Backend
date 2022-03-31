<?php
include_once('class/Database.php');
include_once('class/Auth.php');
$validation = parse();
$database = new Database();
$database->connection();
try {
    $token = $database->login($validation->email, $validation->password);
    if ($token) {
        header("Authorization:" . json_encode($token));
    }
} catch (Exception $error) {
    print_r(json_encode($database->responseError(403, 'Invalid data')));
}
