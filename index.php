<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) ) {

	 include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";
    include "app/Model/Batch.php"; // Include the Batch model

	if ($_SESSION['role'] == "admin") {
		  $todaydue_task = count_tasks_due_today($conn);
	     $overdue_task = count_tasks_overdue($conn);
	     $nodeadline_task = count_tasks_NoDeadline($conn);
	     $num_task = count_tasks($conn);
	     $num_users = count_users($conn);
	     $pending = count_pending_tasks($conn);
	     $in_progress = count_in_progress_tasks($conn);
	     $completed = count_completed_tasks($conn);
         $num_submissions = count_all_submissions($conn);
         $num_batches = count_all_batches($conn); // Gets the count for the new tab

	}else {
        $user_id = $_SESSION['id'];
        $num_my_task = count_my_tasks($conn, $user_id);
        $overdue_task = count_my_tasks_overdue($conn, $user_id);
        $nodeadline_task = count_my_tasks_NoDeadline($conn, $user_id);
        $pending = count_my_pending_tasks($conn, $user_id);
	     $in_progress = count_my_in_progress_tasks($conn, $user_id);
	     $completed = count_my_completed_tasks($conn, $user_id);
         $num_reviews = count_my_reviews($conn, $user_id);
	}
 ?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-item a { text-decoration: none; color: #fff; display: block; padding: 30px 5px; }
        .dashboard-item a i, .dashboard-item a span { display: block; }
        .dashboard-item { padding: 0; }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <?php if ($_SESSION['role'] == "admin") { ?>
                <div class="dashboard">
                    <div class="dashboard-item">
                        <a href="user.php"><i class="fa fa-users"></i><span><?=$num_users?> Trainee</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="manage_batch.php"><i class="fa fa-cubes"></i><span><?=$num_batches?> Batches</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="tasks.php"><i class="fa fa-tasks"></i><span><?=$num_task?> All Tasks</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="all_submissions.php"><i class="fa fa-upload"></i><span><?=$num_submissions?> Submissions</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="tasks.php"><i class="fa fa-check-square-o"></i><span><?=$completed?> Completed</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="tasks.php"><i class="fa fa-square-o"></i><span><?=$pending?> Pending</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="tasks.php"><i class="fa fa-spinner"></i><span><?=$in_progress?> In progress</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="tasks.php?due_date=Overdue"><i class="fa fa-window-close-o"></i><span><?=$overdue_task?> Overdue</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="tasks.php?due_date=Due+Today"><i class="fa fa-exclamation-triangle"></i><span><?=$todaydue_task?> Due Today</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="tasks.php?due_date=No+Deadline"><i class="fa fa-clock-o"></i><span><?=$nodeadline_task?> No Deadline</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="notifications.php"><i class="fa fa-bell"></i><span>Notifications</span></a>
                    </div>
                </div>
            <?php }else{ ?>
                <div class="dashboard">
                    <div class="dashboard-item">
                        <a href="my_task.php"><i class="fa fa-tasks"></i><span><?=$num_my_task?> My Tasks</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="my_reviews.php"><i class="fa fa-commenting-o"></i><span><?=$num_reviews?> Reviews</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="my_task.php"><i class="fa fa-window-close-o"></i><span><?=$overdue_task?> Overdue</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="my_task.php"><i class="fa fa-clock-o"></i><span><?=$nodeadline_task?> No Deadline</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="my_task.php?status=pending"><i class="fa fa-square-o"></i><span><?=$pending?> Pending</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="my_task.php?status=in_progress"><i class="fa fa-spinner"></i><span><?=$in_progress?> In progress</span></a>
                    </div>
                    <div class="dashboard-item">
                        <a href="my_task.php?status=completed"><i class="fa fa-check-square-o"></i><span><?=$completed?> Completed</span></a>
                    </div>
                </div>
            <?php } ?>
        </section>
    </div>

<script type="text/javascript">
    var active = document.querySelector("#navList li:nth-child(1)");
    active.classList.add("active");
</script>
</body>
</html>
<?php }else{ 
   $em = "First login";
   header("Location: login.php?error=$em");
   exit();
}
 ?>