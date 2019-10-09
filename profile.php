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
        <link rel="icon" href="./img/logo.ico" />
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
                <form method="POST" action="./user.php" enctype="multipart/form-data" class="profile">
                    <?php
                        // Izbere uporabnikove podatke
                        $id =$_SESSION["user_id"];
                        $sql = "SELECT * FROM users WHERE (id = $id);";
                        $result = $link->query($sql);
                        while($row = $result->fetch_assoc()) {
                            if(isset($row["avatar"])){
                                echo "<div class='avatar-profile'><img src='./uploads-profile/". $row["avatar"] ."' alt='./img/avatar.png'></div>";
                            }else{
                                echo "<div class='avatar-profile'><img src='./img/avatar.png' alt='./img/avatar.png'></div>";
                            }
                                echo "<div class='upload-image-profile'>";
                                    echo "<div class='upload-btn-wrapper-profile'>";
                                        echo "<button class='btn-profile'>Image</button>";
                                        echo "<input type='file' name='file'>";
                                    echo "</div>";
                                echo "</div>";
                            echo "<input type='text' name='username' placeholder='Username' value='". $row["username"] ."' required><br>";
                            echo "<textarea placeholder='Bio' class='text' name='bio'>". $row["bio"] ."</textarea><br>";
                            echo "<input type='text' name='location' placeholder='Location' value='". $row["location"] ."'><br>";
                            echo "<input type='date' name='born' value='". $row["born"] ."'><br>";
                        }
                    ?>
                    <input type="submit" name="sub" value="Edit">
                </form>
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
                                echo "<p onclick=location.href='friends-profile.php?id=". $row["id"] ."'0>" . $row["username"] . "</p>";

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

            var form = document.getElementsByClassName("profile")[0];
            form.style.borderBottom = "none";
        </script>
        <script>
            var div = document.getElementsByClassName("message")[0];
            div.style.height = "100vh";
        </script>
    </body>
</html>