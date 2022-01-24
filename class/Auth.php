<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

 class Auth {
    private static $secret;

    function __construct() {
        self::$secret = getenv('DB_HOST');;
    }

    public static function createEncodedToken($data)
    {
        $time = time();
        $token = array(
            'exp' => $time + (60*60),
            'aud' => self::Aud(),
            'data' => $data
        );
        return JWT::encode($token, self::$secret, 'HS256');
    }

    public static function getDecodedToken($token) 
    {
        return JWT::decode($token, self::$secret, 'HS256');
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
 }
?>