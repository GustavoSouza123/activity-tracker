<?php
    define('HOST', 'localhost');
    define('USERNAME', 'root');
    define('PASSWORD', '');
    define('DBNAME', 'activity-tracker');

    try {
        $pdo = new PDO('mysql:host='.HOST, USERNAME, PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE DATABASE IF NOT EXISTS `".DBNAME."`;
                USE `activity-tracker`;
                CREATE TABLE IF NOT EXISTS `activities` (
                    id INT NOT NULL AUTO_INCREMENT,
                    name VARCHAR(55) NOT NULL,
                    PRIMARY KEY(id)
                )";
        $pdo->exec($sql);
    } catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
?>