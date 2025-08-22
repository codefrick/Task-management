<?php
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    if (isset($_GET['user_id'], $_GET['batch_id'])) {
        include "../DB_connection.php";
        include "Model/User.php";
        
        $user_id = $_GET['user_id'];
        $batch_id = $_GET['batch_id'];

        remove_trainee_from_batch($conn, $user_id);
        header("Location: ../view_batch_trainees.php?batch_id=$batch_id&success=Trainee removed successfully.");
        exit();
    }
}
header("Location: ../login.php");
exit();