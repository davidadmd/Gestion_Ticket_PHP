<?php
return [
    'host' => 'localhost',
    'dbname' => 'david_db1',
    'charset' => 'utf8mb4',
    'username' => 'david_db1',
    'password' => 'Maminivagodu06/', // Remplacez par le vrai mot de passe de votre base de données KeyHelp
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]
];