<?php
require '../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId("1019854340131-vp3rlflcebprgpbojeec97mb0865rtd8.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/putumayung/auth/google-auth.php");
$client->addScope("email");
$client->addScope("profile");
?>