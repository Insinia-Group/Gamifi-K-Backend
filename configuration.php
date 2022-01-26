<?php 
    require __DIR__.'/vendor/autoload.php';
    use Firebase\JWT\Key;
    use Firebase\JWT\JWT;
    use Symfony\Component\Dotenv\Dotenv;
    $dotenv = new Dotenv();
    $dotenv -> load(__DIR__.'/.env');
?>