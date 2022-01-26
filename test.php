<?php 
    include_once('configuration.php');
    $usuario  = 'eduardo';
    $password = '123456';
    if($usuario === 'eduardo' && $password === '123456')
    {
        $a = Auth::signIn([
            'id' => 2,
            'name' => '3'
        ]);

        $b = Auth::decodeToken($a);
        print_r($b);
    }

?>