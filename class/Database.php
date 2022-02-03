<?php
include_once('configuration.php');
class Database {
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
                header("HTTP/1.1 404 Not Found");
                return $this -> responseError(403, 'Email or password incorrect');
            }
        } catch (Exception $error) {
            return $error;
        }
    }

    public function responseError($status, $message) {
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
    public function getTest()
    {
        $test = $this -> mysql -> query('Select * FROM test');
        while ($row = $test->fetch_assoc()) {
            echo $row['testNum'] .' '. $row['testString'].'<br>';
        }
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