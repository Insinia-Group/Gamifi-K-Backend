<?php 
    include_once('configuration.php');
    include_once('class/Request.php');
    include_once('class/Database.php');
    include_once('class/Router.php');
    include_once('class/Auth.php');
    $database = new Database();
    $database -> connection();

    $router = new Router(new Request);

    $router->get('/', function($request) {
        return 'Estas en /';
    });

    $router->post('/login', function($request) {

        AUTH::signIn([
            "email" => 'paco',
            "password" => '123'
        ]);

        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        fwrite($myfile, implode($input));
        return json_encode(json_decode($inputJSON, TRUE));
    });

    $router->post('/asd', function($request) {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        fwrite($myfile, strval(json_encode($input, true)));
        return json_encode($input, true);
    });
?>