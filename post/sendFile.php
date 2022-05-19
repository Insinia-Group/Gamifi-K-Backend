<?php  
    include_once('class/Database.php');
    $validation = parse();
    $database = new Database();
    $database -> connection();
    $noRepeat = false;
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $fileName = '';
        for ($i = 0; $i < 10; $i++) {
            $fileName .= $characters[rand(0, $charactersLength - 1)];
        }
        $fileName .= "-". $_FILES['file']['name'];

    $target = "files/". $fileName;
    try {
        $idRanking=$_POST['idRanking'];
        move_uploaded_file( $_FILES['file']['tmp_name'], $target);
        $response = $database->sendFile($target,intval($idRanking));
       
    } catch (Exception $error) {
        print_r(json_encode($database -> responseError(403, 'Invalid data')));
    }