<?php

class Database {
    /* ATTRIBUTs */
    public $mysql;

    /* METHODs */
    /**
     * connection - Inicializa la conexón.
     */
    public function connection() 
    {
        include_once('configuration.php');
        define("DB_HOST", getenv('DB_HOST'));
        define("DB_USERNAME", getenv('DB_USERNAME'));
        define("DB_PASSWORD", getenv('DB_PASSWORD'));
        define("DB_DATABASE", getenv('DB_DATABASE'));
        try {
            $this -> mysql = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
            if ($this -> mysql -> connect_errno) {
                exit();
            }
        } catch (Exception $error) {
            return $error;
        };
    }

    /**
     * login - Verifica el email y password para devolver un JWT.
     *
     * @param email string.
     * @param password string.
     *
     */
    public function login($email, $password)
    {
        try {
            $query = $this -> mysql -> prepare('SELECT `id`, `email`, `password` FROM User WHERE email = ?');
            $query -> bind_param('s', $email);
            $query -> execute();
            $result = $query -> get_result();
            $row = $result -> fetch_array(MYSQLI_ASSOC);
            if (password_verify($password, $row['password'])) {
                include_once('class/Auth.php');
                $response = new stdClass();
                $response -> token = 'BEARER ' . AUTH::login(array($row['email']));;
                return $response;
            } else {
                return $this -> responseError(403, 'Email or password incorrect');
            }
        } catch (Exception $error) {
            return $error;
        }
    }

    /**
     * responseError - Crea una instancia de la clase stdClass y devuelve un objeto con dos atributos.
     *
     * @param status string.
     * @param message string.
     *
     */
    public function responseError($status, $message) 
    {
        $response = new stdClass();
        $response -> status = $status;
        $response -> message = $message;
        $response -> autentication = false;
        $response -> authorization = false;
        return $response;
    }

    /**
     * getTest - Printa la tabla test de la BBDD.
     */
    public function status()
    {
        $response = new stdClass();
        $test = $this -> mysql -> query('SELECT * FROM `test` WHERE testNum = 99');
        $row = $test->fetch_assoc();
        if (count($row) > 0 && $row['testString'] == true) {
            $response -> status = true;
        } else {
            $response -> status = false;
        }
        return $response;
    }

    /**
     * getTest - Printa la tabla test de la BBDD.
     */
    public function getUsers()
    {
        $test = $this -> mysql -> query('Select * FROM User');
        $response = [];
        while ($row = $test->fetch_assoc()) {
          array_push($response, $row);
        }
        return $response;
    }
}

?>