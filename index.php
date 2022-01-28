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

    $router->post('/register', function($request) {
        $body = $request -> getJSON();
        $userData = array();
        $userData['email'] = json_decode($body, TRUE)['email'];

        return json_encode(json_decode($body, TRUE));
    });

    $router->post('/login', function($request) {
        $body = $request -> getJSON();
        $body = json_decode($body, TRUE);
        $userData = array();
        $userData['email'] = $body['email'];
        $userData['password'] = $body['password'];
        $token = AUTH::signIn([$userData]);

        $myfile = fopen("output.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $body['password']);
        fwrite($myfile, $body['email']);
        fwrite($myfile, $token);
        return json_encode($body);
    });

    $router->post('/asd', function($request) {
        $body = $request -> getJSON();
        $body = json_decode($body, TRUE);
       
        return json_encode($body, true);
    });
?>