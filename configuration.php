<?php 
    /* CONFIGURATION */
    /* Requires */
    require __DIR__.'/vendor/autoload.php';

    /* Uses */
    use Firebase\JWT\Key;
    use Firebase\JWT\JWT;
    use Symfony\Component\Dotenv\Dotenv;

    /* Init classes*/
    $dotenv = new Dotenv();
    $dotenv -> load(__DIR__.'/.env');
?>