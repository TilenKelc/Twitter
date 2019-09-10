<?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "twitter";

    $link = mysqli_connect($host, $user, $password) or die("Server connection error");
    mysqli_select_db($link, $database) or die("Database connection error");
    mysqli_set_charset($link, "SET NAMES 'utf-8'");

    $salt = 'lerkjth654dgk%$#$#FG';
