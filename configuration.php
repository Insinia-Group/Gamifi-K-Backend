<?php 
    require __DIR__.'/vendor/autoload.php';
    use Firebase\JWT\Key;
    use Firebase\JWT\JWT;
    use Symfony\Component\Dotenv\Dotenv;
    $dotenv = new Dotenv();
    $dotenv -> load(__DIR__.'/.env');
    $token = array(
        "iat" => 1356999524,
        "nbf" => 1357000000,
        "user" => 1,
        "admin" => 'false'
    );
    $hashedSecret = password_hash(getenv('JWT_TOKEN_SECRET'), PASSWORD_BCRYPT);
    $jwt = JWT::encode($token, $hashedSecret, 'HS256');
    $decoded = JWT::decode($jwt, new Key($hashedSecret, 'HS256'));
?>