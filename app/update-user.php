<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {

    // Add 'email' and 'role' to the check
    if (isset($_POST['id']) && isset($_POST['full_name']) && isset($_POST['user_name']) && isset($_POST['email']) && isset($_POST['role'])) {
        include "../DB_connection.php";

        function validate_input($data) {
          $data = trim($data);
          $data = stripslashes($data);
          $data = htmlspecialchars($data);
          return $data;
        }

        $id = validate_input($_POST['id']);
        $full_name = validate_input($_POST['full_name']);
        $user_name = validate_input($_POST['user_name']);
        $email = validate_input($_POST['email']);
        $role = validate_input($_POST['role']);
        $password = $_POST['password']; // Don't validate if it's empty

        if (empty($full_name)) {
            $em = "Full name is required";
            header("Location: ../edit-user.php?error=$em&id=$id");
            exit();
        } else if (empty($user_name)) {
            $em = "Username is required";
            header("Location: ../edit-user.php?error=$em&id=$id");
            exit();
        } else if (empty($email)) {
            $em = "Email is required";
            header("Location: ../edit-user.php?error=$em&id=$id");
            exit();
        } else if (empty($role)) {
            $em = "Role is required";
            header("Location: ../edit-user.php?error=$em&id=$id");
            exit();
        } else {
            
            try {
                // If the password field is left empty, DO NOT update the password.
                if (empty($password)) {
                    $sql = "UPDATE users SET full_name=?, username=?, email=?, role=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$full_name, $user_name, $email, $role, $id]);
                } else {
                    // If a new password is provided, hash it and update it.
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET full_name=?, username=?, email=?, password=?, role=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$full_name, $user_name, $email, $password, $role, $id]);
                }

                $sm = "User updated successfully";
                header("Location: ../edit-user.php?success=$sm&id=$id");
                exit();

            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    $em = "This username or email already exists.";
                } else {
                    $em = "An unknown database error occurred.";
                }
                header("Location: ../edit-user.php?error=$em&id=$id");
                exit();
            }
        }
    } else {
       $em = "All fields are required";
       header("Location: ../edit-user.php?error=$em&id=" . (isset($_POST['id']) ? $_POST['id'] : ''));
       exit();
    }

} else { 
   $em = "First login";
   header("Location: ../login.php?error=$em");
   exit();
}