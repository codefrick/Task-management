<?php 
session_start();
include "DB_connection.php";
include "app/Model/User.php";
include "app/Model/Task.php";
include "app/Model/Batch.php";

// Ensure user is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    $em = "First login";
    header("Location: login.php?error=$em");
    exit();
}

$user_id = $_SESSION['id'];
$role    = $_SESSION['role'];
$name    = isset($_SESSION['name']) ? $_SESSION['name'] : ucfirst($role);

// Default values to avoid undefined variable errors
$num_users = $num_batches = $num_task = $num_submissions = 0;
$completed = $pending = $in_progress = $overdue_task = $todaydue_task = $nodeadline_task = 0;
$num_my_task = $num_reviews = 0;

// ======================
// Dashboard Stats Setup
// ======================
if ($role == 'admin') {
    $num_users        = count_users($conn);
    $num_batches      = count_all_batches($conn);
    $num_task         = count_tasks($conn);
    $num_submissions  = count_all_submissions($conn);
    $completed        = count_completed_tasks($conn);
    $pending          = count_pending_tasks($conn);
    $in_progress      = count_in_progress_tasks($conn);
    $overdue_task     = count_tasks_overdue($conn);
    $todaydue_task    = count_tasks_due_today($conn);
    $nodeadline_task  = count_tasks_NoDeadline($conn);
}

if ($role == 'Trainee') {
    $num_my_task      = count_my_tasks($conn, $user_id);
    $num_reviews      = count_my_reviews($conn, $user_id);
    $pending          = count_my_pending_tasks($conn, $user_id);
    $in_progress      = count_my_in_progress_tasks($conn, $user_id);
    $completed        = count_my_completed_tasks($conn, $user_id);
    $overdue_task     = count_my_tasks_overdue($conn, $user_id);
    $nodeadline_task  = count_my_tasks_NoDeadline($conn, $user_id);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<input type="checkbox" id="checkbox">
<?php include "inc/header.php"; ?>
<div class="body">
    <?php include "inc/nav.php"; ?>
    <section class="section-1">
        <h3 class="title-2">Welcome <?= htmlspecialchars($name) ?>!</h3>

        <?php if ($role == 'admin') { ?>
        <div class="dashboard">
            <div class="dashboard-item"><a href="user.php"><i class="fa fa-users"></i><span><?= $num_users ?> Trainees</span></a></div>
            <div class="dashboard-item"><a href="manage_batch.php"><i class="fa fa-cubes"></i><span><?= $num_batches ?> Batches</span></a></div>
            <div class="dashboard-item"><a href="tasks.php"><i class="fa fa-tasks"></i><span><?= $num_task ?> All Tasks</span></a></div>
            <div class="dashboard-item"><a href="all_submissions.php"><i class="fa fa-upload"></i><span><?= $num_submissions ?> Submissions</span></a></div>
            <div class="dashboard-item"><a href="tasks.php?status=completed"><i class="fa fa-check-square-o"></i><span><?= $completed ?> Completed</span></a></div>
            <div class="dashboard-item"><a href="tasks.php?status=pending"><i class="fa fa-square-o"></i><span><?= $pending ?> Pending</span></a></div>
            <div class="dashboard-item"><a href="tasks.php?status=in_progress"><i class="fa fa-spinner"></i><span><?= $in_progress ?> In progress</span></a></div>
            <div class="dashboard-item"><a href="tasks.php?due_date=Overdue"><i class="fa fa-window-close-o"></i><span><?= $overdue_task ?> Overdue</span></a></div>
            <div class="dashboard-item"><a href="tasks.php?due_date=Due+Today"><i class="fa fa-exclamation-triangle"></i><span><?= $todaydue_task ?> Due Today</span></a></div>
            <div class="dashboard-item"><a href="tasks.php?due_date=No+Deadline"><i class="fa fa-clock-o"></i><span><?= $nodeadline_task ?> No Deadline</span></a></div>
            <div class="dashboard-item"><a href="notifications.php"><i class="fa fa-bell"></i><span>Notifications</span></a></div>
        </div>
        <?php } ?>

        <?php if ($role == 'Trainee') { ?>
        <div class="dashboard">
            <div class="dashboard-item"><a href="my_task.php"><i class="fa fa-tasks"></i><span><?= $num_my_task ?> My Tasks</span></a></div>
            <div class="dashboard-item"><a href="my_reviews.php"><i class="fa fa-commenting-o"></i><span><?= $num_reviews ?> Reviews</span></a></div>
            <div class="dashboard-item"><a href="my_task.php?due_date=Overdue"><i class="fa fa-window-close-o"></i><span><?= $overdue_task ?> Overdue</span></a></div>
            <div class="dashboard-item"><a href="my_task.php?due_date=No+Deadline"><i class="fa fa-clock-o"></i><span><?= $nodeadline_task ?> No Deadline</span></a></div>
            <div class="dashboard-item"><a href="my_task.php?status=pending"><i class="fa fa-square-o"></i><span><?= $pending ?> Pending</span></a></div>
            <div class="dashboard-item"><a href="my_task.php?status=in_progress"><i class="fa fa-spinner"></i><span><?= $in_progress ?> In progress</span></a></div>
            <div class="dashboard-item"><a href="my_task.php?status=completed"><i class="fa fa-check-square-o"></i><span><?= $completed ?> Completed</span></a></div>
        </div>
        <?php } ?>

    </section>
</div>
</body>
</html>