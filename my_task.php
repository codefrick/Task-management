<?php 
session_start();

if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'Trainee') {
    include "DB_connection.php";
    include "app/Model/Task.php";

    $user_id = $_SESSION['id'];
    $tasks = [];
    $text = "All My Tasks";

    // ✅ Integrated Filtering Logic
    if (isset($_GET['due_date']) && $_GET['due_date'] == "Due Today") {
        $text = "Tasks Due Today";
        $tasks = get_my_tasks_due_today($conn, $user_id);
    } else if (isset($_GET['due_date']) && $_GET['due_date'] == "Overdue") {
        $text = "Overdue Tasks";
        $tasks = get_my_tasks_overdue($conn, $user_id);
    } else if (isset($_GET['due_date']) && $_GET['due_date'] == "No Deadline") {
        $text = "Tasks with No Deadline";
        $tasks = get_my_tasks_NoDeadline($conn, $user_id);
    } else if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $text = ucfirst(str_replace('_', ' ', $status)) . " Tasks";
        $tasks = get_my_tasks_by_status($conn, $user_id, $status);
    } else {
        $text = "All My Tasks";
        $tasks = get_all_tasks_by_id($conn, $user_id);
    }

    $num_task = is_array($tasks) ? count($tasks) : 0;

    // Fetch reviews for the tasks
    $sql = "SELECT task_id, review FROM task_submissions WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $reviews_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Tasks</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">

            <h4 class="title-2">
                <a href="my_task.php?due_date=Due Today">Due Today</a>
                <a href="my_task.php?due_date=Overdue">Overdue</a>
                <a href="my_task.php?due_date=No Deadline">No Deadline</a>
                <a href="my_task.php">All My Tasks</a>
            </h4>
            <h4 class="title-2"><?= htmlspecialchars($text) ?> (<?= $num_task ?>)</h4>
            
            <?php if (isset($_GET['success'])) { ?>
                <div class="success" role="alert"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php } ?>
            <?php if (isset($_GET['error'])) { ?>
                <div class="danger" role="alert"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php } ?>
            
            <?php if ($tasks != 0) { ?>
            <table class="main-table">
                <tr>
                    <th>Task Title</th>
                    <th>Task File</th>
                    <th>Status</th>
                    <th>Due Date</th>
                    <th>Admin Review</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($tasks as $task) { ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td>
                        <?php if (!empty($task['file_path'])): ?>
                            <a href="upload_admin_create_task/<?= htmlspecialchars($task['file_path']) ?>" target="_blank">
                                Download File
                            </a>
                        <?php else: ?>
                            <i>No file attached.</i>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($task['status']) ?></td>
                    <td>
                        <?php 
                            if ($task['due_date'] == "0000-00-00" || $task['due_date'] == NULL) {
                                echo "No Deadline";
                            } else {
                                echo htmlspecialchars($task['due_date']);
                            }
                        ?>
                    </td>
                    <td>
                        <?php 
                            if (isset($reviews_data[$task['id']]) && !empty($reviews_data[$task['id']])) {
                                echo htmlspecialchars($reviews_data[$task['id']]);
                            } else {
                                echo "<i>No review yet.</i>";
                            }
                        ?>
                    </td>
                    <td>
                        <form action="app/submit_task.php" method="post" enctype="multipart/form-data" style="display: inline-flex; align-items: center; margin-top: 5px;">
                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                            <input type="file" name="task_file" required style="margin-right: 10px;">
                            <button type="submit" class="edit-btn" style="background: #5cb85c;">Submit File</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <?php } else { ?>
                <h3 style="margin-top: 20px;">You have no tasks in this category.</h3>
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