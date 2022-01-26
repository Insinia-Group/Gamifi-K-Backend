<?php 
    require 'configuration.php';
    include_once('class/Database.php');
    $databaseCon = new Database();
    $databaseCon -> connection();
    $databaseCon -> getTest();
?>