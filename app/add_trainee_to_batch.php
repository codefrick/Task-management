<?php
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    if (isset($_POST['user_id'], $_POST['batch_id'])) {
        include "../DB_connection.php";
        include "Model/User.php";
        
        $user_id = $_POST['user_id'];
        $batch_id = $_POST['batch_id'];

        if ($user_id == 0) {
            header("Location: ../view_batch_trainees.php?batch_id=$batch_id&error=Please select a trainee.");
            exit();
        }

        add_trainee_to_batch($conn, $user_id, $batch_id);
        header("Location: ../view_batch_trainees.php?batch_id=$batch_id&success=Trainee added successfully.");
        exit();
    }
}
header("Location: ../login.php");
exit();