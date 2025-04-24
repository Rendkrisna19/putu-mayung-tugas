<?php
require '../config/gogle-config.php';
session_start();

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    include("../config/config.php");

    $email = $userInfo->email;
    $name = $userInfo->name;
    $google_id = $userInfo->id;

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) { 
        $insert = "INSERT INTO users (email, name, google_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("sss", $email, $name, $google_id);
        $stmt->execute();
    }

    $_SESSION["user"] = $email;
    header("Location: ../dashboard/index.php");
    exit();
} else {
    header("Location: " . $client->createAuthUrl());
}
?>