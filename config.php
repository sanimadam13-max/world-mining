<?php

$host = "sql112.infinityfree.com";

$db   = "if0_40711351_worldmining";

$user = "if0_40711351";

$pass = "j34c5ts7"; // MUHIMMI

try {

    $pdo = new PDO(

        "mysql:host=$host;dbname=$db;charset=utf8mb4",

        $user,

        $pass,

        [

            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC

        ]

    );

} catch (PDOException $e) {

    die("DB ERROR: " . $e->getMessage());

}