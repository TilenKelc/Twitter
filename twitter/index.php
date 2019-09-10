<?php
    session_start();
    if(isset($_SESSION['error'])){
        echo ("<script LANGUAGE='JavaScript'>"
            . "window.alert('".$_SESSION['error']."')"
            . "</script>");
        unset($_SESSION['error']);
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
                    <li class="active" onclick="location.href='index.php'">Home</li>
                    <li>Explore</li>
                    <li>Notifications</li>
                    <li>Messages</li>
                    <li>Bookmarks</li>
                    <li>Lists</li>
                    <li onclick="location.href='profile.php'">Profile</li>
                    <li>More</li>
                </ul>
            </div>
            <div class="message">
                <h2>Home</h2>
                <div class="avatar"><img src="./img/avatar.png" alt="./img/avatar.png"></div>
                <form method="POST" action="./tweets.php" enctype="multipart/form-data">
                    <textarea placeholder="What is going on?" class="text" name="tweet"></textarea>
                    <input type="file" name="file" id="file" class="inputfile">
                    <label for="file">Image</label>
                    <input type="submit" name="button" value="TWEET">
                </form>
                <div class="tweets">

                </div>
            </div>
            <div class="sideline">
                <div class="people">
                    <h2>Who to follow</h2>
                    <div></div>
                    <div class="footer"></div>
                </div>  
            </div>
        </div>
        <script>
            var timer = null;
            $('text').keydown(function(){
                clearTimeout(timer); 
                timer = setTimeout(MoreLess, 1000);
            });

            var MoreLess = function(){
                var element = document.getElementByClass("text")[0];
                if(element.clientHeight < element.scrollHeight){
                    alert("The element has a vertical scrollbar!");
                }
            }

        </script>
    </body>
</html>