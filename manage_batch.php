<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Batch.php";
    $batches = get_all_batches($conn);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage Batches</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<input type="checkbox" id="checkbox">
	<?php include "inc/header.php" ?>
	<div class="body">
		<?php include "inc/nav.php" ?>
		<section class="section-1">
			<h4 class="title">Manage Batches <a href="create_batch.php">Add Batch</a></h4>
			<?php if (isset($_GET['success'])) {?>
      	  	<div class="success" role="alert">
			  <?=htmlspecialchars($_GET['success'])?>
			</div>
		    <?php } ?>
			<?php if ($batches != 0) { ?>
			<table class="main-table">
				<tr>
					<th>#</th>
					<th>Batch Name</th>
					<th>Start Date</th>
					<th>Completion Date</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				<?php $i=0; foreach ($batches as $batch) { ?>
				<tr>
					<td><?=++$i?></td>
					<td><?=htmlspecialchars($batch['batch_name'])?></td>
					<td><?=$batch['start_date']?></td>
					<td><?=$batch['completion_date']?></td>
					<td><?=$batch['status']?></td>
					<td>
    <a href="view_batch_trainees.php?batch_id=<?=$batch['id']?>" class="edit-btn" style="background: #5cb85c; margin-bottom: 5px;">View Trainees</a>
    <a href="edit_batch.php?id=<?=$batch['id']?>" class="edit-btn">Edit</a>
    <a href="delete_batch.php?id=<?=$batch['id']?>" class="delete-btn">Delete</a>
    <a href="download_batch_report.php?batch_id=<?=$batch['id']?>" class="edit-btn" style="background: #f0ad4e;">Download Report</a>
</td>
				</tr>
			   <?php } ?>
			</table>
		<?php } else { ?>
			<h3>No batches found. <a href="create_batch.php">Create one.</a></h3>
		<?php } ?>
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