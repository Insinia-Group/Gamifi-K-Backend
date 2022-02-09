<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    
    class Auth {
        public static function decodeToken($token)
        {
            return JWT::decode($token, new Key(self::secret(), 'HS256'));
        }

        public static function login($data) 
        {
            $time = time();
            $token = array(
                'exp' => $time + (60 * 60 * 24 * 1),
                'aud' => self::Aud(),
                'data' => $data
            );
            return JWT::encode($token, self::secret(), 'HS256');
        }

        public static function isExpired($time)
        {
            if ($time > time()) {
                return false;
            } else {
                return true;
            }
        }

        private static function secret()
        {
            return getenv('JWT_TOKEN_SECRET');
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