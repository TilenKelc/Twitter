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
        <link rel="icon" href="./img/logo.ico" />
        <meta charset="utf-8">
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
                    <li class="active" onclick="location.href='index.php'">Home</li>
                    <li onclick="location.href='followers-list.php'">Lists</li>
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
                <h2>Home <form method="GET" action="index.php?hash=hash" enctype="multipart/form-data" class="search">
                    <input type="text" name="hash" placeholder="Search hashteg">
                </form></h2>
                <?php
                    $id =$_SESSION["user_id"];
                    $sql = "SELECT avatar FROM users WHERE (id = $id);";
                    $result = $link->query($sql);
                    $row = $result->fetch_assoc();
                    if(isset($row["avatar"])){
                        echo "<div class='avatar'><img src='./uploads-profile/". $row["avatar"] ."' alt='./img/avatar.png'></div>";
                    }else{
                        echo "<div class='avatar'><img src='./img/avatar.png' alt='./img/avatar.png'></div>";
                    }
                ?>
                <form method="POST" action="./tweets.php" enctype="multipart/form-data">
                    <textarea placeholder="What is going on?" class="text" name="tweet" maxlength="280"></textarea>
                    <div class='upload-image'>
                        <div class="upload-btn-wrapper">
                            <button class="btn">Image</button>
                            <input type="file" name="file">
                        </div>
                    </div>
                    <input type="submit" name="button" value="TWEET">
                </form>
                <?php
                    $id = $_SESSION["user_id"];
                    if(isset($_GET["hash"])){
                        $hash = $_GET["hash"];
                        if($hash[0] === '#'){
                            $hash = $hash;
                        }else{
                            $hash = '#'.$hash;
                        }
                        $sql = "SELECT t.id, t.picture, t.text, t.likes, t.time, t.like_id, u.username, u.avatar, u.id as user_id FROM tweets t INNER JOIN users u ON t.user_id = u.id WHERE (t.text LIKE '%".$hash."%') ORDER BY(t.time) DESC";
                    }else{
                        $sql = "SELECT t.id, t.picture, t.text, t.likes, t.time, t.like_id, u.username, u.avatar, u.id as user_id FROM tweets t INNER JOIN users u ON t.user_id = u.id WHERE (t.user_id = $id) OR (t.user_id IN (SELECT f.friend_id FROM friends f INNER JOIN users u ON u.id=f.user_id WHERE (f.user_id = $id))) ORDER BY(t.time) DESC";
                    }
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
                                if (strpos($row["text"], '#') !== false) {
                                    echo  "<div class='text-tweet'><pre>";
                                    $temp = false;
                                    $hashteg = "";
                                    for ($i = 0; $i < strlen($row["text"]); $i++) {
                                        if(strpos($row["text"][$i], '#') !== false){
                                            $temp = true;
                                            echo "<a href='index.php?hash=$hashteg' class='link'>#";
                                        }else if($row["text"][$i] === " " || $row["text"][$i] === null){
                                            $temp = false;
                                            echo "</a> ";
                                        }else if(strpos($row["text"][$i], '#') === false){
                                            echo $row["text"][$i];
                                        }else if($temp){
                                            echo $row["text"][$i];
                                            $hashteg = $hashteg . $row["text"][$i];
                                        }
                                    }
                                    //$raw = $row["text"];
                                    //$text = explode($raw, $hashteg);
                                    //echo $raw;//$text;
                                    //echo "<a href='index.php?hash=$hashteg'>#". $hashteg ."</a>";
                                    echo "</pre></div>";
                                }else{
                                    echo  "<div class='text-tweet'><pre>" . $row["text"] . "</pre></div>";
                                }
                            
                            echo  "<div class='like-tweet'>" . $row["likes"] . "</div>";
                                if($row["like_id"] == $_SESSION["user_id"]){
                                    echo  "<div  class='like-already'><a href='tweets.php?like=false&post_id=". $row["id"] ."'>Unlike</a></div>";
                                }else{
                                    echo  "<a href='tweets.php?like=false&post_id=". $row["id"] ."' class='like'>Like</a>";
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
            var y = document.getElementsByClassName("link");
            for (var i = 0; i < y.length; i++) {
                var mystring = y[i].innerHTML;
                mystring = mystring.replace('#','');
                y[i].href = 'index.php?hash=' + mystring;
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