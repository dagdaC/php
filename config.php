<?php
    define('DB_NAME', 'bo1nrvxhwi8phuvv4jsz'); //'id13026142_werecycle'
    define('DB_USER', 'u1wyeal6dsm7ounv'); //'id13026142_dagda'
    define('DB_PASSWORD', 'oCzSpYOoonAxeqeXx77b'); //'81857818577Cr1571@n'
    define('DB_HOST', 'bo1nrvxhwi8phuvv4jsz-mysql.services.clever-cloud.com');

    $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);


    date_default_timezone_set('America/Bogota');
    
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
?>