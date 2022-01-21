<?php

class Database {
    /* CONSTRUCTOR */
    function __construct(){}

    public function connection() {
        require 'configuration.php';
        echo getenv('DB_HOST').' '.getenv('DB_USERNAME').' '.getenv('DB_PASSWORD').' '.getenv('DB_DATABASE');
        $mysql = new mysqli(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'));
        $mysql -> set_charset("utf8");
        $res = $mysql -> query("SELECT * FROM test");
        while($f = $res->fetch_object()){
            echo $f->nombre.' <br/>';
        }
    }
}

?>