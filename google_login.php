<?php
require 'vendor/autoload.php'; // Ensure Google Client is loaded

session_start();

$client = new Google_Client();
// $client->setClientId('549756415193-lpq84cgbrhsfbl0gdmb58a0ciu7ivbnd.apps.googleusercontent.com');
// $client->setClientSecret('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');

$client->setRedirectUri('http://localhost/BookStore/google_callback.php');
$client->addScope("email");
$client->addScope("profile");

$login_url = $client->createAuthUrl();

header("Location: $login_url");
exit();
?>
