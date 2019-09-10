<?php
    include_once "./database.php";
    session_start();

    if(isset($_POST["button"]) && $_POST["button"] === "TWEET"){
        if(isset($_POST["tweet"])){
            $tweet = filter_input(trim(INPUT_POST, "username",FILTER_SANITIZE_STRING));
        }else if(isset($_POST["file"])){
            $picture = filter_input($_POST["file"]);
        }

        // Upload datoteke
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
                    $stmt = $link->prepare("INSERT INTO tweets () "
                                    . "VALUES ((SELECT id FROM stanje WHERE (naziv = ? )),"
                                    . "?,?,?,?,?,?,(SELECT id FROM pasme WHERE (ime = ?)));");
                    $stmt->bind_param('ssssssss', $stanje, $uporabnik, $ime, $rojstvo, $spol, $velikost, $opis, $pasma);
                    $stmt->execute();
                    $stmt->get_result();
                    
                    $sql = $link->prepare("INSERT INTO slike (zival_id,ime,url) "
                                    . "VALUES ((SELECT id FROM zivali WHERE (ime = ?) AND (datum_roj = ?) AND (spol = ?) AND (velikost = ?) AND (opis = ?)),?,?);");
                    $name = $_FILES["slika"]["name"];
                    $sql->bind_param('sssssss', $ime, $rojstvo, $spol, $velikost, $opis, $name, $name);
                    $sql->execute();
                    $sql->get_result();

                    unset($_SESSION["error"]);

                    header("Location:index.php");
            } else {
                    header("Location:dodaj.php");
                }
        }

    }
?>