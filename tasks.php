<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";
    
    // ---------- Filtering Logic ----------
    $text = "All Task";
    if (isset($_GET['due_date']) && $_GET['due_date'] == "Due Today") {
        $text = "Due Today";
        $tasks = get_all_tasks_due_today($conn);
    } else if (isset($_GET['due_date']) && $_GET['due_date'] == "Overdue") {
        $text = "Overdue";
        $tasks = get_all_tasks_overdue($conn);
    } else if (isset($_GET['due_date']) && $_GET['due_date'] == "No Deadline") {
        $text = "No Deadline";
        $tasks = get_all_tasks_NoDeadline($conn);
    } else if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $text = ucfirst(str_replace('_', ' ', $status)) . " Tasks";
        $tasks = get_tasks_by_status($conn, $status);
    } else {
        $tasks = get_all_tasks($conn);
    }

    $users = get_all_users($conn);
 ?>
<!DOCTYPE html>
<html>
<head>
	<title>All Tasks</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<input type="checkbox" id="checkbox">
	<?php include "inc/header.php" ?>
	<div class="body">
		<?php include "inc/nav.php" ?>
		<section class="section-1">
			<h4 class="title-2">
				<a href="create_task.php" class="btn">Create Task</a>
				<a href="tasks.php?due_date=Due Today">Due Today</a>
				<a href="tasks.php?due_date=Overdue">Overdue</a>
				<a href="tasks.php?due_date=No Deadline">No Deadline</a>
				<a href="tasks.php">All Tasks</a>
				<!-- Optional: Add status filter buttons -->
				<a href="tasks.php?status=completed">Completed</a>
				<a href="tasks.php?status=in_progress">In Progress</a>
				<a href="tasks.php?status=pending">Pending</a>
			</h4>
			
            <h4 class="title-2"><?=$text?> (<?= is_array($tasks) ? count($tasks) : 0 ?>)</h4>
			
			<?php if (isset($_GET['success'])) { ?>
      	  	    <div class="success" role="alert">
			        <?php echo htmlspecialchars($_GET['success']); ?>
			    </div>
		    <?php } ?>
			
			<?php if ($tasks != 0) { ?>
			<table class="main-table">
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Assigned To</th>
					<th>Due Date</th>
					<th>Status</th>
                    <th>Priority</th>
                    <th>Attachment</th>
					<th>Action</th>
				</tr>
				<?php $i=0; foreach ($tasks as $task) { ?>
				<tr>
					<td><?=++$i?></td>
					<td><?=htmlspecialchars($task['title'])?></td>
					<td>
						<?php 
                            $assignee_name = "N/A";
                            foreach ($users as $user) {
                                if($user['id'] == $task['assigned_to']){
                                    $assignee_name = $user['full_name'];
                                    break;
                                }
                            }
                            echo htmlspecialchars($assignee_name);
                        ?>
	                </td>
	                <td>
                        <?php 
                            if($task['due_date'] == "0000-00-00" || $task['due_date'] == NULL) 
                                echo "No Deadline";
	                        else 
                                echo htmlspecialchars($task['due_date']);
	                    ?>
                    </td>
	                <td><?=htmlspecialchars($task['status'])?></td>
                    <td><?=htmlspecialchars($task['priority'])?></td>
                    <td>
                        <?php if (!empty($task['file_path'])): ?>
                            <a href="upload_admin_create_task/<?=htmlspecialchars($task['file_path'])?>" target="_blank">View File</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
					<td>
                        <a href="view_submissions.php?task_id=<?=$task['id']?>" class="edit-btn" style="background: #5cb85c; margin-bottom: 5px;">View Submissions</a>
						<a href="edit-task.php?id=<?=$task['id']?>" class="edit-btn">Edit</a>
						<a href="delete-task.php?id=<?=$task['id']?>" class="delete-btn">Delete</a>
					</td>
				</tr>
			   <?php } ?>
			</table>
		<?php } else { ?>
			<h3>No tasks found.</h3>
		<?php } ?>
			
		</section>
	</div>

<script type="text/javascript">
	var active = document.querySelector("#navList li:nth-child(4)");
	active.classList.add("active");
</script>
</body>
</html>
<?php 
} else { 
   $em = "First login";
   header("Location: login.php?error=$em");
   exit();
}
?>
