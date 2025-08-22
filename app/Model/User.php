<?php 

function get_all_users($conn){
	$sql = "SELECT * FROM users WHERE role =? ";
	$stmt = $conn->prepare($sql);
	$stmt->execute(["Trainee"]);

	if($stmt->rowCount() > 0){
		$users = $stmt->fetchAll();
	}else $users = 0;

	return $users;
}


function insert_user($conn, $data){
	$sql = "INSERT INTO users (full_name, username, password, role) VALUES(?,?,?,?)";
	$stmt = $conn->prepare($sql);
	$stmt->execute($data);
}

function update_user($conn, $data){
	$sql = "UPDATE users SET full_name=?, username=?, password=?, role=? WHERE id=? AND role=?";
	$stmt = $conn->prepare($sql);
	$stmt->execute($data);
}

function delete_user($conn, $data){
	$sql = "DELETE FROM users WHERE id=? AND role=?";
	$stmt = $conn->prepare($sql);
	$stmt->execute($data);
}


function get_user_by_id($conn, $id){
	$sql = "SELECT * FROM users WHERE id =? ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	if($stmt->rowCount() > 0){
		$user = $stmt->fetch();
	}else $user = 0;

	return $user;
}
function update_profile($conn, $data){
	$sql = "UPDATE users SET full_name=?,  password=? WHERE id=? ";
	$stmt = $conn->prepare($sql);
	$stmt->execute($data);
}
function get_users_by_batch_id($conn, $batch_id){
	$sql = "SELECT * FROM users WHERE batch_id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$batch_id]);

	if($stmt->rowCount() > 0){
		return $stmt->fetchAll();
	} else {
        return 0;
    }
}

function count_users($conn){
	$sql = "SELECT id FROM users WHERE role='Trainee'";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	return $stmt->rowCount();
}

function get_unassigned_trainees($conn){
    $sql = "SELECT * FROM users WHERE role = 'Trainee' AND batch_id IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if($stmt->rowCount() > 0){
		return $stmt->fetchAll();
	} else {
        return 0;
    }
}

function add_trainee_to_batch($conn, $user_id, $batch_id){
    $sql = "UPDATE users SET batch_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$batch_id, $user_id]);
}

function remove_trainee_from_batch($conn, $user_id){
    $sql = "UPDATE users SET batch_id = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
}