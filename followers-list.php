<?php
    session_start();
    include_once "./database.php";
    if(isset($_SESSION['error'])){
        echo ("<script LANGUAGE='JavaScript'>"
            . "window.alert('".$_SESSION['error']."')"
            . "</script>");
        unset($_SESSION['error']);
    }
    if(!isset($_SESSION["user_id"])){
        header("Location: login.php");
    }
?>
<script type="text/javascript">
    if (window.location.hash === "#_=_"){
        history.replaceState 
            ? history.replaceState(null, null, window.location.href.split("#")[0])
            : window.location.hash = "";
    }
</script>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Twitter</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="./css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    <body>
        <div class="main">
            <div class="nav">
                <img src="./img/logo.jpg" alt="Logo" class="image">
                <ul>
                    <li onclick="location.href='index.php'">Home</li>
                    <li class="active" onclick="location.href='followers-list.php'">Lists</li>
                    <li onclick="location.href='profile.php'">Profile</li>
                    <?php
                        
                        if($_SESSION["user_type"] == "Administrator"){
                            echo "<li onclick=location.href='admin-page.php'>Admin page</li>";
                        }
                    ?>
                    <li onclick="location.href='user.php?logout=true'">Logout</li>
                </ul>
            </div>
            <div class="message">
                <h2>List</h2>
                <div class='numbers'>
                    <div class='number-of-followers' onclick="Followers(true)">
                        <?php
                            $id = $_SESSION["user_id"];
                            $sql = "SELECT COUNT(id) as count FROM friends WHERE (user_id = $id);";
                            $result = mysqli_query($link, $sql);
                            $row = mysqli_fetch_array($result);
                            echo "Following: ". $row["count"];
                        ?>
                    </div>
                    <div class='number-of-following' onclick="Followers(false)">
                        <?php
                            $id = $_SESSION["user_id"];
                            $sql = "SELECT COUNT(id) as count FROM friends WHERE (friend_id = $id);";
                            $result = mysqli_query($link, $sql);
                            $row = mysqli_fetch_array($result);
                            echo "Followers: ". $row["count"];
                        ?>
                    </div>
                </div>
                <div class="people-list-following">
                    <?php
                        $id = $_SESSION["user_id"];
                        $sql = "SELECT f.friend_id FROM users u INNER JOIN friends f ON f.user_id=u.id WHERE (f.state = 'Following') and (f.user_id = $id);";
                        $result = mysqli_query($link, $sql);
                        while($row = mysqli_fetch_array($result))
                        {
                            $sqlFriends = "SELECT * FROM users WHERE (id =".  $row["friend_id"] .");";
                            $resultFriends = mysqli_query($link, $sqlFriends);
                            $rowFriends = mysqli_fetch_array($resultFriends);

                            echo "<div class='sideline-people-list'>";
                                if(isset($row["avatar"])){
                                    echo "<img src='./uploads-profile/". $rowFriends["avatar"] ."' alt='./img/avatar.png' onclick=location.href='friends-profile.php?id=". $rowFriends["id"] ."'><br>";
                                }else{
                                    echo "<img src='./img/avatar.png' alt='./img/avatar.png' onclick=location.href='friends-profile.php?id=". $rowFriends["id"] ."'><br>";
                                }
                                echo "<p>" . $rowFriends["username"] . "</p>";

                                $sqlFriends = "SELECT * FROM friends WHERE (user_id =".  $id .") AND (friend_id = ". $row["friend_id"] .");";
                                $resultFriends = mysqli_query($link, $sqlFriends);

                                if(mysqli_num_rows($resultFriends) > 0){
                                    echo "<div class='follow' onclick=location.href='user.php?id=". $row["friend_id"] ."&action=unfollow'>Following</div>";
                                }else{
                                    echo "<div class='follow' onclick=location.href='user.php?id=". $row["friend_id"] ."&action=follow'>Follow</div>";
                                }
                            echo "</div>";
                        }
                    ?>
                </div>  
                <div class="people-list-followers">
                    <?php
                        $id = $_SESSION["user_id"];
                        $sql = "SELECT f.user_id FROM users u INNER JOIN friends f ON f.user_id=u.id WHERE (f.state = 'Following') and (f.friend_id = $id);";
                        $result = mysqli_query($link, $sql);
                        while($row = mysqli_fetch_array($result))
                        {
                            $sqlFriends = "SELECT * FROM users WHERE (id =".  $row["user_id"] .");";
                            $resultFriends = mysqli_query($link, $sqlFriends);
                            $rowFriends = mysqli_fetch_array($resultFriends);

                            echo "<div class='sideline-people-list'>";
                                if(isset($row["avatar"])){
                                    echo "<img src='./uploads-profile/". $rowFriends["avatar"] ."' alt='./img/avatar.png' onclick=location.href='friends-profile.php?id=". $rowFriends["id"] ."'><br>";
                                }else{
                                    echo "<img src='./img/avatar.png' alt='./img/avatar.png' onclick=location.href='friends-profile.php?id=". $rowFriends["id"] ."'><br>";
                                }
                                echo "<p>" . $rowFriends["username"] . "</p>";

                                $sqlFriends = "SELECT * FROM friends WHERE (user_id =".  $id .") AND (friend_id = ". $row["user_id"] .");";
                                $resultFriends = mysqli_query($link, $sqlFriends);

                                if(mysqli_num_rows($resultFriends) > 0){
                                    echo "<div class='follow' onclick=location.href='user.php?id=". $row["user_id"] ."&action=unfollow'>Following</div>";
                                }else{
                                    echo "<div class='follow' onclick=location.href='user.php?id=". $row["user_id"] ."&action=follow'>Follow</div>";
                                }
                            echo "</div>";
                        }
                    ?>
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
                            $sqlFriends = "SELECT * FROM friends WHERE (user_id =".  $id .") AND (friend_id = ". $row["id"] .");";
                            $resultFriends = mysqli_query($link, $sqlFriends);

                            if(mysqli_num_rows($resultFriends) == 0){
                                echo "<div class='sideline-people'>";
                                if(isset($row["avatar"])){
                                    echo "<img src='./uploads-profile/". $row["avatar"] ."' alt='./img/avatar.png' onclick=location.href='friends-profile.php?id=". $row["id"] ."'><br>";
                                }else{
                                    echo "<img src='./img/avatar.png' alt='./img/avatar.png' onclick=location.href='friends-profile.php?id=". $row["id"] ."'><br>";
                                }
                                echo "<p>" . $row["username"] . "</p>";

                                if(mysqli_num_rows($resultFriends) > 0){
                                    echo "<div class='follow' onclick=location.href='user.php?id=". $row["id"] ."&action=unfollow'>Following</div>";
                                }else{
                                    echo "<div class='follow' onclick=location.href='user.php?id=". $row["id"] ."&action=follow'>Follow</div>";
                                }
                            echo "</div>";
                            }
                        }
                    ?>
                </div>  
            </div>
        </div>
        <script>
            var Followers = function(check){
                var people = document.getElementsByClassName("people-list-following")[0];
                var strangers = document.getElementsByClassName("people-list-followers")[0];
                if(!check){
                    people.style.display = "none";
                    strangers.style.display = "block";
                }else{
                    people.style.display = "block";
                    strangers.style.display = "none";
                }
            }
        </script>
        <script>
            var div = document.getElementsByClassName("message")[0];
            div.style.height = "100vh";
        </script>
    </body>
</html>