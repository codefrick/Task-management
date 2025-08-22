<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {
    include "DB_connection.php";
    
    if (!isset($_GET['task_id'])) {
        header("Location: tasks.php");
        exit();
    }

    $task_id = $_GET['task_id'];

    $task_sql = "SELECT title FROM tasks WHERE id = ?";
    $task_stmt = $conn->prepare($task_sql);
    $task_stmt->execute([$task_id]);
    $task = $task_stmt->fetch();
    $task_title = $task ? $task['title'] : 'Unknown Task';

    $sql = "SELECT ts.id, ts.file_path, ts.submitted_at, ts.review, u.full_name 
            FROM task_submissions ts 
            JOIN users u ON ts.user_id = u.id 
            WHERE ts.task_id = ? 
            ORDER BY ts.submitted_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$task_id]);
    $submissions = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>View Submissions for <?= htmlspecialchars($task_title) ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Submissions for "<?= htmlspecialchars($task_title) ?>" <a href="tasks.php" style="font-size: 1rem; margin-left: 1rem;">Back to All Tasks</a></h4>
            
            <?php if (count($submissions) > 0) { ?>
            <table class="main-table">
                <tr>
                    <th>Submitted By</th>
                    <th>File</th>
                    <th>Review / Comment</th>
                </tr>
                <?php foreach ($submissions as $submission) { ?>
                <tr>
                    <td><?=htmlspecialchars($submission['full_name'])?></td>
                    <td>
                        <a href="uploads/<?=htmlspecialchars($submission['file_path'])?>" target="_blank">
                            <?=htmlspecialchars($submission['file_path'])?>
                        </a>
                    </td>
                    <td>
                        <form action="app/add_review.php" method="post">
                            <input type="hidden" name="submission_id" value="<?=$submission['id']?>">
                            <input type="hidden" name="return_url" value="../view_submissions.php?task_id=<?=$task_id?>">
                            <textarea name="review" rows="2" style="width: 100%; margin-bottom: 5px;"><?=htmlspecialchars($submission['review'] ?? '')?></textarea>
                            <button type="submit" class="edit-btn">Save Review</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <?php } else { ?>
                <h3>No submissions found for this task.</h3>
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