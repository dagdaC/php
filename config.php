<?php
    define('DB_NAME', 'werecycle'); //'id13026142_werecycle'
    define('DB_USER', 'root'); //'id13026142_dagda'
    define('DB_PASSWORD', 'root'); //'81857818577Cr1571@n'
    define('DB_HOST', 'localhost');

    $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);


    date_default_timezone_set('America/Bogota');
    
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
?>