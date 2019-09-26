<?php
    include_once "./database.php";
    session_start();

    if(isset($_POST["button"]) && $_POST["button"] === "TWEET"){
        $tweet;
        $picture;
        $sql;
        $id = $_SESSION["user_id"];

        // Preveri ali je samo text ali samo slika ali oboje
        if(isset($_POST["tweet"]) && $_POST["tweet"] != ""){
            $tweet = filter_input(INPUT_POST, "tweet",FILTER_SANITIZE_STRING);

        }
        if($_FILES["file"]["name"]){
            // Sliko preveri ce je pravi format, prevelika, ali pa ni slika ter jo shrani
            $target_dir = "uploads/";
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
        // Sql kombinirani stavki
        if(isset($tweet) && isset($picture)){
            $sql = "INSERT INTO tweets (user_id, text, picture) VALUES (?,?,?);";

            $stmt = $link->prepare($sql);
            $stmt->bind_param('sss', $id, $tweet, $picture);
            $stmt->execute();
            $stmt->get_result();

        }else if(isset($tweet) && !isset($picture)){
            $sql = "INSERT INTO tweets (user_id, text) VALUES (?,?);";

            $stmt = $link->prepare($sql);
            $stmt->bind_param('ss', $id, $tweet);
            $stmt->execute();
            $stmt->get_result();

        }else if(!isset($tweet) && isset($picture)){
            $sql = "INSERT INTO tweets (user_id, picture) VALUES (?,?);";

            $stmt = $link->prepare($sql);
            $stmt->bind_param('ss', $id,$picture);
            $stmt->execute();
            $stmt->get_result();
        }
        header("Location: index.php");
        //Prejme reply in ga vstavi v bazo
    }else if(isset($_POST["reply"])){
        $text = filter_input(INPUT_POST, "reply",FILTER_SANITIZE_STRING);
        $user_id = $_SESSION["user_id"];
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

        $stmt = $link->prepare("INSERT INTO replies (user_id, reply, tweet_id) VALUES (?,?,?);");
        $stmt->bind_param('isi', $user_id, $text, $id);
        $stmt->execute();
        $stmt->get_result();
        header("Location: index.php");

    }else if(isset($_GET["like"]) && $_GET["like"] == "true"){
        $user_id = $_SESSION["user_id"];
        
        // Preveri ce je id stevilka
        if (filter_input(INPUT_GET, "post_id", FILTER_VALIDATE_INT)){
            $post_id = filter_input(INPUT_GET, "post_id");

            // Pogleda trenutno st likov
            $stmt = $link->prepare("SELECT COUNT(likes) as likes FROM tweets WHERE id=?;");
            $stmt->bind_param('i', $post_id);
            $stmt->execute();
            $stmt->get_result();
            $result = $stmt->get_result();
            $row = mysqli_fetch_array($result);

            $count = $row["likes"];
            $count++;

            $stmt = $link->prepare("UPDATE tweets SET likes = ?, like_id = ? WHERE id =?");
            $stmt->bind_param('iii', $count, $user_id, $post_id);
            $stmt->execute();
            $stmt->get_result();

            header("Location: index.php");

        } else {
            header("Location: index.php");
        }

    }else{
        header("Location: index.php");
    }
?>