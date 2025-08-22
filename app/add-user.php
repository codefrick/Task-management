<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {

    // Add 'batch_id' to the list of expected fields
    if (isset($_POST['full_name'], $_POST['user_name'], $_POST['password'], $_POST['email'], $_POST['role'], $_POST['batch_id'])) {
        include "../DB_connection.php";

        function validate_input($data) {
          $data = trim($data);
          $data = stripslashes($data);
          $data = htmlspecialchars($data);
          return $data;
        }

        $full_name = validate_input($_POST['full_name']);
        $user_name = validate_input($_POST['user_name']);
        $email = validate_input($_POST['email']);
        $password = validate_input($_POST['password']);
        $role = validate_input($_POST['role']);
        $batch_id = $_POST['batch_id'] == 0 ? null : validate_input($_POST['batch_id']); // Handle 'No Batch' option

        if (empty($full_name)) {
            $em = "Full name is required";
            header("Location: ../add-user.php?error=$em");
            exit();
        } else if (empty($user_name)) {
            $em = "Username is required";
            header("Location: ../add-user.php?error=$em");
            exit();
        } else if (empty($email)) {
            $em = "Email is required";
            header("Location: ../add-user.php?error=$em");
            exit();
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $em = "Invalid email format";
            header("Location: ../add-user.php?error=$em");
            exit();
        } else if (empty($password)) {
            $em = "Password is required";
            header("Location: ../add-user.php?error=$em");
            exit();
        } else {
           // Securely hash the password
           $password = password_hash($password, PASSWORD_DEFAULT);

           // Add batch_id to the data array
           $data = array($full_name, $user_name, $email, $password, $role, $batch_id);
           
            try {
                // Update SQL to include batch_id
                $sql = "INSERT INTO users (full_name, username, email, password, role, batch_id) VALUES(?,?,?,?,?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute($data);

                $sm = "User created successfully";
                header("Location: ../add-user.php?success=$sm");
                exit();
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    $em = "This username or email already exists.";
                } else {
                    $em = "An unknown database error occurred.";
                }
                header("Location: ../add-user.php?error=$em");
                exit();
            }
        }
    } else {
       $em = "All fields are required";
       header("Location: ../add-user.php?error=$em");
       exit();
    }

} else { 
   $em = "First login";
   header("Location: ../login.php?error=$em");
   exit();
}