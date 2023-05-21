// Login file for the mysql database

<?php
    $host = 'localhost';
    $databse = 'COP4331';
    $user = 'root';
    $password = 'root';
    $character_set = 'utf8mb4';
    $attributes = 'mysql:host=$host;dbname=$data;charset=character_set';
    $options = 
    [
        PDO::ATTR_ERRMODE =>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
?>
