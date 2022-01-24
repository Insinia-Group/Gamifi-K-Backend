<?php 
    require __DIR__.'/vendor/autoload.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use Symfony\Component\Dotenv\Dotenv;
    $dotenv = new Dotenv();
    $dotenv -> load(__DIR__.'/.env');
    $secret = getenv('DB_HOST');
    $token = array(
        "iss" => "http://example.org",
        "aud" => "http://example.com",
        "iat" => 1356999524,
        "nbf" => 1357000000
    );
    $jwt = JWT::encode($token, $secret, 'HS256');
    $decoded = JWT::decode($jwt, new Key(getenv('DB_HOST'), 'HS256'));
?>