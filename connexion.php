<?php

    try {

        // Identifiers
        $host = 'localhost';
        $dbName = 'tp_mysql';
        $user = 'root';
        $password = 'root';

        // Connection to database
        $connection = new PDO('mysql:host=' . $host . ';dbname=' . $dbName, $user, $password);
        
        // Errors management
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch (PDOException $error) {

        // Display errors
        echo "Échec : " . $error->getMessage();

    }

?>