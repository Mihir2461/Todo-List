<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connect.php';
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM `users` WHERE `username`='$username'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: /todoList/index.php');
    } else {
        echo "Invalid credentials!";
    }
}
?>
