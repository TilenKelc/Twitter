<?php
    session_start();
    if(isset($_SESSION['error'])){
        echo ("<script LANGUAGE='JavaScript'>"
                . "window.alert('".$_SESSION['error']."')"
                . "</script>");
        unset($_SESSION['error']);
    }
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Twitter</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="./css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    <body>
        <div class="left-side">
            <div class="box">
                <img src="./img/logo.jpg" alt="Logo">
                <h2>Welcome to Twitter</h2>
            </div>
        </div>
        <div class="right-side">
            <form method="POST" action="./user.php" class="login">
                <h2>See what’s happening in the world right now</h2>
                <input type="text" name="email" placeholder="Email" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="submit" name="sub" value="Login"><br>
                <a onclick="Change(0)">Sign up?</a>
            </form>
            <form method="POST" action="./user.php" class="reg">
                <h2>See what’s happening in the world right now</h2>
                <p class="pass-error">Passwords do not match</p>
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="text" name="email" placeholder="Email" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="password" name="password_check" placeholder="Confirm Password" required><br>
                <input type="submit" name="sub" value="Register"><br>
                <a onclick="Change(1)">Back</a>
            </form>
        </div>
        <script>
            var Change = function(button){
                var login = document.getElementsByClassName("login")[0];
                var reg = document.getElementsByClassName("reg")[0];
                if(button === 0){
                    login.style.display = "none";
                    reg.style.display = "block";
                }else{
                    login.style.display = "block";
                    reg.style.display = "none";
                }
            }

            var timer = null;
            $('input[name=password]').keydown(function(){
                clearTimeout(timer); 
                timer = setTimeout(checkPasswords, 500);
            });
            $('input[name=password_check]').keydown(function(){
                clearTimeout(timer); 
                timer = setTimeout(checkPasswords, 500);
            });

            var checkPasswords = function(){
                var error = document.getElementsByClassName("pass-error")[0];
                var password_1 = document.getElementsByName("password")[1].value;
                var password_2 = document.getElementsByName("password_check")[0].value;
                if(password_1 !== password_2){
                    error.style.visibility = "visible";
                }else{
                    error.style.visibility = "hidden";
                }
            }
        </script>
    </body>
</html>







