<?php 
    try {
        $db = new PDO("mysql:host=localhost;dbname=api","root","");
    } catch (PDOException $ex) {
        echo $ex->getMessage();
    }
?>