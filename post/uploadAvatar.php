<?php  
    include_once('class/Database.php');
    $validation = parse();
    $database = new Database();
    $database -> connection();
    try {
        $type = $_FILES['file']['type'];
        $foto=$_FILES['file']['tmp_name'];
        $data = file_get_contents($foto);

        $fotoFinal='data:'.$type.';base64,'.base64_encode($data);
      
        print_r(json_encode($fotoFinal));
    } catch (Exception $error) {
        print_r(json_encode($database -> responseError(403, 'Invalid data')));
    }
    