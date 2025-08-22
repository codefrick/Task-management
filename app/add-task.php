<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';


session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {

    // CORRECTED: Checks for 'assignments' from the checkbox list instead of 'assignment_type'
    if (isset($_POST['title'], $_POST['description'], $_POST['assignments'])) {
        include "../DB_connection.php";
        include "Model/User.php";
        include "Model/Task.php";
        include "Model/Notification.php";

        function validate_input($data) {
          $data = trim($data);
          $data = stripslashes($data);
          $data = htmlspecialchars($data);
          return $data;
        }

        $title = validate_input($_POST['title']);
        $description = validate_input($_POST['description']);
        $due_date = $_POST['due_date'];
        $priority = $_POST['priority'];
        $assignments = $_POST['assignments'];

        // File upload logic
        $filePath = null;
        $uploadPath = null;
        if (isset($_FILES['task_file']) && $_FILES['task_file']['error'] == 0) {
            $target_dir = "../upload_admin_create_task/";
            $fileName = basename($_FILES["task_file"]["name"]);
            $uniqueFileName = time() . '-' . $fileName;
            $uploadPath = $target_dir . $uniqueFileName;
            if (move_uploaded_file($_FILES["task_file"]["tmp_name"], $uploadPath)) {
                $filePath = $uniqueFileName;
            }
        }
        
        $final_trainee_ids = [];
        foreach ($assignments as $assignment) {
            if (strpos($assignment, 'batch_') === 0) {
                $batch_id = substr($assignment, 6);
                $trainees_in_batch = get_users_by_batch_id($conn, $batch_id);
                if ($trainees_in_batch != 0) {
                    foreach ($trainees_in_batch as $trainee) {
                        $final_trainee_ids[] = $trainee['id'];
                    }
                }
            } elseif (strpos($assignment, 'trainee_') === 0) {
                $trainee_id = substr($assignment, 8);
                $final_trainee_ids[] = $trainee_id;
            }
        }

        $final_trainee_ids = array_unique($final_trainee_ids);

        if (empty($final_trainee_ids)) {
            header("Location: ../create_task.php?error=No valid trainees selected.");
            exit();
        }

        foreach($final_trainee_ids as $trainee_id) {
            $user = get_user_by_id($conn, $trainee_id);
            if ($user) {
                $task_batch_id = $user['batch_id'] ? $user['batch_id'] : null;

                $data = [$title, $description, $trainee_id, $task_batch_id, $due_date, $priority, $filePath];
                $sql = "INSERT INTO tasks (title, description, assigned_to, batch_id, due_date, priority, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute($data);

                $notif_data = ["'$title' has been assigned to you.", $user['id'], 'New Task Assigned'];
                insert_notification($conn, $notif_data);

                if (!empty($user['email'])) {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'shivam.jumboking@gmail.com';   // ✅ Your Gmail
                        $mail->Password   = 'lwaf yxkv meor hvlo';          // ✅ Your Gmail App Password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port       = 465;
                        $mail->setFrom('from@example.com', 'Task Pro');
                        $mail->addAddress($user['email'], $user['full_name']);
                        if ($uploadPath) $mail->addAttachment($uploadPath);
                        $mail->isHTML(true);
                        $mail->Subject = 'New Task Assigned: ' . $title;
                        $mail->Body    = "Hello " . $user['full_name'] . ",<br><br>A new task has been assigned to you: <b>$title</b>.";
                        $mail->send();
                    } catch (Exception $e) {
                        // Fail silently
                    }
                }
            }
        }

        $sm = "Task(s) created successfully!";
        header("Location: ../create_task.php?success=$sm");
        exit();

    } else {
       $em = "Title, Description, and at least one assignment are required";
       header("Location: ../create_task.php?error=$em");
       exit();
    }
} else { 
   $em = "First login";
   header("Location: ../login.php?error=$em");
   exit();
}



