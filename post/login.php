<?php
include_once('class/Database.php');
include_once('class/Auth.php');
$validation = parse();
$database = new Database();
$database->connection();
try {
    $token = $database->login($validation->email, $validation->password);
    if ($token) {
        $response = new stdClass();
        $response->isAuthenticated = true;
        header("Authorization:" . json_encode($token));
        print_r(json_encode($response));
    }
} catch (Exception $error) {
    print_r(json_encode($database->responseError(403, 'Invalid data')));
}
