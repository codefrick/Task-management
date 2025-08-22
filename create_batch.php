<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Batch</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Create Batch <a href="manage_batch.php">Manage Batch</a></h4>
            <form class="form-1" method="POST" action="app/add_batch.php">
                <?php if (isset($_GET['error'])) { ?>
                    <div class="danger" role="alert"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php } ?>
                <?php if (isset($_GET['success'])) { ?>
                    <div class="success" role="alert"><?= htmlspecialchars($_GET['success']) ?></div>
                <?php } ?>

                <div class="input-holder">
                    <label>Batch Name</label>
                    <input type="text" name="batch_name" class="input-1" placeholder="e.g., Summer 2025 Interns" required>
                </div>
                <div class="input-holder">
                    <label>Description</label>
                    <textarea name="description" class="input-1" placeholder="A short description of the batch"></textarea>
                </div>
                <div class="input-holder">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="input-1" required>
                </div>
                <div class="input-holder">
                    <label>Completion Date</label>
                    <input type="date" name="completion_date" class="input-1" required>
                </div>
                <button type="submit" class="edit-btn">Create Batch</button>
            </form>
        </section>
    </div>
</body>
</html>
<?php 
} else { 
   $em = "First login";
   header("Location: login.php?error=$em");
   exit();
}
?>