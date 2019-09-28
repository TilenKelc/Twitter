<?php
    include_once ("./database.php");
    session_start();
    if(!isset($_SESSION["user_id"])){
        header("Location: login.php");
    }
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Twitter</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="./css/style.css">
    </head>
    <body>
        <div class="main">
            <div class="nav">
                <img src="./img/logo.jpg" alt="Logo" class="image">
                <ul>
                    <li onclick="location.href='index.php'">Home</li>
                    <li>Explore</li>
                    <li>Notifications</li>
                    <li>Messages</li>
                    <li>Bookmarks</li>
                    <li>Lists</li>
                    <li class="active" onclick="location.href='profile.php'">Profile</li>
                    <?php
                        
                        if($_SESSION["user_type"] == "Administrator"){
                            echo "<li onclick=location.href='admin-page.php'>Admin page</li>";
                        }
                    ?>
                    <li onclick="location.href='user.php?logout=true'">Logout</li>
                    <li>More</li>
                </ul>
            </div>
            <div class="message">
                <h2>Home</h2>
                <div class="avatar-profile"><img src="./img/avatar.png" alt="./img/avatar.png"></div>
                <form method="POST" action="./user.php" enctype="multipart/form-data" class="profile">
                    <?php
                        // Izbere uporabnikove podatke
                        $id =$_SESSION["user_id"];
                        $sql = "SELECT * FROM users WHERE (id = $id);";
                        $result = $link->query($sql);
                        while($row = $result->fetch_assoc()) {
                            echo "<input type='text' name='username' placeholder='Username' value='". $row["username"] ."' required>";
                            echo "<textarea placeholder='Bio' class='text' name='bio'>". $row["bio"] ."</textarea>";
                            echo "<input type='text' name='location' placeholder='Location' value='". $row["location"] ."'>";
                            echo "<input type='date' name='born' value='". $row["born"] ."'>";
                        }
                    ?>
                    <input type="submit" name="sub" value="Edit">
                </form>
                <div class="tweets">

                </div>
            </div>
            <div class="sideline">
                <div class="people">
                    <h2>Who to follow</h2>
                    <?php
                        $id = $_SESSION["user_id"];
                        $sql = "SELECT * FROM users WHERE (id != $id); ";
                        $result = mysqli_query($link, $sql);
                        while($row = mysqli_fetch_array($result))
                        {
                            echo "<div class='sideline-people'>";
                                echo "<img src='./img/avatar.png' alt='./img/avatar.png'><br>";
                                echo  "<p>" . $row["username"] . "</p>";
                            echo "</div>";
                        }
                    ?>
                    <div class="footer"></div>
                </div>  
            </div>
        </div>
        <script>
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1;
            var yyyy = today.getFullYear();
            if(dd<10){
                    dd='0'+dd
                } 
                if(mm<10){
                    mm='0'+mm
                } 

            today = yyyy+'-'+mm+'-'+dd;
            document.getElementsByName("born")[0].setAttribute("max", today);
        </script>
    </body>
</html>