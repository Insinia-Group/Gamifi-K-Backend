<?php 
    require 'class/Database.php';
    require 'class/Auth.php';
    require 'configuration.php';
    // $test = new Database();
    // $test -> connection();
    $hashedSecret = password_hash(getenv('JWT_TOKEN_SECRET'), PASSWORD_BCRYPT);
    if (password_verify(getenv('JWT_TOKEN_SECRET'), $hashedSecret)) {
        echo 'true<br>';
    } else {
        echo 'false<br>';
    }
    
    $usuario  = 'eduardo';
    $password = '123456';
    if($usuario === 'eduardo' && $password === '123456')
    {
        echo $a = Auth::SignIn([
            'id' => 1,
            'name' => 'Edu'
        ]);

    }

?>