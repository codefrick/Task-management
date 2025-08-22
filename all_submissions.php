<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {
    include "DB_connection.php";
    
    $sql = "SELECT ts.id, ts.file_path, ts.submitted_at, ts.review, u.full_name, t.title 
            FROM task_submissions ts 
            JOIN users u ON ts.user_id = u.id 
            JOIN tasks t ON ts.task_id = t.id 
            ORDER BY ts.submitted_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $submissions = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>All Submissions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">All Trainee Submissions</h4>
            
            <?php if (count($submissions) > 0) { ?>
            <table class="main-table">
                <tr>
                    <th>Task Title</th>
                    <th>Submitted By</th>
                    <th>File</th>
                    <th>Review / Comment</th>
                </tr>
                <?php foreach ($submissions as $submission) { ?>
                <tr>
                    <td><?=htmlspecialchars($submission['title'])?></td>
                    <td><?=htmlspecialchars($submission['full_name'])?></td>
                    <td>
                        <a href="uploads/<?=htmlspecialchars($submission['file_path'])?>" target="_blank">
                            <?=htmlspecialchars($submission['file_path'])?>
                        </a>
                    </td>
                    <td>
                        <form action="app/add_review.php" method="post">
                            <input type="hidden" name="submission_id" value="<?=$submission['id']?>">
                            <input type="hidden" name="return_url" value="../all_submissions.php">
                            <textarea name="review" rows="2" style="width: 100%; margin-bottom: 5px;"><?=htmlspecialchars($submission['review'] ?? '')?></textarea>
                            <button type="submit" class="edit-btn">Save Review</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <?php } else { ?>
                <h3>No submissions found.</h3>
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