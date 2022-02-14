 <?php
  include_once('class/Database.php');
 $validation = parse();
    $database = new Database();
    $database -> connection();
    try {
      
        $response = $database -> register($validation -> nick,$validation -> userName,$validation -> lastUserName,$validation -> email,$validation -> description, $validation -> password, $validation -> dateBirth, $validation -> avatar, $validation -> role, $validation -> dateJoined, $validation -> status);
        
    } catch (Exception $error) {
        print_r(json_encode($database -> responseError(403, 'Invalid data')));
    }