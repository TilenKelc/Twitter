<?php
    require_once 'vendor/autoload.php';
    session_start();
    
    // init configuration
    $clientID = '622226021483-gu8duueds3hoal4anv9dq7busec1bf5d.apps.googleusercontent.com';
    $clientSecret = 'jYg2DmZUjm6LvIzEJp_lZ0Q5';
    $redirectUri = 'https://tilenkelc.si/twitter/google-callback.php';
    
    // create Client Request to access Google API
    $client = new Google_Client();
    $client->setClientId($clientID);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri($redirectUri);
    $client->addScope("email");
    $client->addScope("profile");
    
    // authenticate code from Google OAuth Flow
    if (isset($_GET['code'])) {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token['access_token']);
        
        // get profile info
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        $email =  $google_account_info->email;
        $name =  $google_account_info->name;

        $user_data = array($name, $email);
        $_SESSION["temp"] = $user_data;

        header("Location: user.php?login=google");
    
    // now you can use this profile info to create account in your website and make user logged in.
    } else {
        header("Location: login.php");
    }
?>