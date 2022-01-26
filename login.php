<?php 
    require 'configuration.php';
    include_once('class/Database.php');
    include_once('class/Router.php');
    $database = new Database();
    $database -> connection();

    $router = new Router;
    // How GET requests will be defined
    $router->get('/some/route', function($request) {
        // The $request argument of the callback 
        // will contain information about the request
        return "Content";
    });
    // How POST requests will be defined
    $router->post('/some/route', function($request) {
        // How to get data from request body
        $body = $request->getBody();
    });
?>