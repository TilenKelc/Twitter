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
                    <li class="active" onclick="location.href='index.php'">Home</li>
                    <li>Explore</li>
                    <li>Notifications</li>
                    <li>Messages</li>
                    <li>Bookmarks</li>
                    <li>Lists</li>
                    <li onclick="location.href='profile.php'">Profile</li>
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
                <div class="avatar"><img src="./img/avatar.png" alt="./img/avatar.png"></div>
                <form method="POST" action="./tweets.php" enctype="multipart/form-data">
                    <textarea placeholder="What is going on?" class="text" name="tweet"></textarea>
                    <div class='upload-image'>
                        <div class="upload-btn-wrapper">
                            <button class="btn">Image</button>
                            <input type="file" name="file">
                        </div>
                    <!--<input type="file" name="file">-->
                    </div>
                    <input type="submit" name="button" value="TWEET">
                </form>
                <?php
                    $id = $_SESSION["user_id"];
                    $sql = "SELECT t.id, t.picture, t.text, t.likes, t.time, t.like_id, u.username, u.avatar FROM tweets t INNER JOIN users u ON t.user_id = u.id";
                    $result = mysqli_query($link, $sql);
                    $count = 0;
                    while($row = mysqli_fetch_array($result))
                    {
                        echo "<div class='tweets'>";
                            echo "<img src='./img/avatar.png' alt='./img/avatar.png' class='profile-tweet'>";
                            echo  "<div class='time'>" . $row["time"] . "</div>";
                            echo  "<div class='username-tweet'>" . $row["username"] . "</div>";
                            echo  "<div class='text-tweet'>" . $row["text"] . "</div>";
                            echo  "<div class='like-tweet'>" . $row["likes"] . "</div>";
                                if($row["like_id"] == $_SESSION["user_id"]){
                                    echo  "<div onclick=location.href='tweets.php?like=false&post_id=". $row["id"] ."' class='like-already'>Like</div>";
                                }else{
                                    echo  "<div class='like' onclick=location.href='tweets.php?like=true&post_id=". $row["id"] ."'>Like</div>";
                                }
                            if($row["picture"]){
                                echo  "<img src='./uploads/". $row["picture"] ."' alt='' class='image-tweet'>";   
                            }
                            echo "<a onclick='Comment(". $count .")' class='comment'> Comment</a>";
                            echo "<form action='tweets.php?id=". $row["id"] ."' enctype='multipart/form-data' method='POST' class='form-change'>";
                                echo "<input type='text' placeholder='Your opinion' name='reply'>";
                            echo "</form>";

                            echo "<div class='replies'>";
                                $sqlReplies = "SELECT u.username, r.reply, r.date FROM tweets t INNER JOIN replies r ON r.tweet_id = t.id INNER JOIN users u ON u.id=r.user_id WHERE t.id = '". $row["id"]. "'";
                                $resultReplies = mysqli_query($link, $sqlReplies);
                                while($row = mysqli_fetch_array($resultReplies))
                                {
                                    echo "<div class='reply'>";
                                        echo "<div class='username-replie'>". $row["username"]. "</div>";
                                        echo "<div class='date-replie'>". $row["date"] ."</div>";
                                        echo "<div class='text-reply'>". $row["reply"]. "</div>";
                                    echo "</div>"; 
                                }
                            echo "</div>";
                        echo "</div>";
                        $count++;
                    }
                ?>
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
                                echo "<p>" . $row["username"] . "</p>";

                                $sqlFriends = "SELECT * FROM friends WHERE (user_id = $id) AND (friend_id = ". $row["id"] .");";
                                $resultFriends = mysqli_query($link, $sqlFriends);
                                $rowFriends = mysqli_fetch_array($resultFriends);

                                if($rowFriends["state"] == "1 Following 2" || $rowFriends["state"] == "Both"){
                                    echo "<div class='follow' onclick=location.href='user.php?id=". $row["id"] ."&action=unfollow'>Following</div>";
                                }else{
                                    $sqlFriends = "SELECT * FROM friends WHERE (friend_id = $id) AND (user_id = ". $row["id"] .");";
                                    $resultFriends = mysqli_query($link, $sqlFriends);
                                    $rowFriends = mysqli_fetch_array($resultFriends);

                                    if($rowFriends["state"] == "Both"){
                                        echo "<div class='follow' onclick=location.href='user.php?id=". $row["id"] ."&action=unfollow'>Following</div>";
                                    }else{
                                        echo "<div class='follow' onclick=location.href='user.php?id=". $row["id"] ."&action=follow'>Follow</div>";
                                    }
                                }
                            echo "</div>";
                        }
                    ?>
                    <div class="footer"></div>
                </div>  
            </div>
        </div>
        <script>
            var Comment = function(count){
                var text = document.getElementsByClassName("text-tweet")[count];
                var check = document.getElementsByClassName("form-change")[count];
                var replies = document.getElementsByClassName("replies")[count];
                if(check.style.display == 'block'){
                    replies.style.display = "none";
                    text.style.marginBottom = "20px";
                    check.style.display = "none";
                }else{
                    replies.style.display = "block";
                    text.style.marginBottom = "5px";
                    check.style.display = "block";
                }
            }
        </script>
    </body>
</html>