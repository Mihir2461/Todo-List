<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connect.php';
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password == $confirm_password) {
        $hash_password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO `users` (username, email, password) VALUES ('$username', '$email', '$hash_password')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            header('Location: /todoList/index.php?signup=success');
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Passwords do not match!";
    }
}
?>
