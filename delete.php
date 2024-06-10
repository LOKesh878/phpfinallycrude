<?php
include 'connection.php';

if (isset($_GET['deleteid'])) {
    $id = $_GET['deleteid'];
    $sql = "DELETE FROM `users` WHERE id=$id"; // Corrected the backtick usage here
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // echo "Deleted successfully";
        header('location:display.php');//redirect on display page
    } else {
        die(mysqli_error($conn));
    }
}
?>
