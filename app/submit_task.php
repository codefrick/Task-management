<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'Trainee') {

    if (isset($_POST['task_id']) && isset($_FILES['task_file'])) {
        include "../DB_connection.php";
        include "Model/Notification.php"; // Include the notification model

        $task_id = $_POST['task_id'];
        $user_id = $_SESSION['id'];
        $trainee_name = $_SESSION['username']; // Get trainee's name from session

        if ($_FILES['task_file']['error'] == 0) {
            $target_dir = "../uploads/"; // Using your 'uploads' folder
            $fileName = basename($_FILES["task_file"]["name"]);
            $uniqueFileName = time() . '-' . $user_id . '-' . $fileName;
            $uploadPath = $target_dir . $uniqueFileName;

            if (move_uploaded_file($_FILES["task_file"]["tmp_name"], $uploadPath)) {
                try {
                    // 1. Record the file submission
                    $sql = "INSERT INTO task_submissions (task_id, user_id, file_path) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$task_id, $user_id, $uniqueFileName]);

                    // ## START: Create Notification for Admin ##
                    // 2. Find the admin's ID
                    $admin_sql = "SELECT id FROM users WHERE role = 'admin' LIMIT 1";
                    $admin_stmt = $conn->prepare($admin_sql);
                    $admin_stmt->execute();
                    $admin = $admin_stmt->fetch();

                    if ($admin) {
                        $admin_id = $admin['id'];
                        
                        // 3. Get the task title for the notification message
                        $task_sql = "SELECT title FROM tasks WHERE id = ?";
                        $task_stmt = $conn->prepare($task_sql);
                        $task_stmt->execute([$task_id]);
                        $task = $task_stmt->fetch();
                        $task_title = $task ? $task['title'] : 'a task';

                        // 4. Create the notification message and insert it
                        $message = "$trainee_name submitted a file for task: '$task_title'.";
                        $type = "New Submission";
                        insert_notification($conn, [$message, $admin_id, $type]);
                    }
                    // ## END: Create Notification for Admin ##

                    $sm = "File submitted successfully!";
                    header("Location: ../my_task.php?success=$sm");
                    exit();
                } catch (PDOException $e) {
                    $em = "Database error: Could not record submission.";
                    header("Location: ../my_task.php?error=$em");
                    exit();
                }
            } else {
                $em = "Sorry, there was an error uploading your file.";
                header("Location: ../my_task.php?error=$em");
                exit();
            }
        } else {
            $em = "No file selected or an error occurred during upload.";
            header("Location: ../my_task.php?error=$em");
            exit();
        }
    } else {
        $em = "Invalid request.";
        header("Location: ../my_task.php?error=$em");
        exit();
    }
} else {
    $em = "First login";
    header("Location: ../login.php?error=$em");
    exit();
}
