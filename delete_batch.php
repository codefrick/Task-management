<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    if (isset($_GET['id'])) {
        include "DB_connection.php";
        include "app/Model/Batch.php";
        
        $id = $_GET['id'];
        delete_batch($conn, $id);
        $sm = "Batch deleted successfully.";
        header("Location: manage_batch.php?success=$sm");
        exit();
    } else {
        header("Location: manage_batch.php");
        exit();
    }
} else { 
   $em = "First login";
   header("Location: login.php?error=$em");
   exit();
}
?>