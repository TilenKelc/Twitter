<?php
    require ("vendor/autoload.php");
    session_start();
    $fb = new Facebook\Facebook([
        'app_id' => '3081918591824631', // Replace {app-id} with your app id
        'app_secret' => '668af297d9ac4c5c09a8bbf119910932',
        'default_graph_version' => 'v3.2',
    ]);

    $helper = $fb->getRedirectLoginHelper();

    try {
        $accessToken = $helper->getAccessToken();   
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
    exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    if (! isset($accessToken)) {
    if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
    }
    exit;
    }
    // Dobi podatke iz facebooka
    $response = $fb->get('/me?fields=name,email',$accessToken);
    $fb_user = $response->getGraphUser();
    // Shrani jih v array
    $user_data = array($fb_user["name"], $fb_user["email"]);
    $_SESSION["temp"] = $user_data;

    header("Location: user.php?login=facebook");
