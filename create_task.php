<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/User.php";
    include "app/Model/Batch.php"; 

    $users = get_all_users($conn);
    $batches = get_active_batches($conn);

 ?>
<!DOCTYPE html>
<html>
<head>
	<title>Create Task</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<input type="checkbox" id="checkbox">
	<?php include "inc/header.php" ?>
	<div class="body">
		<?php include "inc/nav.php" ?>
		<section class="section-1">
			<h4 class="title">Create Task </h4>
		   <form class="form-1" method="POST" action="app/add-task.php" enctype="multipart/form-data">
			    <?php if (isset($_GET['error'])) {?>
      	  	    <div class="danger" role="alert"><?=htmlspecialchars($_GET['error'])?></div>
      	        <?php } ?>
      	        <?php if (isset($_GET['success'])) {?>
      	  	    <div class="success" role="alert"><?=htmlspecialchars($_GET['success'])?></div>
      	        <?php } ?>

				<div class="input-holder">
					<label>Title</label>
					<input type="text" name="title" class="input-1" placeholder="Title" required>
				</div>
				<div class="input-holder">
					<label>Description</label>
					<textarea name="description" class="input-1" placeholder="Description" required></textarea>
				</div>
				<div class="input-holder">
					<label>Due Date</label>
					<input type="date" name="due_date" class="input-1">
				</div>
                <div class="input-holder">
                    <label>Priority</label>
                    <select name="priority" class="input-1">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div class="input-holder">
                    <label>Attach File (Optional)</label>
                    <input type="file" name="task_file" class="input-1">
                </div>

                <div class="input-holder">
					<label>Assigned to</label>
					<div class="assignment-list">
                        <p class="assignment-group-title">Batches</p>
                        <?php if ($batches != 0): foreach ($batches as $batch): ?>
                            <label class="assignment-item">
                                <input type="checkbox" name="assignments[]" value="batch_<?=$batch['id']?>">
                                Entire Batch: <?=htmlspecialchars($batch['batch_name'])?>
                            </label>
                        <?php endforeach; else: ?>
                            <p><i>No active batches found.</i></p>
                        <?php endif; ?>

                        <p class="assignment-group-title" style="margin-top: 15px;">Individual Trainees</p>
                        <?php if ($users != 0): foreach ($users as $user): ?>
                            <label class="assignment-item">
                                <input type="checkbox" name="assignments[]" value="trainee_<?=$user['id']?>">
                                <?=htmlspecialchars($user['full_name'])?>
                            </label>
                        <?php endforeach; else: ?>
                            <p><i>No trainees found.</i></p>
                        <?php endif; ?>
                    </div>
				</div>
                <button type="submit" class="edit-btn">Create Task</button>
			</form>
		</section>
	</div>
</body>
</html>
<?php } else {
   $em = "First login";
   header("Location: login.php?error=$em");
   exit();
}
 ?>