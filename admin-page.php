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
        <link rel="icon" href="./img/logo.ico" />
        <link rel="stylesheet" type="text/css" href="./css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body>
        <div class="main">
            <div class="nav">
                <img src="./img/logo.jpg" alt="Logo" class="image">
                <i class="fa fa-bars" onclick="menuChange()"></i>
                <ul>
                    <li onclick="location.href='index.php'">Home</li>
                    <li onclick="location.href='followers-list.php'">Lists</li>
                    <li onclick="location.href='profile.php'">Profile</li>
                    <?php
                        
                        if($_SESSION["user_type"] == "Administrator"){
                            echo "<li onclick=location.href='admin-page.php' class='active'>Admin page</li>";
                        }
                    ?>
                    <li onclick="location.href='user.php?logout=true'">Logout</li>
                </ul>
            </div>
            <div class="message">
                <h2>Admin page</h2>
                <?php
                    $id = $_SESSION["user_id"];
                    $sql = "SELECT t.id, t.picture, t.text, t.likes, t.time, t.like_id, u.username, u.avatar FROM tweets t INNER JOIN users u ON t.user_id = u.id WHERE t.reported='1';";
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
                            echo "<a onclick=location.href='tweets.php?action=delete&id=". $row["id"] ."' class='report'>Delete</a>";
                            echo "<a onclick=location.href='tweets.php?action=pardon&id=". $row["id"] ."' class='pardon'>Pardon</a>";
                            echo "<a onclick='Comment(". $count .")' class='comment'>Comment</a>";
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
                    if(mysqli_num_rows($result) == 0){
                        echo "<div class='reports'>No reports</div>";
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
            var menuChange = function(){
                var ul = document.getElementsByTagName("ul")[0];
                if(ul.style.display == "block"){
                    ul.style.display = "none";
                }else{
                    ul.style.display = "block";
                }
            }

            function myFunction(x) {
                if (x.matches) {
                    var menuImg = document.getElementsByClassName("fa fa-bars")[0]; 
                    menuImg.style.display = "inline block";
                } else {
                    var menuImg = document.getElementsByClassName("fa fa-bars")[0]; 
                    menuImg.style.display = "none";
                }
            }

            var x = window.matchMedia("(max-width: 850px)")
            myFunction(x)
            x.addListener(myFunction)
        </script>
        <script>
            var div = document.getElementsByClassName("message")[0];
            div.style.height = "100vh";
        </script>
    </body>
</html>