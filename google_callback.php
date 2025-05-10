<?php
require 'vendor/autoload.php';
include "db.php";

session_start();

$client = new Google_Client();
// $client->setClientId('549756415193-lpq84cgbrhsfbl0gdmb58a0ciu7ivbnd.apps.googleusercontent.com');
// $client->setClientSecret('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');

$client->setRedirectUri('http://localhost/BookStore/google_callback.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // ✅ Check for token errors
    if (isset($token['error'])) {
        echo "Error fetching token: " . htmlspecialchars($token['error']);
        exit();
    }

    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $google_user = $oauth->userinfo->get();

    $google_id = $google_user->id;
    $name = $google_user->name;
    $email = $google_user->email;

    // ✅ Check if user already exists in database
    $stmt = $conn->prepare("SELECT id, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION["user_id"] = $row["id"];
        $_SESSION["user_name"] = $name;
        $_SESSION["email"] = $email;
        $_SESSION["role"] = $row["role"];
    } else {
        // ✅ Insert new user with Google
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, is_verified, role) VALUES (?, ?, '', 1, 'user')");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();

        $_SESSION["user_id"] = $conn->insert_id;
        $_SESSION["user_name"] = $name;
        $_SESSION["email"] = $email;
        $_SESSION["role"] = "user";
    }

    header("Location: index.php");
    exit();
} else {
    echo "No authorization code received from Google.";
}
