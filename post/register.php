 <?php
    include_once('class/Database.php');
    $database = new Database();
    $database->connection();
    $validation = parse();
    try {
        $response = $database->register($validation->nick, $validation->userName, $validation->lastUserName, $validation->email, $validation->description, $validation->password, $validation->dateBirth, $validation->role, $validation->dateJoined, $validation->status);
        print_r(json_encode($response));
    
    } catch (Exception $error) {
        print_r(json_encode($database->responseError(403, 'Invalid data')));
    }



    