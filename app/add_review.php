<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {

    if (isset($_POST['submission_id']) && isset($_POST['review'])) {
        include "../DB_connection.php";
        include "Model/Notification.php"; // Include the notification model

        $submission_id = $_POST['submission_id'];
        $review = htmlspecialchars($_POST['review']);
        $return_url = $_POST['return_url'] ?? '../tasks.php';

        try {
            // Update the review in the database
            $sql = "UPDATE task_submissions SET review = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$review, $submission_id]);

            // ## START: Create Notification for Trainee ##
            // First, get the user_id and task_id from the submission
            $sql = "SELECT user_id, task_id FROM task_submissions WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$submission_id]);
            $submission = $stmt->fetch();

            if ($submission) {
                $trainee_id = $submission['user_id'];
                $task_id = $submission['task_id'];

                // Then, get the task title for the notification message
                $sql = "SELECT title FROM tasks WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$task_id]);
                $task = $stmt->fetch();
                $task_title = $task ? $task['title'] : 'a task';

                // Create the notification
                $message = "Your submission for task '" . $task_title . "' has been reviewed.";
                $type = "Review Update";
                insert_notification($conn, [$message, $trainee_id, $type]);
            }
            // ## END: Create Notification for Trainee ##

            $sm = "Review submitted successfully!";
            header("Location: " . $return_url . "?success=" . urlencode($sm));
            exit();

        } catch (PDOException $e) {
            $em = "Database error: Could not save the review.";
            header("Location: " . $return_url . "?error=" . urlencode($em));
            exit();
        }
    } else {
        $em = "Invalid request.";
        $return_url = $_POST['return_url'] ?? '../tasks.php';
        header("Location: " . $return_url . "?error=" . urlencode($em));
        exit();
    }
} else {
    $em = "First login";
    header("Location: ../login.php?error=$em");
    exit();
}