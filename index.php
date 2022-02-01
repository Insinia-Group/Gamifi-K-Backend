<?php 
    include_once('configuration.php');
    include_once('class/Request.php');
    include_once('class/Database.php');
    include_once('class/Router.php');
    include_once('class/Auth.php');

    $router = new Router(new Request);

    /* GETs */
     $router->get('/usuarios', function($request) {
         $database = new  Database();
         $database->connection();

        return json_encode($database->getUsers());
    });

    $router->get('/', function($request) {
        return 'Estas en 123';
    });

    $router->get('/usuario/:id', function($request) {
        return json_encode("{status: true}");
    });

    $router->get('/status', function($request) {
        $database = new Database();
        $status = new stdClass();
        $status -> status = true;
        return json_encode($status);
    });

    /* POSTs */
    $router->post('/register', function($request) {
        $body = $request -> getJSON();
        $userData = array();
        $userData['email'] = json_decode($body, TRUE)['email'];

        return json_decode($body, TRUE);
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