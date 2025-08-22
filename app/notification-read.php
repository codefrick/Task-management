<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "../DB_connection.php";
    include "Model/Notification.php";

   if (isset($_GET['notification_id'])) {
       $notification_id = $_GET['notification_id'];

       // Get the notification to check its type
       $sql = "SELECT type FROM notifications WHERE id = ? AND recipient = ?";
       $stmt = $conn->prepare($sql);
       $stmt->execute([$notification_id, $_SESSION['id']]);
       $notification = $stmt->fetch();
       
       // Mark the notification as read
       notification_make_read($conn, $_SESSION['id'], $notification_id);

       // ## START: Conditional Redirect ##
       if ($notification && $notification['type'] == 'Review Update') {
           header("Location: ../my_reviews.php");
           exit();
       } else {
           // Default redirect for all other notification types
           header("Location: ../notifications.php");
           exit();
       }
       // ## END: Conditional Redirect ##

     }else {
       header("Location: ../index.php");
       exit();
     }
}else{ 
    $em = "First login";
    header("Location: ../login.php?error=$em");
    exit();
}
 ?>