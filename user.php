<?php
    include_once "./database.php";
    session_start();
    /*//The URL with parameters / query string.
    $url = 'https://accounts.google.com/o/oauth2/v2/auth?client_id=622226021483-gu8duueds3hoal4anv9dq7busec1bf5d.apps.googleusercontent.com&response_type=code&scope=https://www.googleapis.com/auth/gmail.send&redirect_uri=https://localhost/twitter/php/user.php&access_type=offline';
        
    //Once again, we use file_get_contents to GET the URL in question.
    file_get_contents($url);   
    $code = $_GET["code"];
    echo $code;*/

    // Preveri ce je input registracija
    if(isset($_POST["sub"]) && $_POST["sub"] === "Register"){
        $username = filter_input(INPUT_POST, "username",FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, "password",FILTER_SANITIZE_STRING);

        // Preveri ce je email email
        if (filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)){
            $email = filter_input(INPUT_POST, "email");
        } else {
            $_SESSION["error"] = "Not a valid email";
            header("Location: login.php");
        }

        $pass = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $link->prepare("SELECT email FROM users WHERE (email = ?);");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Preveri ce email ze obstaja
        if(mysqli_num_rows($result) > 0)
        {
            $_SESSION['error'] = "This email already exists";
            header("Location: login.php"); 
        }
        else
        {   
            //Vstavi uporabnika
            $date = date("Y-m-d");
            $stmt = $link->prepare("INSERT INTO users (username, password, email, joined) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss', $username, $pass, $email, $date);
            $stmt->execute();
            $result = $stmt->get_result();

            header("Location: login.php");        
        }
        //Preveri ce je kliknjen login
    }else if(isset($_POST["sub"]) && $_POST["sub"] === "Login"){
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
        $pass = filter_input(INPUT_POST, "password",FILTER_SANITIZE_STRING);

        //Izbere ce uporabnik obstaja
        $stmt = $link->prepare('SELECT * FROM users WHERE (email = ?);');
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = mysqli_fetch_array($result);
        //Preveri ce je povezava kaj vrnila
        if(mysqli_num_rows($result)==1)
        {
            if (password_verify($pass, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];        
                $_SESSION['user_type'] = $row['type_id'];        
                header("Location: index.php");
            }
        }
        else
        {
            header("Location: login.php");
        }
    }else if(isset($_POST["sub"]) && $_POST["sub"] === "Edit"){
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
        $location = filter_input(INPUT_POST, "location",FILTER_SANITIZE_STRING);
        $bio = filter_input(INPUT_POST, "bio",FILTER_SANITIZE_STRING);
        $born = filter_input(INPUT_POST, "born");
        $id = $_SESSION["user_id"];

        //Izbere ce uporabnik obstaja
        $stmt = $link->prepare("UPDATE users SET username=?, location=?, bio=?, born=? WHERE id=?");
        $stmt->bind_param("sssss", $username, $location, $bio, $born, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        header("Location: profile.php");
    }else if(isset($_GET["logout"]) && $_GET["logout"] === true){
        session_destroy();
        header("Location: login.php");
    }else {
        header("Location: login.php");
    }
        
