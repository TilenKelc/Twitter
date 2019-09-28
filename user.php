<?php
    include_once "./database.php";
    session_start();

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
        
        $stmt = $link->prepare("SELECT email, password FROM users WHERE (email = ?);");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = mysqli_fetch_array($result);
        
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
        $stmt = $link->prepare("SELECT u.id, t.user_type, u.password FROM users u INNER JOIN types t ON u.type_id=t.id WHERE (email = ?)");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = mysqli_fetch_array($result);
        //Preveri ce je povezava kaj vrnila
        if(mysqli_num_rows($result) == 1)
        {
            if (password_verify($pass, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];        
                $_SESSION['user_type'] = $row['user_type'];        
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
        $picture = null;

        if($_FILES["file"]["name"]){
            // Sliko preveri ce je pravi format, prevelika, ali pa ni slika ter jo shrani
            $target_dir = "uploads-profile/";
            $target_file = $target_dir . basename($_FILES["file"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["file"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            } else {
                $_SESSION['error'] = "The file is not a picture";
                $uploadOk = 0;
            }
            if ($_FILES["file"]["size"] > 1000000) {
                $_SESSION['error'] = "To big file";
                $uploadOk = 0;
            }
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg")
            {
                $_SESSION['error'] = "Only jpg, png and jpeg are allowed";
                $uploadOk = 0;
            }
            if ($uploadOk == 0) {
                header("Location:index.php");
            } else {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                        unset($_SESSION["error"]);
                        $picture = basename($_FILES["file"]["name"]);
                } else {
                    header("Location:index.php");
                }
            }
        }

        //Izbere ce uporabnik obstaja
        $stmt = $link->prepare("UPDATE users SET username=?, location=?, bio=?, born=?, avatar=? WHERE id=?");
        $stmt->bind_param("ssssss", $username, $location, $bio, $born, $picture, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        header("Location: profile.php");

        //Facebook login
    }else if(isset($_GET["login"]) && $_GET["login"] === "facebook"){   
        $user_data = $_SESSION["temp"];
        $name = $user_data[0];
        $email = $user_data[1];
        $password = "facebook";

        //Preveri ce je uporabnik ze registriran
        $stmt = $link->prepare("SELECT u.id, t.user_type, u.password FROM users u INNER JOIN types t ON u.type_id=t.id WHERE (email = ?)");
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = mysqli_fetch_array($result);

        if(mysqli_num_rows($result) > 0)
        {
            if($row["password"] == "facebook"){
                $_SESSION["user_id"] = $row["id"];
                $_SESSION['user_type'] = $row['user_type'];        
                unset($_SESSION["error"]);
                unset($_SESSION["temp"]);
                header("Location: index.php");
            }else{
                $_SESSION['error'] = "This email already exists";
                header("Location: login.php");
            }
            
        }else{
            $date = date("Y-m-d");
            $stmt = $link->prepare("INSERT INTO users (username, email, password, joined) VALUES (?,?,?,?);");
            $stmt->bind_param("ssss", $name, $email, $password, $date);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $stmt = $link->prepare("SELECT id FROM users WHERE (email=?)");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = mysqli_fetch_array($result);
            $_SESSION["user_id"] = $row["id"];
            unset($_SESSION["error"]); 
            unset($_SESSION["temp"]);
            header("Location: index.php");

        }

    }else if(isset($_GET["login"]) && $_GET["login"] === "google"){   
        $user_data = $_SESSION["temp"];
        $name = $user_data[0];
        $email = $user_data[1];
        $password = "google";

        //Preveri ce je uporabnik ze registriran
        $stmt = $link->prepare("SELECT u.id, t.user_type, u.password FROM users u INNER JOIN types t ON u.type_id=t.id WHERE (email = ?)");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = mysqli_fetch_array($result);

        if(mysqli_num_rows($result) > 0)
        {
            if($row["password"] == "google"){
                $_SESSION["user_id"] = $row["id"];
                $_SESSION['user_type'] = $row['user_type'];        
                unset($_SESSION["temp"]);
                unset($_SESSION["error"]);
                header("Location: index.php");
            }else{
                $_SESSION['error'] = "This email already exists";
                header("Location: login.php");
            }

        }else{
            $date = date("Y-m-d");
            $stmt = $link->prepare("INSERT INTO users (username, email, password, joined) VALUES (?,?,?,?);");
            $stmt->bind_param("ssss", $name, $email, $password, $date);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $stmt = $link->prepare("SELECT id FROM users WHERE (email=?)");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = mysqli_fetch_array($result);
            $_SESSION["user_id"] = $row["id"];
            unset($_SESSION["error"]);
            unset($_SESSION["temp"]);
            header("Location: index.php");
        }
        // Preveri ce je user follow-u osebi
    }else if(isset($_GET["action"]) && $_GET["action"] === "follow"){
        // Preveri ce je id stevilka
        if (filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT)){
            $friend_id = filter_input(INPUT_GET, "id");

            $stmt = $link->prepare("SELECT id FROM friends WHERE (user_id=?) AND (friend_id=?);");
            $stmt->bind_param('ii', $_SESSION["user_id"], $friend_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = mysqli_fetch_array($result);

            // Preveri ce ze imata povezavo

            if(mysqli_num_rows($result) == 0){
                $stmt = $link->prepare("INSERT INTO friends (user_id, friend_id) VALUES (?,?)");
                $stmt->bind_param('ii', $_SESSION["user_id"], $friend_id);
                $stmt->execute();
            }

            header("Location: index.php");

        } else {
            header("Location: index.php");
        }

        //Preveri ce je bil kliknjen unfollow
    }else if(isset($_GET["action"]) && $_GET["action"] === "unfollow"){
        // Preveri ce je id stevilka
        if (filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT)){
            $friend_id = filter_input(INPUT_GET, "id");

            $stmt = $link->prepare("SELECT id FROM friends WHERE (user_id=?) AND (friend_id=?);");
            $stmt->bind_param('ii', $_SESSION["user_id"], $friend_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = mysqli_fetch_array($result);

            // Preveri ce ze imata povezavo
            if(mysqli_num_rows($result) > 0){
                $stmt = $link->prepare("DELETE FROM friends WHERE id=?;");
                $stmt->bind_param('i', $row["id"]);
                $stmt->execute();
            }
           header("Location: index.php");

        }else{
            header("Location: index.php");
        }

    }else if(isset($_GET["logout"]) && $_GET["logout"] === true){
        unset($_SESSION["user_id"]);
        session_destroy();
        header("Location: login.php");
    }else {
        header("Location: login.php");
    }
        
