<?php
    include_once "./database.php";
    session_start();

    if(isset($_POST["button"]) && $_POST["button"] === "TWEET"){
        $sql = "INSERT INTO tweets (user_id, ";
        $array = array();

        if(isset($_POST["tweet"])){
            $tweet = filter_input(INPUT_POST, "tweet",FILTER_SANITIZE_STRING);
            array_push($array, $tweet);
            $sql = $sql . "text";

        }else if(isset($_POST["file"])){
            if(isset($_POST["tweet"])){
                $sql = $sql . ",picture)";
            }else{
                $sql = $sql . "picture)";
            }

            $picture = filter_input($_POST["file"]);
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

                        header("Location:index.php");
                } else {
                        header("Location:dodaj.php");
                    }
            }

            array_push($array, $picture);
        }
        if(isset($_POST["tweet"]) && !isset($_POST["file"])){
            $sql = $sql . ") VALUES (?,?);";
            
        }else{
            $sql = $sql . " VALUES (?,?,?);";
        }
        echo $sql;
        array_push($array, $_SESSION["user_id"]);

        /*

        $stmt = $link->prepare($sql);
        $stmt->bind_param('sss', $array[0], $array[1]);
        $stmt->execute();
        $stmt->get_result();

        header("Location: index.php");*/
    }
?>