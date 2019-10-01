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
                    <div class='number-of-followers' onclick="Followers('following')">
                        <?php
                            $id = $_SESSION["user_id"];
                            $sql = "SELECT COUNT(id) as count FROM friends WHERE (user_id = $id);";
                            $result = mysqli_query($link, $sql);
                            $row = mysqli_fetch_array($result);
                            echo "Following: ". $row["count"];
                        ?>
                    </div>
                    <div class='number-of-following' onclick="Followers('followers')">
                        <?php
                            $id = $_SESSION["user_id"];
                            $sql = "SELECT COUNT(id) as count FROM friends WHERE (friend_id = $id);";
                            $result = mysqli_query($link, $sql);
                            $row = mysqli_fetch_array($result);
                            echo "Followers: ". $row["count"];
                        ?>
                    </div>
                    <div class='number-of-likes' onclick="Followers('likes')">
                        <?php
                            $id = $_SESSION["user_id"];
                            $sql = "SELECT COUNT(t.id) as count FROM tweets t INNER JOIN users u ON u.id = t.user_id WHERE (t.like_id = $id);";
                            $result = mysqli_query($link, $sql);
                            $row = mysqli_fetch_array($result);
                            echo "Likes: ". $row["count"];
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
                <div class="people-list-likes">
                    <?php
                        $id = $_SESSION["user_id"];
                        $sql = "SELECT t.id, t.picture, t.text, t.likes, t.time, t.like_id, u.username, u.avatar, u.id as user_id FROM tweets t INNER JOIN users u ON t.user_id = u.id WHERE t.like_id = $id";
                        $result = mysqli_query($link, $sql);
                        $count = 0;
                        while($row = mysqli_fetch_array($result))
                        {
                            echo "<div class='tweets'>";
                                if(isset($row["avatar"])){
                                    echo "<img src='./uploads-profile/". $row["avatar"] ."' class='profile-tweet' onclick=location.href='friends-profile.php?id=". $row["user_id"] ."'>";
                                }else{
                                    echo "<img src='./img/avatar.png' class='profile-tweet' onclick=location.href='friends-profile.php?id=". $row["user_id"] ."'>";
                                }
                                echo  "<div class='time'>" . $row["time"] . "</div>";
                                echo  "<div class='username-tweet' onclick=location.href='friends-profile.php?id=". $row["user_id"] ."'>" . $row["username"] . "</div>";
                                echo  "<div class='text-tweet'>" . $row["text"] . "</div>";
                                echo  "<div class='like-tweet'>" . $row["likes"] . "</div>";
                                    if($row["like_id"] == $_SESSION["user_id"]){
                                        echo  "<div onclick=location.href='tweets.php?like=false&post_id=". $row["id"] ."' class='like-already'>Unlike</div>";
                                    }else{
                                        echo  "<div class='like' onclick=location.href='tweets.php?like=true&post_id=". $row["id"] ."'>Like</div>";
                                    }
                                if($row["picture"]){
                                    echo  "<img src='./uploads/". $row["picture"] ."' alt='' class='image-tweet'>";   
                                }
                                echo "<a onclick=location.href='tweets.php?action=report&id=". $row["id"] ."' class='report'>Report</a>";
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
                        if($count < 4){
                            ?>
                                <script>
                                    var div = document.getElementsByClassName("message")[0];
                                    div.style.height = "100vh";
                                </script>
                            <?php
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
                                echo "<p onclick=location.href='friends-profile.php?id=". $row["id"] ."'>" . $row["username"] . "</p>";

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
                var likes = document.getElementsByClassName("people-list-likes")[0];
                if(check === 'followers'){
                    people.style.display = "none";
                    strangers.style.display = "block";
                    likes.style.display = "none";
                }else if(check === 'following'){
                    people.style.display = "block";
                    strangers.style.display = "none";
                    likes.style.display = "none";
                }else{
                    people.style.display = "none";
                    strangers.style.display = "none";
                    likes.style.display = "block";
                }
            }

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