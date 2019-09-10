<?php
    session_start();
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
                    <li>More</li>
                </ul>
            </div>
            <div class="message">
                <h2>Home</h2>
                <div class="avatar-profile"><img src="./img/avatar.png" alt="./img/avatar.png"></div>
                <form method="POST" action="./tweets.php" enctype="multipart/form-data">
                    <input type="text" name="username" placeholder="Username">
                    <textarea placeholder="Bio" class="text" name="bio"></textarea>
                    <input type="text" name="location" placeholder="Location">
                    <input type="date" name="born" max=""><!-- samozeleznik-->
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