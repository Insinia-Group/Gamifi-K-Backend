<?php
    require __DIR__.'/vendor/autoload.php';
    use Symfony\Component\Dotenv\Dotenv;
    $dotenv = new Dotenv();
    $dotenv->load('.env');
    $dotenv->load(__DIR__.'/.env', __DIR__.'/.env');
?>