<?php

class Database {
    public $mysql;

    /* CONSTRUCTOR */
    function __construct(){}

    /* METHODs */
    /**
     * connection - Inicializa la conexón.
     *
     * @param void vacío.
     *
     */
    public function connection() 
    {
        require 'configuration.php';
        define("DB_HOST", getenv('DB_HOST'));
        define("DB_USERNAME", getenv('DB_USERNAME'));
        define("DB_PASSWORD", getenv('DB_PASSWORD'));
        define("DB_DATABASE", getenv('DB_DATABASE'));
        $this -> mysql = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $this -> mysql -> set_charset("utf8");
        $result = $this -> $mysql -> query("SELECT * FROM test");
        while($fetched = $result -> fetch_object()){
            echo $fetched -> nombre.'<br/>';
        }
    }
}

?>