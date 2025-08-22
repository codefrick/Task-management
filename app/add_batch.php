<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {
    if (isset($_POST['batch_name'], $_POST['start_date'], $_POST['completion_date'])) {
        include "../DB_connection.php";

        $batch_name = htmlspecialchars(trim($_POST['batch_name']));
        $description = htmlspecialchars(trim($_POST['description']));
        $start_date = $_POST['start_date'];
        $completion_date = $_POST['completion_date'];

        if (empty($batch_name) || empty($start_date) || empty($completion_date)) {
            $em = "Batch Name, Start Date, and Completion Date are required.";
            header("Location: ../create_batch.php?error=$em");
            exit();
        }

        try {
            $sql = "INSERT INTO batches (batch_name, description, start_date, completion_date) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$batch_name, $description, $start_date, $completion_date]);

            $sm = "Batch created successfully!";
            header("Location: ../manage_batch.php?success=$sm");
            exit();
        } catch (PDOException $e) {
            $em = "Database error: Could not create batch.";
            header("Location: ../create_batch.php?error=$em");
            exit();
        }
    } else {
        $em = "All fields are required.";
        header("Location: ../create_batch.php?error=$em");
        exit();
    }
} else {
    $em = "First login";
    header("Location: ../login.php?error=$em");
    exit();
}