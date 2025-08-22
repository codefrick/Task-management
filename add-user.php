<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    // You will need to create this model file in the next steps
    include "app/Model/Batch.php"; 
    $batches = get_active_batches($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Add User <a href="user.php">Users</a></h4>
            <form class="form-1" method="POST" action="app/add-user.php">
                <?php if (isset($_GET['error'])) { ?>
                    <div class="danger" role="alert">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php } ?>
                <?php if (isset($_GET['success'])) { ?>
                    <div class="success" role="alert">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php } ?>

                <div class="input-holder">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="input-1" placeholder="Full Name" required>
                </div>
                <div class="input-holder">
                    <label>Username</label>
                    <input type="text" name="user_name" class="input-1" placeholder="Username" required>
                </div>
                <div class="input-holder">
                    <label>Email</label>
                    <input type="email" name="email" class="input-1" placeholder="Email Address" required>
                </div>
                <div class="input-holder">
                    <label>Password</label>
                    <input type="password" name="password" class="input-1" placeholder="Password" required>
                </div>
                <div class="input-holder">
                    <label>Role</label>
                    <select name="role" class="input-1">
                        <option value="Trainee">Trainee</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="input-holder">
                    <label>Assign to Batch (Optional)</label>
                    <select name="batch_id" class="input-1">
                        <option value="0">No Batch</option>
                        <?php if ($batches != 0): foreach ($batches as $batch): ?>
                            <option value="<?=$batch['id']?>"><?=htmlspecialchars($batch['batch_name'])?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <button type="submit" class="edit-btn">Add User</button>
            </form>
        </section>
    </div>
    <script type="text/javascript">
        var active = document.querySelector("#navList li:nth-child(2)");
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