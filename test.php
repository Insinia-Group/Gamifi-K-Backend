<?php 
    require 'class/Database.php';
    require 'class/Auth.php';
    require 'configuration.php';
    // $test = new Database();
    // $test -> connection();
    
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