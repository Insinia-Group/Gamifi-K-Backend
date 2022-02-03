<?php
include_once('configuration.php');
class Database {
    public $mysql;

    /* METHODs */
    /**
     * connection - Inicializa la conexón.
     *
     * @param void vacío.
     *
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

    public function login($email, $password)
    {
        try {
            $query = $this -> mysql -> prepare('SELECT `id`, `email`, `password` FROM User WHERE email = ?');
            $query -> bind_param('s', $email);
            $query -> execute();
            $result = $query -> get_result();
            $row = $result -> fetch_array(MYSQLI_ASSOC);
            $verificationPassword = password_verify($password, $row['password']);
            echo $verificationPassword;
        } catch (Exception $error) {
            return $error;
        }
    }

     /**
     * getTest - Printa la tabla test de la BBDD.
     *
     * @param void vacío.
     *
     */
    public function getTest()
    {
        $test = $this -> mysql -> query('Select * FROM test');
        while ($row = $test->fetch_assoc()) {
            echo $row['testNum'] .' '. $row['testString'].'<br>';
        }
    }

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