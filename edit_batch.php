<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Batch.php";
    
    if (!isset($_GET['id'])) {
    	 header("Location: manage_batch.php");
    	 exit();
    }
    $id = $_GET['id'];
    $batch = get_batch_by_id($conn, $id);

    if ($batch == 0) {
    	 header("Location: manage_batch.php");
    	 exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Edit Batch</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<input type="checkbox" id="checkbox">
	<?php include "inc/header.php" ?>
	<div class="body">
		<?php include "inc/nav.php" ?>
		<section class="section-1">
			<h4 class="title">Edit Batch <a href="manage_batch.php">Manage Batch</a></h4>
			<form class="form-1" method="POST" action="app/update_batch.php">
			    <?php if (isset($_GET['error'])) {?>
      	  	    <div class="danger" role="alert"><?=htmlspecialchars($_GET['error'])?></div>
      	        <?php } ?>
      	        <?php if (isset($_GET['success'])) {?>
      	  	    <div class="success" role="alert"><?=htmlspecialchars($_GET['success'])?></div>
      	        <?php } ?>
				<div class="input-holder">
					<label>Batch Name</label>
					<input type="text" name="batch_name" class="input-1" value="<?=htmlspecialchars($batch['batch_name'])?>" required>
				</div>
                <div class="input-holder">
					<label>Description</label>
					<textarea name="description" class="input-1"><?=htmlspecialchars($batch['description'])?></textarea>
				</div>
				<div class="input-holder">
					<label>Start Date</label>
					<input type="date" name="start_date" class="input-1" value="<?=$batch['start_date']?>" required>
				</div>
                <div class="input-holder">
					<label>Completion Date</label>
					<input type="date" name="completion_date" class="input-1" value="<?=$batch['completion_date']?>" required>
				</div>
                <div class="input-holder">
                    <label>Status</label>
                    <select name="status" class="input-1">
                        <option value="active" <?php if($batch['status'] == 'active') echo 'selected';?>>Active</option>
                        <option value="completed" <?php if($batch['status'] == 'completed') echo 'selected';?>>Completed</option>
                    </select>
                </div>
				<input type="hidden" name="id" value="<?=$batch['id']?>">
				<button type="submit" class="edit-btn">Update Batch</button>
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