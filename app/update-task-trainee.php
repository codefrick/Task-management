<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'Trainee') {

    if (isset($_POST['id']) && isset($_POST['status'])) {
        include "../DB_connection.php";
        include "Model/Task.php";

        function validate_input($data) {
          $data = trim($data);
          $data = stripslashes($data);
          $data = htmlspecialchars($data);
          return $data;
        }

        $status = validate_input($_POST['status']);
        $id = validate_input($_POST['id']);

        if (empty($status)) {
            $em = "Status is required";
            header("Location: ../edit-task-trainee.php?error=$em&id=$id");
            exit();
        } else {
           $data = array($status, $id);
           update_task_status($conn, $data);

           $sm = "Task updated successfully";
            header("Location: ../edit-task-trainee.php?success=$sm&id=$id");
            exit();
        }
    } else {
       $em = "Unknown error occurred";
       header("Location: ../edit-task-trainee.php?error=$em");
       exit();
    }

} else { 
   $em = "First login";
   header("Location: ../login.php?error=$em");
   exit();
}