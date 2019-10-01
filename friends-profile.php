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
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body>
        <div class="main">
            <div class="nav">
                <img src="./img/logo.jpg" alt="Logo" class="image">
                <i class="fa fa-bars" onclick="menuChange()"></i>
                <ul>
                    <li  onclick="location.href='index.php'">Home</li>
                    <li onclick="location.href='followers-list.php'">Lists</li>
                    <li onclick="location.href='profile.php'" class="active">Profile</li>
                    <?php
                        if($_SESSION["user_type"] == "Administrator"){
                            echo "<li onclick=location.href='admin-page.php'>Admin page</li>";
                        }
                    ?>
                    <li onclick="location.href='user.php?logout=true'">Logout</li>
                </ul>
            </div>
            <div class="message">
                <h2>Profile</h2>
                <form method="" action="" class="profile">
                    <?php
                        $friend_id;
                        // Izbere uporabnikove podatke
                        if (filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT)){
                            $friend_id = filter_input(INPUT_GET, "id");
                            
                        }else{
                            header("Location: index.php");
                        }
                        $sql = "SELECT * FROM users WHERE (id = $friend_id);";
                        $result = $link->query($sql);
                        while($row = $result->fetch_assoc()) {
                            if(isset($row["avatar"])){
                                echo "<div class='avatar-profile'><img src='./uploads-profile/". $row["avatar"] ."' alt='./img/avatar.png'></div>";
                            }else{
                                echo "<div class='avatar-profile'><img src='./img/avatar.png' alt='./img/avatar.png'></div>";
                            }
                            echo "<input type='text' name='username' placeholder='Username' value='". $row["username"] ."' disabled><br>";
                            echo "<textarea placeholder='Bio' class='text' name='bio' disabled>". $row["bio"] ."</textarea><br>";
                            echo "<input type='text' name='location' placeholder='Location' value='". $row["location"] ."' disabled><br>";
                            echo "<input type='date' name='born' value='". $row["born"] ."' disabled><br>";
                        }
                    ?>
                </form>
                <?php
                        $id = $_SESSION["user_id"];
                        $sql = "SELECT t.id, t.picture, t.text, t.likes, t.time, t.like_id, u.username, u.avatar, u.id as user_id FROM tweets t INNER JOIN users u ON t.user_id = u.id WHERE t.user_id = $friend_id";
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