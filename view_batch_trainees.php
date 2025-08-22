<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Batch.php";
    include "app/Model/User.php";

    if (!isset($_GET['batch_id'])) {
        header("Location: manage_batch.php");
        exit();
    }
    $batch_id = $_GET['batch_id'];
    $batch = get_batch_by_id($conn, $batch_id);
    $trainees_in_batch = get_users_by_batch_id($conn, $batch_id);
    $unassigned_trainees = get_unassigned_trainees($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Trainees in <?=htmlspecialchars($batch['batch_name'])?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Manage Trainees in "<?=htmlspecialchars($batch['batch_name'])?>" <a href="manage_batch.php" style="font-size: 1rem; margin-left: 1rem;">Back to Batches</a></h4>

            <?php if (isset($_GET['success'])) {?>
      	  	<div class="success" role="alert"><?=htmlspecialchars($_GET['success'])?></div>
		    <?php } ?>
            <?php if (isset($_GET['error'])) {?>
      	  	<div class="danger" role="alert"><?=htmlspecialchars($_GET['error'])?></div>
		    <?php } ?>

            <h4 class="title-2" style="margin-top: 30px;">Add Trainee to Batch</h4>
            <form action="app/add_trainee_to_batch.php" method="POST" class="form-1">
                <input type="hidden" name="batch_id" value="<?=$batch_id?>">
                <div class="input-holder">
                    <select name="user_id" class="input-1">
                        <option value="0">Select an unassigned trainee</option>
                        <?php if($unassigned_trainees != 0): foreach($unassigned_trainees as $trainee): ?>
                        <option value="<?=$trainee['id']?>"><?=htmlspecialchars($trainee['full_name'])?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <button type="submit" class="edit-btn">Add Trainee</button>
            </form>

            <h4 class="title-2" style="margin-top: 30px;">Trainees Currently in Batch</h4>
			<?php if ($trainees_in_batch != 0) { ?>
			<table class="main-table">
				<tr>
					<th>#</th>
					<th>Full Name</th>
					<th>Username</th>
					<th>Action</th>
				</tr>
				<?php $i=0; foreach ($trainees_in_batch as $trainee) { ?>
				<tr>
					<td><?=++$i?></td>
					<td><?=htmlspecialchars($trainee['full_name'])?></td>
					<td><?=htmlspecialchars($trainee['username'])?></td>
					<td>
						<a href="app/remove_trainee.php?user_id=<?=$trainee['id']?>&batch_id=<?=$batch_id?>" class="delete-btn">Remove</a>
					</td>
				</tr>
			   <?php } ?>
			</table>
			<?php } else { ?>
				<h3>This batch has no trainees.</h3>
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