<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {
    if (isset($_POST['id'], $_POST['batch_name'], $_POST['start_date'], $_POST['completion_date'], $_POST['status'])) {
        include "../DB_connection.php";
        include "Model/Batch.php";

        $id = $_POST['id'];
        $batch_name = htmlspecialchars(trim($_POST['batch_name']));
        $description = htmlspecialchars(trim($_POST['description']));
        $start_date = $_POST['start_date'];
        $completion_date = $_POST['completion_date'];
        $status = $_POST['status'];

        if (empty($batch_name) || empty($start_date) || empty($completion_date)) {
            $em = "Batch Name and dates are required.";
            header("Location: ../edit_batch.php?error=$em&id=$id");
            exit();
        }

        try {
            update_batch($conn, [$batch_name, $description, $start_date, $completion_date, $status, $id]);
            $sm = "Batch updated successfully!";
            header("Location: ../edit_batch.php?success=$sm&id=$id");
            exit();
        } catch (PDOException $e) {
            $em = "Database error: Could not update batch.";
            header("Location: ../edit_batch.php?error=$em&id=$id");
            exit();
        }
    } else {
        $em = "All fields are required.";
        header("Location: ../edit_batch.php?error=$em" . (isset($_POST['id']) ? '&id='.$_POST['id'] : ''));
        exit();
    }
} else {
    $em = "First login";
    header("Location: ../login.php?error=$em");
    exit();
}