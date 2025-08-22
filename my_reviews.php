<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'Trainee') {
    include "DB_connection.php";
    
    $user_id = $_SESSION['id'];

    $sql = "SELECT t.title, ts.review 
            FROM task_submissions ts
            JOIN tasks t ON ts.task_id = t.id
            WHERE ts.user_id = ? AND ts.review IS NOT NULL AND ts.review != ''
            ORDER BY ts.submitted_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $reviews = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>My Reviews</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">My Reviews</h4>
            
            <?php if (count($reviews) > 0) { ?>
            <table class="main-table">
                <tr>
                    <th>#</th>
                    <th>Task Title</th>
                    <th>Admin's Review</th>
                </tr>
                <?php $i=0; foreach ($reviews as $review) { ?>
                <tr>
                    <td><?=++$i?></td>
                    <td><?=htmlspecialchars($review['title'])?></td>
                    <td><?=htmlspecialchars($review['review'])?></td>
                </tr>
                <?php } ?>
            </table>
            <?php } else { ?>
                <h3>You have no reviews yet.</h3>
            <?php } ?>
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