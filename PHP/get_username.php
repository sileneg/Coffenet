<?php
session_start();

$response = array('loggedIn' => false);

if (isset($_SESSION['user_name'])) {
    $response['loggedIn'] = true;
    $response['user_name'] = $_SESSION['user_name'];
}

echo json_encode($response);
?>
