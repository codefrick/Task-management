<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    if (!isset($_GET['batch_id'])) {
        header("Location: manage_batch.php");
        exit();
    }

    include "DB_connection.php";
    include "app/Model/Batch.php"; 

    $batch_id = $_GET['batch_id'];
    $batch = get_batch_by_id($conn, $batch_id);

    if ($batch == 0) {
        header("Location: manage_batch.php?error=Batch not found");
        exit();
    }

    // SQL query to fetch all related data
    $sql = "SELECT
                b.batch_name, b.start_date, b.completion_date, b.status AS batch_status,
                u.full_name AS trainee_name, u.username AS trainee_username, u.email AS trainee_email,
                t.title AS task_title, t.description AS task_description, t.due_date AS task_due_date, t.status AS task_status, t.priority AS task_priority,
                ts.file_path AS submission_file, ts.submitted_at, ts.review AS admin_review
            FROM
                batches b
            LEFT JOIN
                users u ON b.id = u.batch_id
            LEFT JOIN
                tasks t ON u.id = t.assigned_to
            LEFT JOIN
                task_submissions ts ON t.id = ts.task_id AND u.id = ts.user_id
            WHERE
                b.id = ?
            ORDER BY
                u.full_name, t.id, ts.submitted_at";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$batch_id]);
    $report_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set headers for CSV download
    $filename = "batch_report_" . str_replace(' ', '_', $batch['batch_name']) . "_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // Add header row to the CSV file
    fputcsv($output, [
        'Batch Name', 'Batch Start Date', 'Batch Completion Date', 'Batch Status',
        'Trainee Name', 'Trainee Username', 'Trainee Email',
        'Task Title', 'Task Description', 'Task Due Date', 'Task Status', 'Task Priority',
        'Submission File', 'Submitted At', 'Admin Review'
    ]);

    // Construct the base URL for the file links
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script_dir = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $uploads_url = $protocol . $host . $script_dir . '/uploads/';


    if ($stmt->rowCount() > 0) {
        foreach ($report_data as $row) {
            
            // **IMPROVED LOGIC HERE**
            // Check if a submission record exists (i.e., submitted_at is not null)
            if (!empty($row['submitted_at'])) {
                // Then, check if the file path is valid
                if (!empty($row['submission_file']) && $row['submission_file'] !== '0') {
                    // If valid, create the hyperlink
                    $file_url = $uploads_url . $row['submission_file'];
                    $row['submission_file'] = '=HYPERLINK("' . $file_url . '","' . $row['submission_file'] . '")';
                } else {
                    // If submission exists but file path is missing or invalid, show a clear message
                    $row['submission_file'] = 'No file submitted';
                }
            }

            fputcsv($output, $row);
        }
    } else {
         fputcsv($output, [
            $batch['batch_name'], $batch['start_date'], $batch['completion_date'], $batch['status'],
            'No trainees or task data found for this batch.'
        ]);
    }

    fclose($output);
    exit();

} else {
   $em = "First login";
   header("Location: login.php?error=$em");
   exit();
}
?>